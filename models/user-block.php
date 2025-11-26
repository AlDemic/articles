<?php 
    require_once dirname(__DIR__, 1) . '/core/init.php';
    require_once MODELS_PATH . "users/user-session.php";
?>

<div class="user__avatar">
    <img src="/img/avatars/<?php echo $u_avatar ?>" width="48" height="48" alt="Avatar" />
    <?php if(isset($u_id) && $u_id > 0): ?>
        <a href="/models/users/avatar.php"><img src="/img/arrows.png" alt="Change avatar"></a>
    <?php endif; ?>
</div>
<h3>Hello, <?php echo $u_nick ?>!</h2>
    <?php if($u_id > 0): ?>
        <h4>That's main info about you:</h3>
        <p>id: <?php echo $u_id ?> </p>
        <p>email: <?php echo $u_email ?> </p>
        <p>rank: <?php echo $u_rank_name ?> </p>
        <a href='/models/articles/create-article.php'><button>Suggest article</button></a>
        <a href='/models/articles/my-articles.php'><button>My articles</button></a>
        <a href='/models/users/promotion.php'><button>Promotion panel</button></a>
        <?php if($u_rank > 1): ?>
            <a href='/models/articles/admin-panel.php'><button>Admin panel</button></a>
        <?php endif; ?>
        <?php 
            require_once MODELS_PATH . '/users/logout.php'; 
        ?>
        <?php 
            else:
                require_once MODELS_PATH . 'users/login-form.php';
            endif;
        ?>