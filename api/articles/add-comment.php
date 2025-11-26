<?php 
    //php logic for add comment

    require_once dirname(__DIR__, 2) . '/core/init.php';

    wrongMethod('POST', 'json'); //check method

    notLogged('json'); //authorization check(additional safety)

    //main logic
    try {
        //get json
        $jsonData = json_decode(file_get_contents("php://input"), true);
        if(!isset($jsonData['comment']) || !isset($jsonData['idA']) || !isset($_SESSION['user']['id'])) json_error("no have vars", 200);

        //id article
        $idA = (int)$jsonData['idA'];
        if($idA <= 0 || !is_int($idA)) json_error("Wrong id of article", 200);

        //id of user
        $id_user = (int)$_SESSION['user']['id'];
        if($id_user <= 0 || !is_int($id_user)) json_error("Wrong id of user", 200);

        //comment + check length
        $msg = trim($jsonData['comment']);
        if(mb_strlen($msg) < 3 || mb_strlen($msg) > 512) json_error("Incorrect comment length", 200);

        //check for duplicate comment
        $db_duplicate_msg = $pdo->prepare("SELECT 1 FROM a_comments WHERE id_article = :idA AND id_user = :id_user AND msg = :msg");
        $db_duplicate_msg->bindValue(':idA', (int)$idA, PDO::PARAM_INT);
        $db_duplicate_msg->bindValue(':id_user', (int)$id_user, PDO::PARAM_INT);
        $db_duplicate_msg->bindValue(':msg', $msg, PDO::PARAM_STR);
        $db_duplicate_msg->execute();

        if($db_duplicate_msg->fetchColumn()) json_error("You're trying to add same comment", 200);

        //get time of last article's comment by user and check time to avoid spam
        $c_interval = 15; //user can write once per 15sec;
        $time_now = time(); //time now in sec

        //get time of last user's comment in this article
        $db_last_com_time = $pdo->prepare("SELECT added_at FROM a_comments WHERE id_article = :idA AND id_user = :id_user
                                                                            ORDER BY added_at DESC
                                                                            LIMIT 1
                                                                            ");
        $db_last_com_time->bindValue(':idA', (int)$idA, PDO::PARAM_INT);
        $db_last_com_time->bindValue(':id_user', (int)$id_user, PDO::PARAM_INT);
        $db_last_com_time->execute();


        $u_last_com_time = $db_last_com_time->fetchColumn(); //get user's last com time
        //if false
        $last_com_time = $u_last_com_time ? strtotime($u_last_com_time) : 0; //avoid false

        //check to avoid spam
        if(($time_now - $last_com_time) < $c_interval) json_error("You can't write comment. Only once per 15 sec!", 200);

        //if all is ok -> add to db
        $db_add_msg = $pdo->prepare("INSERT INTO a_comments (id_article, id_user, msg, added_at) VALUES (:idA, :id_user, :msg, NOW())");
        $db_add_msg->execute([
            'idA' => (int)$idA,
            'id_user' => (int)$id_user,
            'msg' => $msg
        ]);

        //send json answer
        json_ok("Comment is added!");
    } catch(PDOException $e) {
        debug($e->getMessage());
        json_error("Problem with db", 500);
    } catch(Exception $e) {
        debug($e->getMessage());
        json_error("Problem with server", 500);
    }

?>