<?php
    //searching php logic

    require_once dirname(__DIR__, 1) . '/core/init.php';

    //answer json
    header('Content-Type: application/json; charset=utf-8');

    wrongMethod('GET', 'json'); //check if get method

    //main part
    try {
        //pagination var
        $page = (int)isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if($page < 1 || !is_int(($page))) $page = 1;

        //filter var
        $filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';
        if($filter === '' || (mb_strlen($filter) < 2)) json_error("Wrong search length", 200);

        $maxOnPage = 5;
        $maxOnPageLimit = 50;
        if($maxOnPage > $maxOnPageLimit) $maxOnPage = 5;

        //"start from" point
        $offset = ($page - 1) * $maxOnPage;


        //count how many artls with filter
        $db_count_all = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE (title LIKE ? OR short_desc LIKE ? OR full_desc LIKE ?) AND article_status = 'approved'");
        $db_count_all->execute(["%$filter%", "%$filter%", "%$filter%"]);
        //get number
        $count_all = $db_count_all->fetchColumn();

        //get total pages
        $totalPages = (int) ceil($count_all / $maxOnPage);

        //get articles depends on filter and page
        $db_articles = $pdo->prepare("SELECT * FROM articles 
                                                        WHERE (title LIKE ? OR short_desc LIKE ? OR full_desc LIKE ?) AND article_status = 'approved'
                                                        ORDER BY added_at DESC
                                                        LIMIT ? OFFSET ?    
                                                    ");
        $filter = "%$filter%";                                            
        $db_articles->bindValue(1, $filter, PDO::PARAM_STR);
        $db_articles->bindValue(2, $filter, PDO::PARAM_STR);
        $db_articles->bindValue(3, $filter, PDO::PARAM_STR);
        $db_articles->bindValue(4, (int)$maxOnPage, PDO::PARAM_INT);
        $db_articles->bindValue(5, (int)$offset, PDO::PARAM_INT);
        $db_articles->execute();

        //get array:
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
            'filter' => $filter,
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