<?php 
    //php logic for article comments

    require_once dirname(__DIR__, 2) . '/core/init.php';

    wrongMethod('POST', 'json'); //check method

    //logic part
    try {
        //get vars from POST
        $jsonData = json_decode(file_get_contents('php://input'), true);

        //check json
        if(!isset($jsonData['idA']) || !isset($jsonData['page'])) json_error("no have vars", 200);

        //id of article
        $idA = (int)$jsonData['idA'];
        if($idA < 0 || !is_int($idA)) $idA = 0;

        //user id in session
        $id_user = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
        if(!is_int($id_user) || $id_user <= 0) $id_user = 0;

        //page of comments
        $page = (int)$jsonData['page'];
        if($page <= 0 || !is_int($page)) $page = 1;

        //global params how many comments on page
        $maxOnPage = 5;
        $maxOnPageLimit = 15;
        if($maxOnPage > $maxOnPageLimit) $maxOnPage = 5;

        //from where "start" point to take comments
        $offset = ($page - 1) * $maxOnPage;

        //work with db
        //check how many comments of this article
        $count_all_com = $pdo->prepare("SELECT COUNT(*) FROM a_comments WHERE id_article = :idA");
        $count_all_com->bindValue(':idA', (int)$idA, PDO::PARAM_INT);
        $count_all_com->execute();

        //get n of all comments
        $c_all_com = $count_all_com->fetchColumn();

        //get how many pages of comments
        $totalPages = (int) ceil($c_all_com / $maxOnPage);

        //get all comments depends on OFFSET
        $db_comments = $pdo->prepare("SELECT * FROM a_comments WHERE id_article = :idA
                                                                ORDER BY added_at DESC
                                                                LIMIT :maxOnPage OFFSET :offset
                                                                ");
        $db_comments->bindValue(':idA', (int)$idA, PDO::PARAM_INT);
        $db_comments->bindValue(':maxOnPage', (int)$maxOnPage, PDO::PARAM_INT);
        $db_comments->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $db_comments->execute();

        //get comments array
        $comments = $db_comments->fetchAll(PDO::FETCH_ASSOC);

        //check user rank
        $db_user_rank = $pdo->prepare("SELECT 1 FROM users WHERE id = :id_user AND rank > 2");
        $db_user_rank->bindValue(':id_user', (int)$id_user, PDO::PARAM_INT);
        $db_user_rank->execute();

        $user_rank = (bool)$db_user_rank->fetchColumn();

        //create var in array for admin actions. If rank > 2 => true(can delete)
        foreach($comments as &$comment) {
            $comment['isAdm'] = $user_rank;
        }
        
        //change id users => nick (func from additional.php)
        $comments = setNicksCom($pdo, $comments);

        //clean from xss (func from additional.php)
        $comments = removeXSSComments($comments); //clean comments

        //send json
        echo json_encode([
            'status' => 'ok',
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'idA' => $idA,
            'comments' => $comments,
        ]);
    } catch(PDOException $e) {
        debug($e->getMessage());
        json_error("Problem with db", 500);
    } catch(Exception $e) {
        debug($e->getMessage());
        json_error("Problem with server", 500);
    }

?>