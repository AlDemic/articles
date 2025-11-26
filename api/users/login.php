<?php
    //login in logic

    require_once dirname(__DIR__, 2) . "/core/init.php";

    //check if POST
    wrongMethod(); //standard: method: post / type: html

    //if logged in
    loggedInAlready();

    //get globals
    //email
    $u_email = trim($_POST['email']) ?? '';
    $u_email = filter_var($u_email, FILTER_SANITIZE_EMAIL);
    $u_email = filter_var($u_email, FILTER_VALIDATE_EMAIL);

    //pass
    $u_pass = $_POST['pass'];

    //check if correct
    try {
        //check if user's email exist
        $db_u_email = $pdo->prepare("SELECT * FROM users WHERE email = :u_email");
        $db_u_email->execute([
            'u_email' => $u_email
        ]);

        //get user from db by email
        $db_user = $db_u_email->fetch(PDO::FETCH_ASSOC);

        //if not exist
        if(!$db_user) {
            html_error("User with this email isn't exist.");
        }

        //if wrong password
        if(!password_verify($u_pass, $db_user['pass'])) {
            html_error("Password is wrong!");
        }

        //change id rank to rank's name
        $u_rank_name = setNameRank($pdo, (int)$db_user['rank']);

        //create a session for 7 days
        session_regenerate_id();

        //get info of session
        $new_sess = session_get_cookie_params();

        //make long life cookie
        setcookie(
            session_name(),
            session_id(),
            [
                'expires' => time() + 7*24*60*60,
                'path' => $new_sess['path'],
                'domain' => $new_sess['domain'],
                'secure' => $new_sess['secure'],
                'httponly' => $new_sess['httponly'],
                'samesite' => 'Lax'
            ]
        );
        
        //create global session
        $_SESSION['user'] = [
            'id' => $db_user['id'],
            'nick' => $db_user['nick'],
            'email' => $db_user['email'],
            'avatar' => $db_user['avatar'],
            'rank' => $db_user['rank'],
            'rank_name' => $u_rank_name
        ];

        //html ok
        html_ok("Successfully logged in! ^^");
    } catch(PDOException $e) {
        debug($e->getMessage());
    }
?>