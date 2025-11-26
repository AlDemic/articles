<?php
    $u_nick = isset($_SESSION['user']['nick']) ? $_SESSION['user']['nick'] : 'Guest';
    $u_id = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
    $u_email = isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : 'no';
    $u_rank = isset($_SESSION['user']['rank']) ? (int)$_SESSION['user']['rank'] : 0;
    $u_rank_name = isset($_SESSION['user']['rank_name']) ? $_SESSION['user']['rank_name'] : 'Guest';
    $u_avatar = (isset($_SESSION['user']['avatar']) && $_SESSION['user']['avatar'] !== '0') ? $u_id . '.' . $_SESSION['user']['avatar'] : 'user.png';
?>