<?php 
    //articles moderation page logic

    require_once dirname(__DIR__, 2) . '/core/init.php';

    //json answer
    header('Content-Type: application/json; charset=utf-8');

    wrongMethod('GET', 'json'); //check if get method

    notLogged('json'); //safety

    //main part
    try {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1; 
        if($page < 1 || !is_int($page)) $page = 1;

        $filter = isset($_GET['filter']) ? (string) $_GET['filter'] : 'all';
        //filters array
        $filters = [
            'all',
            'canMod',
            'doneMod',
            'declined'
        ];
        if(!in_array($filter, $filters)) $filter = 'all'; //if no existed filter was sended

        $maxOnPage = 5; //how many articles on page
        $maxOnPageLimit = 50;
        if($maxOnPage > $maxOnPageLimit) $maxOnPage = 5;

        //"start from" point
        $offset = ($page - 1) * $maxOnPage;

        //get articles and make array depends on filter
        $filter_a = ($filter === 'all' || $filter === 'canMod' || $filter === 'doneMod') ? 'moderation' : 'declined';

        //how many articles have depends on filter
        $all_articles = 0; //counter

        switch($filter) {
            case 'all':
                $count_all_articles = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE article_status = :filter_a");
                break;
            case 'canMod':
                $count_all_articles = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE article_status = :filter_a
                                                    AND NOT EXISTS(
                                                        SELECT 1 FROM moderation_decisions WHERE id_article = articles.id AND id_user = :id_user     
                                                    )
                                                    ");
                $count_all_articles->bindValue(':id_user', (int)$_SESSION['user']['id'], PDO::PARAM_INT);
                break; 
            case 'doneMod':
                $count_all_articles = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE article_status = :filter_a
                                                    AND EXISTS(
                                                        SELECT 1 FROM moderation_decisions WHERE id_article = articles.id AND id_user = :id_user     
                                                    )
                                                    ");
                $count_all_articles->bindValue(':id_user', (int)$_SESSION['user']['id'], PDO::PARAM_INT);
                break; 
            case 'declined':
                $count_all_articles = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE article_status = :filter_a");   
                break;
        }

        $count_all_articles ->bindValue(':filter_a', (string)$filter_a, PDO::PARAM_STR);
        $count_all_articles->execute(); 

        $all_articles = $count_all_articles->fetchColumn();
        
        //count how many pages
        $totalPages = (int) ceil($all_articles / $maxOnPage);

        //take articles from db for render
        switch($filter) {
            case 'all':
                $db_articles = $pdo->prepare("SELECT * FROM articles
                                                    WHERE article_status = :filter_a
                                                    ORDER BY added_at DESC
                                                    LIMIT :maxOnPage OFFSET :offset
                                                    ");
                break;
            case 'canMod':
                $db_articles = $pdo->prepare("SELECT * FROM articles
                                                        WHERE 
                                                        NOT EXISTS(
                                                            SELECT 1 FROM moderation_decisions WHERE id_article = articles.id AND id_user = :id_user
                                                        )
                                                        AND article_status = :filter_a
                                                        AND id_author != :id_user
                                                        ORDER BY added_at DESC
                                                        LIMIT :maxOnPage OFFSET :offset
                                                        ");
                $db_articles->bindValue(':id_user', (int)$_SESSION['user']['id'], PDO::PARAM_INT);
                break;
            case 'doneMod':
                $db_articles = $pdo->prepare("SELECT * FROM articles
                                                        WHERE 
                                                        EXISTS(
                                                            SELECT 1 FROM moderation_decisions WHERE id_article = articles.id AND id_user = :id_user
                                                        )
                                                        AND article_status = :filter_a
                                                        ORDER BY added_at DESC
                                                        LIMIT :maxOnPage OFFSET :offset
                                                        ");
                $db_articles->bindValue(':id_user', (int)$_SESSION['user']['id'], PDO::PARAM_INT);
                break;
            case 'declined':
                $db_articles = $pdo->prepare("SELECT * FROM articles
                                                    WHERE article_status = :filter_a
                                                    ORDER BY added_at DESC
                                                    LIMIT :maxOnPage OFFSET :offset
                                                    ");
                break;
        }

        //make as int
        $db_articles->bindValue(':filter_a', (string)$filter_a, PDO::PARAM_STR);
        $db_articles->bindValue(':maxOnPage', (int)$maxOnPage, PDO::PARAM_INT);
        $db_articles->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $db_articles->execute();

        //get array of articles from db
        $articles = $db_articles->fetchAll(PDO::FETCH_ASSOC);


        //clean text to avoid xss
        foreach($articles as &$article) {
            $article['title'] = strip_tags($article['title']);
            $article['short_desc'] = strip_tags($article['short_desc'], '<p><b><strong><em><i><ul><li><a>');
            $article['full_desc'] = strip_tags($article['full_desc'], '<p><b><strong><em><i><ul><li><a>');
        }

        //make field isModerated (check if this admin made "approved/canceled")
        $articles = isModerated($pdo, $filter, $articles);

        //count votes statistic for render
        $articles = votesCount($pdo, $articles);

        //change category from id -> name
        $articles = ctgryName($pdo, $articles);
        
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

    //for moderation render btns
    function isModerated($pdo, $filter, $articles) {
        foreach($articles as &$article) {
                if($filter === 'all' || $filter === 'canMod' ) {
                    $isApproved = $pdo->prepare("SELECT 1 FROM articles WHERE id = :id_article                                                                       
                                                                        AND NOT EXISTS(
                                                                            SELECT 1 FROM moderation_decisions WHERE id_article = :id_article AND id_user = :id_user
                                                                        )
                                                                        AND id_author != :id_user
                                                                        ");
                    $isApproved->bindValue(':id_article', (int)$article['id'], PDO::PARAM_INT);
                    $isApproved->bindValue(':id_user', (int)$_SESSION['user']['id'], PDO::PARAM_INT);
                    $isApproved->execute();

                    $article['isModerated'] = (bool)!$isApproved->fetchColumn();  //here we make reverse. Bcs true = no render / false = render
                } else {
                    $article['isModerated'] = true; //to not render btns
                }
        }
        unset($article);

        return $articles;
    }

    //for render votes total result 
    function votesCount($pdo, $articles) {
        foreach($articles as &$article) {           
            //db
            $db_votes = $pdo->prepare("SELECT
                                            SUM(CASE WHEN decision < 0 THEN decision ELSE 0 END) as decl,
                                            SUM(CASE WHEN decision > 0 THEN decision ELSE 0 END) as appr 
                                            FROM moderation_decisions WHERE id_article = :id_article");
            $db_votes->bindValue(':id_article', (int)$article['id'], PDO::PARAM_INT);
            $db_votes->execute();

            //get array of votes
            $votes = $db_votes->fetch(PDO::FETCH_ASSOC);

            //add new vars for article
            $article['appr'] = $votes['appr'] ?? 0;
            $article['decl'] = $votes['decl'] ?? 0;
            $article['sum'] = $votes['appr'] + $votes['decl'];

        }
        unset($article);

        return $articles;
    }

    //change id ctgry => name
    function ctgryName($pdo, $articles) {
        //take all array of categories
        $db_catgries_name = $pdo->prepare("SELECT * FROM a_catgries");
        $db_catgries_name->execute();
        $catgries_name = $db_catgries_name->fetchAll(PDO::FETCH_ASSOC);

        //change id to name in all articles
        foreach($articles as &$article) {
            $id_ctgry = (int)$article['ctgry'];
            $article['ctgry'] = $catgries_name[$id_ctgry - 1]['name']; //-1 bcs in array count from 0 => 0 -> [id, name]
        }
        unset($article);

        return $articles;
    }

?>