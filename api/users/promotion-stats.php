<?php 
    //php logic of promotion model

    require_once dirname(__DIR__,2) . '/core/init.php';

    header('Content-Type: application/json;charset=utf-8');

    //if wrong method
    wrongMethod('POST', 'json');

    //if not logged in
    notLogged('json');

    try {
        //get user id
        $id_user = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        if($id_user <= 0) json_error("Wrong user");

        $id_rank = isset($_SESSION['user']['rank']) ? (int)$_SESSION['user']['rank'] : 0;
        if($id_rank <= 0) json_error("Wrong rank");

        //collect user's statistic of articles
        $db_user_stat = $pdo->prepare("SELECT
                                         COUNT(CASE WHEN article_status = 'approved' THEN 1 END) as appr, 
                                         COUNT(CASE WHEN article_status = 'declined' THEN 1 END) as decl, 
                                         COUNT(CASE WHEN article_status = 'moderation' THEN 1 END) as onMod 
                                         FROM articles
                                         WHERE id_author = ?
                                         ");
        $db_user_stat->bindValue(1, (int)$id_user, PDO::PARAM_INT);
        $db_user_stat->execute();

        //get result as array
        $user_stat = $db_user_stat->fetch(PDO::FETCH_ASSOC);

        //check if can be promoted
        //globals
        $mod_req = 3;
        $adm_req = 5;
        $msg = "Promotion isn't available";
        $isProm = (bool)false;

        if(((int)$user_stat['appr'] >= $mod_req && $id_rank < 2) || ((int)$user_stat['appr'] >= $adm_req && $id_rank < 3)) {
            $msg = "Can be promoted";
            $isProm = true;
        }

        //send answer
        echo json_encode([
            'status' => 'ok',
            'msg' => $msg,
            'appr' => $user_stat['appr'],
            'decl' => $user_stat['decl'],
            'mod' => $user_stat['onMod'],
            'isProm' => $isProm
        ]);
    } catch(PDOException $e) {
        debug($e->getMessage());
        json_error("Problem with db", 500);
    } catch(Exception $e) {
        debug($e->getMessage());
        json_error("Problem with server", 500);
    }
?>