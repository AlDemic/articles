<?php
    //additional functions as assist
    //consist of: debug, html_error, json_error, html_ok, json_ok, loggedInAlready, wrongMethod, notLogged, 
    //check_user_nick, check_user_password, onlyAdmin

    //debug writing to txt file
    function debug($msg) {
        try {
            //create string for file-writing
            //time of record
            $time = date("Y-m-d H:i:s");

            //file name
            $file = __DIR__ . '/debug.txt'; 

            //full line
            $err_msg = "$time : $msg";

            //write to file to last line
            file_put_contents($file, PHP_EOL . $err_msg, FILE_APPEND);
            exit;
        } catch(Exception $e) {
            error_log($e->getMessage());
        }
    }

    //html error base
    function html_error($msg = 'Something wrong.', $back = '/') {
        header('Content-Type: text/html; charset=utf-8');
        echo $msg . "<br/>";
        echo "<a href='$back'><- Go Back</a><br/>";
        exit;
    }

    //json error base
    function json_error($msg = 'Something wrong.', $code = 400) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'msg' => $msg
        ]);
        exit;
    }

    //html okey notification 
    function html_ok($msg, $back='/') {
        header('Content-Type: text/html; charset=utf-8');
        echo "Good! <b>$msg</b>.<br/>";
        echo "<a href='$back'>Go back</a>";
        $URL="/";
        echo '<META HTTP-EQUIV="refresh" content="1;URL=' . $URL . '">';
        exit;
    }

    //json okey notification 
    function json_ok($msg) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'msg' => $msg
        ]);
        exit;
    }

    //page for not logged in users
    function loggedInAlready($type = "html") {
        //if user' session exist
        if(isset($_SESSION['user']['id'])) {
            $msg = "You are logged in already.";

            //json or html error
            if($type === "json") {
                json_error($msg);
            } else {
                html_error($msg);
            }
        }
    }

    //page for logged in users
    function notLogged($type = "html") {
        //if user' session exist
        if(!isset($_SESSION['user']['id'])) {
            $msg = "You are not logged in!";

            //json or html error
            if($type === "json") {
                json_error($msg);
            } else {
                html_error($msg);
            }
        }
    }

    //wrong method
    function wrongMethod($method = 'POST', $type = 'html') { //post and html - standart
        if($_SERVER['REQUEST_METHOD'] !== $method) {
            if($type === 'json') {
                json_error('Wrong method');
            } else {
                html_error('Wrong method.');
            }
        }
    }

    //patterns block
    function check_user_nick($nick) {
        //if empty or ''
        if($nick === null || $nick === '') return false;

        //pattern of nick
        $nick_pattern = '/^[a-zA-Z0-9_]{3,20}$/';

        return (bool)preg_match($nick_pattern, $nick);
    }

    function check_user_password($pass) {
        //if empty
        if($pass === null || $pass === '') return false;

        //pattern of password
        $pass_pattern = '/^\d+$/';

        return (bool)preg_match($pass_pattern, $pass);
    }

    function onlyAdmin($type = 'html', $rank = 0) {
        if($rank <= 1) {
            if($type === 'json') {
                json_error('This page for admin only');
            } else {
                html_error('This page for admin only');
            }
        }
    }
?>