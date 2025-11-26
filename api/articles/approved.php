<?php
    //moderated articles php logic

    require_once dirname(__DIR__, 2) . '/core/init.php';

    //answer json
    header('Content-Type: application/json; charset=utf-8');

    wrongMethod('GET', 'json'); //check if get method

    //main part
    try {
        //pagination var
        $page = (int)isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if($page < 1 || !is_int(($page))) $page = 1;

        //filter var
        $ctgry = (int)isset($_GET['ctgry']) ? (int)$_GET['ctgry'] : 0;

        //check how many categories in db
        $db_a_catgries = $pdo->prepare("SELECT COUNT(*) FROM a_catgries");
        $db_a_catgries->execute();

        $db_catgries_count = (int)$db_a_catgries->fetchColumn();

        //check that filter in size
        if($ctgry < 0 || !is_int($ctgry) || $ctgry > $db_catgries_count) $ctgry = 0;

        //limits on page for artcls render
        $maxOnPage = 5;
        $maxOnPageLimit = 50;
        if($maxOnPage > $maxOnPageLimit) $maxOnPage = 5;

        //"start from" point
        $offset = ($page - 1) * $maxOnPage;

        //how many articles depends on category filter
        //1 - all articles
        if($ctgry === 0) {
            $db_a_ctgry = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE article_status = 'approved'");
        } else {
            $db_a_ctgry = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE article_status = 'approved' AND ctgry = :ctgry");
            $db_a_ctgry->bindValue(':ctgry', $ctgry, PDO::PARAM_INT);
        }
        $db_a_ctgry->execute();

        $db_ctgry_count = (int)$db_a_ctgry->fetchColumn();

        //count how many pages
        $totalPages = (int) ceil($db_ctgry_count / $maxOnPage);

        //take articles for render depends on category and offset
        //1 - all articles
        if($ctgry === 0) {
            $db_articles = $pdo->prepare("SELECT * FROM articles 
                                                        WHERE article_status = 'approved'
                                                        ORDER BY added_at DESC
                                                        LIMIT :maxOnPage OFFSET :offset     
                                                    ");
        } else {
            $db_articles = $pdo->prepare("SELECT * FROM articles 
                                                        WHERE article_status = 'approved' AND ctgry = :ctgry
                                                        ORDER BY added_at DESC
                                                        LIMIT :maxOnPage OFFSET :offset     
                                                    ");
            $db_articles->bindValue(':ctgry', (int)$ctgry, PDO::PARAM_INT);
        }
        $db_articles->bindValue(':maxOnPage', (int)$maxOnPage, PDO::PARAM_INT);
        $db_articles->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $db_articles->execute();

        //get array of articles
        $articles = $db_articles->fetchAll(PDO::FETCH_ASSOC);

        //clean text to avoid xss
        $articles = removeXSSArticles($articles);

        //change id ctgry => name
        $articles = idCtgryToName($pdo, $articles);

        //change id author => nick
        $articles = setNicksAuthor($pdo, $articles);

        //json answer
        echo json_encode([
            'status' => 'ok',
            'currentPage' => $page,
            'ctgry' => $ctgry,
            'totalPages' => $totalPages,
            'articles' => $articles
        ]);
    } catch(PDOException $e) {
        debug($e->getMessage());
        json_error("Problem with db", 500);
    } catch(Exception $e) {
        debug($e->getMessage());
        json_error("Problem with server", 500);
    }
?>