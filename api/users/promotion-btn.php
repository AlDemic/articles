<?php 
    //php logic of promotion button

    require_once dirname(__DIR__,2) . '/core/init.php';

    header('Content-Type: application/json;charset=utf-8'); //return json

    //if wrong method
    wrongMethod('POST', 'json');

    //if not logged in
    notLogged('json');

    try {
        //get id from session
        $user_id = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        if($user_id <= 0) return json_error("Wrong user id", 200);


        //get user rank and artls approved from db
        $sql_req = "SELECT u.rank as user_rank,
                            COUNT(CASE WHEN a.article_status = 'approved' THEN 1 END) as appr
                    FROM users u
                    LEFT JOIN articles a ON a.id_author = u.id
                    WHERE u.id = ?
                    GROUP BY u.id
                    ";
        $db_u_rank = $pdo->prepare($sql_req);
        $db_u_rank->bindValue(1, (int)$user_id, PDO::PARAM_INT);
        $db_u_rank->execute();

        //user rank and appr as array
        $u_rank = $db_u_rank->fetch();

        //check if user can be promoted(function in additional.php)
        $prom_rank = checkPromotion($u_rank['user_rank'], $u_rank['appr']); //false = no promotion, otherwise number of rank(2,3)

        //if number of rank(not false)
        if($prom_rank) {
            //update user rank
            $db_upd_rank = $pdo->prepare("UPDATE users SET rank = ? WHERE id = ?");
            $db_upd_rank->bindValue(1, (int)$prom_rank, PDO::PARAM_INT);
            $db_upd_rank->bindValue(2, (int)$user_id, PDO::PARAM_INT);
            $db_upd_rank->execute();
        }

        //update session
        $_SESSION['user']['rank'] = $prom_rank; //rank id
        $_SESSION['user']['rank_name'] = setNameRank($pdo, (int)$prom_rank); //rank name

        //send json answer
        if($prom_rank) {
            json_ok("Promotion is successed!");
        } else {
            json_error("Something wrong with your promotion..", 200);
        }
    } catch(PDOException $e) {
        debug($e->getMessage());
        json_error("Problem with db", 500);
    } catch(Exception $e) {
        debug($e->getMessage());
        json_error("Problem with server", 500);
    }
?>