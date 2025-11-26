<?php 
    require_once '../../core/init.php'; //db, helpers

    //check if post method
    wrongMethod();

    //destroy cookie
    setcookie("PHPSESSID", "", time() - 3600);

    //destroy session
    session_unset();
    
    //inform user
    html_ok("Logged out! Come back!");
?>