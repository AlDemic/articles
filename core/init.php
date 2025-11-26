<?php
    //core/init.php

    //start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    //path vars
    define('BASE_PATH', __DIR__ . '/../');
    define('CORE_PATH', BASE_PATH . 'core/');
    define('MODELS_PATH', BASE_PATH . 'models/');
    define('API_PATH', BASE_PATH . 'api/');
    define('JS_PATH', '/js/');

    //add helpers
    require_once CORE_PATH . 'helpers.php';

    //connect db
    require_once CORE_PATH . 'db.php';

    //additional functions(id -> nick etc)
    require_once API_PATH . 'articles/additional.php';
?>