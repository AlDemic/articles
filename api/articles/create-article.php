<?php 
    //create article server logic

    require_once dirname(__DIR__,2) . '/core/init.php';

    //if wrong method
    wrongMethod('POST', 'json');

    //if not logged in
    notLogged('json');

    //get globals from form
    $a_title = trim($_POST['title']) ?? '';
    $a_ctgry = (int)$_POST['ctgry'] ?? (int)1;
    $a_short_desc = trim($_POST['short-desc']) ?? '';
    $a_full_desc = trim($_POST['full-desc']) ?? '';

    //check if filled up title and description
    if($a_title === '' || $a_full_desc === '') {
        $err_title = ($a_title === '') ? 'title ' : '';
        $err_desc = ($a_full_desc === '') ? 'description' : '';
        $err_msg = "Please, fill up: " . $err_title . $err_desc;

        //send json answer
        json_error($err_msg, 200);
    }

    //check str length for title and description
    if(mb_strlen($a_title) < 5 || mb_strlen($a_title) > 128) {
        json_error("Title must be between 5 and 128 symbols.", 200);
    }

    if(mb_strlen($a_short_desc) > 255) {
        json_error("Short description should be not more than 255 symbols", 200);
    }

    if(mb_strlen($a_full_desc) < 100 || mb_strlen($a_full_desc) > 2025) {
        json_error("Full description must be between 100 and 2025 symbols.", 200);
    }

    //fill up short_desc if empty
    if($a_short_desc === '') {
        $a_short_desc = mb_substr($a_full_desc, 0, 255);
    }

    //add to db and send js answer
    try {
        $db_add = $pdo->prepare('INSERT INTO articles (title, ctgry, short_desc, full_desc, id_author, added_at)
                                             VALUES (:title, :ctgry, :short_desc, :full_desc, :id_author, NOW())');
        $db_add->execute([
            'title' => $a_title,
            'ctgry' => $a_ctgry,
            'short_desc' => $a_short_desc,
            'full_desc' => $a_full_desc,
            'id_author' => $_SESSION['user']['id']
        ]);

        //send answer that ok
        json_ok("Article is added successfully!");
    } catch(PDOException $e) {
        debug($e->getMessage());
        json_error("Wrong db work", 500);
    }


?>