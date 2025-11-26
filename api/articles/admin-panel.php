<?php 
    //admin panel logic for articles

    require_once dirname(__DIR__,2) . '/core/init.php';

    //if not logged in
    notLogged();

    //check if admin
    try {
        $db_user = $pdo->prepare('SELECT rank FROM users WHERE id = :id_user');
        $db_user->execute([
            'id_user' => $_SESSION['user']['id']
        ]);

        //get rank info
        $user_rank = $db_user->fetchColumn();

        //security
        onlyAdmin('html', $user_rank);    

    } catch(PDOException $e) {
        debug($e->getMessage());
    }
?>