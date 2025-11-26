<?php 
    //Full article page php logic

    require_once dirname(__DIR__, 2) . '/core/init.php';

    //answer json
    header('Content-Type: application/json; charset=utf-8');

    wrongMethod('GET', 'json'); //check if get method

    try {
        //pagination var
        $page = (int)isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if($page < 1 || !is_int(($page))) $page = 1;

        //article id
        $idA = (int)isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if($idA < 0 || !is_int(($idA))) $idA = 0;

        //work with db

        //get article
        $db_article = $pdo->prepare("SELECT * FROM articles WHERE id = :id_article");
        $db_article->bindValue(':id_article', (int)$idA, PDO::PARAM_INT);
        $db_article->execute();

        //get array
        $article = $db_article->fetch(PDO::FETCH_ASSOC);

        //clean from xss (func from additional.php)
        $article = removeXSSArticles($article); //clean article

        //change id ctgry => name (func from additional.php)
        $article = idCtgryToName($pdo, $article);

        //change user id to his nickname (func from additional.php)
        $article = setNicksAuthor($pdo, $article);

        //json answer
        echo json_encode([
            'status' => 'ok',
            'currentPage' => $page,
            'idA' => $idA,
            'article' => $article,
        ]);
    } catch(PDOException $e) {
        debug($e->getMessage());
        json_error("Problem with db", 500);
    } catch(Exception $e) {
        debug($e->getMessage());
        json_error("Problem with server", 500);
    }

?>
