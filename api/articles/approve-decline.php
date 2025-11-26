<?php 
    //approve or decline article logic

    //json answer
    header('Content-Type: application/json;charset=utf-8');

    require_once dirname(__DIR__,2) . '/core/init.php';

    wrongMethod('POST', 'json'); //if not post

    notLogged('json'); //if not logged

    try {
        //get from js decision and articleId
        $js_get = json_decode(file_get_contents("php://input"), true);
        if(!isset($js_get) || !isset($js_get['decision']) || !isset($js_get['articleId'])) { //if no have json
            json_error('no have vars', 200);
        }

        //check if moderator isn't owner of article
        $check_author_db = $pdo->prepare("SELECT 1 FROM articles WHERE id = :id_article AND id_author = :id_user");
        $check_author_db->bindValue(':id_article', (int)$js_get['articleId'], PDO::PARAM_INT);
        $check_author_db->bindValue(':id_user', (int)$_SESSION['user']['id'], PDO::PARAM_INT);
        $check_author_db->execute();

        if($check_author_db->fetchColumn()) {
            json_error("You can't appreciate your own article", 200);
        }

        //check if have moderation record
        $check_record = $pdo->prepare("SELECT 1 FROM moderation_decisions WHERE id_article = :id_article AND id_user = :id_user");
        $check_record->execute([
            'id_article' => $js_get['articleId'],
            'id_user' => $_SESSION['user']['id']
        ]);

        if($check_record->fetchColumn()) {
            json_error('You had made moderation already', 200);
        }

        //work with db
        $pdo->beginTransaction();

        //globals for change status
        $approvedPoint = 10;
        $declinedPoint = -5;
        
        //check active rank of user
        $db_rank = $pdo->prepare("SELECT rank FROM users WHERE id = :id_user");
        $db_rank->execute([
            'id_user' => $_SESSION['user']['id']
        ]);
        $u_rank = $db_rank->fetchColumn(); 

        //check "value" of rank's decision
        $db_dec_value = $pdo->prepare("SELECT approved_power FROM ranks WHERE id = :u_rank");
        $db_dec_value->bindValue(':u_rank', (int)$u_rank, PDO::PARAM_INT);
        $db_dec_value->execute();

        $dec_value = $db_dec_value->fetchColumn();
        $u_decision = 0;
        if($js_get['decision'] === 'approve') $u_decision = $dec_value;
        if($js_get['decision'] === 'decline') $u_decision = -$dec_value;

        //create moderation decision record
        $add_mod_decision = $pdo->prepare('INSERT INTO moderation_decisions (id_article, id_user, decision, decision_time)
                                                VALUES (:id_article, :id_user, :decision, NOW())');
        $add_mod_decision->execute([
            'id_article' => $js_get['articleId'],
            'id_user' => $_SESSION['user']['id'],
            'decision' => $u_decision
        ]);

        //take sum of all article decision and check if change its status(approved,canceled)

        //check db
        $db_a_count_dec = $pdo->prepare('SELECT SUM(decision) FROM moderation_decisions WHERE id_article = :id_article');
        $db_a_count_dec->execute([
            'id_article' => $js_get['articleId']
        ]);
        
        //get summary of article decision
        $a_sum_dec = $db_a_count_dec->fetchColumn();

        if($a_sum_dec <= $declinedPoint) { //declined
            //change article
            $db_a_status = $pdo->prepare("UPDATE articles SET article_status = 'declined' WHERE id = :id_article");
            $db_a_status->execute([
                'id_article' => $js_get['articleId']
            ]);
        }

        //check if change articles' status
        if($a_sum_dec >= $approvedPoint) { //approved
            //change article
            $db_a_status = $pdo->prepare("UPDATE articles SET article_status = 'approved' WHERE id = :id_article");
            $db_a_status->execute([
                'id_article' => $js_get['articleId']
            ]);
        }

        //get votes
        $db_votes = $pdo->prepare("SELECT 
                                            SUM(CASE WHEN decision < 0 THEN decision ELSE 0 END) as decl,
                                            SUM(CASE WHEN decision > 0 THEN decision ELSE 0 END) as appr 
                                            FROM moderation_decisions WHERE id_article = :id_article");
        $db_votes->bindValue(':id_article', (int)$js_get['articleId'], PDO::PARAM_INT);
        $db_votes->execute();

        //get array of votes
        $votes = $db_votes->fetch(PDO::FETCH_ASSOC);

        //add new vars for article
        $appr = $votes['appr'];
        $decl = $votes['decl'];

        //close transaction
        $pdo->commit();

        //send back answer
        $js_answer = [
            'status' => 'ok',
            'msg' => 'Your decision is done',
            'appr' => $appr,
            'decl' => $decl,
            'sum' => $a_sum_dec,
        ];

        echo json_encode($js_answer);
        exit;
    } catch(PDOException $e) {
        //reset sql
        if($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        debug($e->getMessage());
        json_error('Problem due to working with database', 500);
    }  catch(Exception $e) {
        debug($e->getMessage());
        json_error("Problem with server", 500);
    }

?>