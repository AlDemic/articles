<?php 
    //php logic for delete comment

    require_once dirname(__DIR__, 2) . '/core/init.php';

    wrongMethod('POST', 'json'); //check method

    notLogged('json'); //authorization check(additional safety)

    try {
        //get json
        $jsonData = json_decode(file_get_contents('php://input'), true);
        $idCom = filter_var($jsonData, FILTER_VALIDATE_INT); //get int
        if($idCom <= 0 || $idCom === false) json_error("Wrong number of comment", 200);

        //get id of user from session
        $id_user = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        if($id_user <= 0) json_error("Your id is wrong", 200);

        //work with db

        //check if user can delete(rank > 2)
        $db_rank_check = $pdo->prepare("SELECT 1 FROM users WHERE id = :id_user AND rank > 2");
        $db_rank_check->bindValue(':id_user', (int)$id_user, PDO::PARAM_INT);
        $db_rank_check->execute();

        //get bool
        $rank_check = (bool)$db_rank_check->fetchColumn();

        //if not admin
        if(!$rank_check) json_error("You don't have enough rights to delete comment", 200);

        //delete comment if all is ok
        $db_com_del = $pdo->prepare("DELETE FROM a_comments WHERE id = :idCom");
        $db_com_del->bindValue(':idCom', (int)$idCom, PDO::PARAM_INT);
        $db_com_del->execute();

        //send answer
        json_ok("Comment is deleted");
    }  catch(PDOException $e) {
        debug($e->getMessage());
        json_error("Problem with db", 500);
    } catch(Exception $e) {
        debug($e->getMessage());
        json_error("Problem with server", 500);
    }


?>