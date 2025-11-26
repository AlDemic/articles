<?php
    //registration logic

    require_once '../../core/init.php'; //db, helpers

    //check if post method
    wrongMethod();

    //if already exist in session
    loggedInAlready();

    //get globals
    $reg_nick = trim($_POST['nick'] ?? null);
    $reg_email = trim($_POST['email']);
    $reg_email_sanitize = filter_var($reg_email, FILTER_SANITIZE_EMAIL); //remove "odds symbols"
    $reg_pass = $_POST['pass'] ?? null;

    //validation
    $reg_nick_ok = check_user_nick($reg_nick);
    $reg_email_ok = filter_var($reg_email_sanitize, FILTER_VALIDATE_EMAIL) !== false;
    $reg_pass_ok = check_user_password($reg_pass);

    //if something not pass 
    if(!$reg_nick_ok || !$reg_email_ok || !$reg_pass_ok) {
        $err_nick = ($reg_nick_ok) ? "" : "<i>nickname</i>";
        $err_email = ($reg_email_ok) ? "" : "<i>email</i>";
        $err_pass = ($reg_pass_ok) ? "" : "<i>password</i>";

        $msg = "Fields that not pass validation: " . $err_nick . $err_pass . $err_email;
        html_error($msg, MODELS_PATH . 'users/reg-form.php');
    }

    //check for nick/email duplicates in db
    try {
        //nick sql
        $db_check_nick = $pdo->prepare("SELECT 1 FROM users WHERE nick = :nick");
        $db_check_nick->execute([
            'nick' => $reg_nick
        ]);
        
        //email sql
        $db_check_email = $pdo->prepare("SELECT 1 FROM users WHERE email = :email");
        $db_check_email->execute([
            'email' => $reg_email_sanitize
        ]);
   
        //nick/email check
        $nick_busy = ($db_check_nick->fetchColumn()) ? true : false;
        $email_busy = ($db_check_email->fetchColumn()) ? true : false;

        //html page error if nick or email exist
        if($nick_busy || $email_busy) {
            $n_busy_msg = ($nick_busy) ? "nick " : "";
            $em_busy_msg = ($email_busy) ? "email" : "";
            $msg = "Sorry, but some fileds are busy. Change: " . $n_busy_msg . $em_busy_msg;

            html_error($msg);
        }


    } catch(PDOException $e) {
        $err = $e->getMessage();
        $msg = "DB wrong during user registration: " . $err;
        debug($msg);
    }

    //if all is ok -> create a new user
    try {
        $db_new_user = $pdo->prepare("INSERT INTO users (nick, email, pass) VALUES (:nick, :email, :pass)");
        $db_new_user->execute([
            'nick' => $reg_nick,
            'email' => $reg_email_sanitize,
            'pass' => password_hash($reg_pass, PASSWORD_DEFAULT)
        ]);

        //ok notification page
        html_ok("User successfully created!");
    } catch(PDOException $e) {
        $err = $e->getMessage();
        $msg = "DB wrong during user registration: " . $err;
        debug($msg);
    }

?>