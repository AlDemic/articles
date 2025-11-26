<?php 
    require_once "helpers.php";

    //base properties of bd
    $dsn = "mysql:host=127.0.1.14;dbname=articles;charset=utf8";
    $db_user = "root";
    $db_pass = "";

    //connection to db
    try {
        $pdo = new PDO($dsn, $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        debug($e->getMessage());
    }

?>