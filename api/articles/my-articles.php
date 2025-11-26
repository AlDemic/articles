<?php 
    //php logic for article comments

    require_once dirname(__DIR__, 2) . '/core/init.php';

    wrongMethod('POST', 'json'); //check method

    notLogged(); //authorization check(additional safety)

    //logic part
    try {
        //get json
        $jsonData = json_decode(file_get_contents('php://input'), true);
        $page = filter_var($jsonData, FILTER_VALIDATE_INT); //get int
        if($page <= 0 || $page === false) $page = 1;

        //user id in session
        $id_user = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
        if(!is_int($id_user) || $id_user <= 0) $id_user = 0;

        //global params how many comments on page
        $maxOnPage = 5;
        $maxOnPageLimit = 15;
        if($maxOnPage > $maxOnPageLimit) $maxOnPage = 5;

        //from where "start" point to take comments
        $offset = ($page - 1) * $maxOnPage;

        //work with db
        //check how many articles user has
        $count_all_a = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE id_author = :id_user");
        $count_all_a->bindValue(':id_user', (int)$id_user, PDO::PARAM_INT);
        $count_all_a->execute();

        //get n of all articles
        $count_a = $count_all_a->fetchColumn();

        //get how many pages of comments
        $totalPages = (int) ceil($count_a / $maxOnPage);

        //get all articles depends on OFFSET
        $db_articles = $pdo->prepare("SELECT * FROM articles WHERE id_author = :id_user
                                                                ORDER BY added_at DESC
                                                                LIMIT :maxOnPage OFFSET :offset
                                                                ");
        $db_articles->bindValue(':id_user', (int)$id_user, PDO::PARAM_INT);
        $db_articles->bindValue(':maxOnPage', (int)$maxOnPage, PDO::PARAM_INT);
        $db_articles->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $db_articles->execute();

        //get comments array
        $articles = $db_articles->fetchAll(PDO::FETCH_ASSOC);

        //clean from xss (func from additional.php)
        $articles = removeXSSArticles($articles); //clean comments
        
        //change categories id to name
        $articles = idCtgryToName($pdo, $articles);

        //send json
        echo json_encode([
            'status' => 'ok',
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'articles' => $articles,
        ]);
    } catch(PDOException $e) {
        debug($e->getMessage());
        json_error("Problem with db", 500);
    } catch(Exception $e) {
        debug($e->getMessage());
        json_error("Problem with server", 500);
    }

?>