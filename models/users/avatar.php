<?php
    require_once dirname(__DIR__, 2) . '/core/init.php';
    require_once MODELS_PATH . "users/user-session.php";
    notLogged(); //only for logged
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select avatar</title>
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <div class="layout">
        <!--header block-->
        <?php include_once MODELS_PATH . 'header.php'; ?>
        <main>
            <!--search and categories section(left sidebar-->
            <section class='categories'>
                <?php include_once MODELS_PATH . 'menu-block.php'; ?>
            </section>
            <!--center block with articles-->
            <div class='articles'>
                <div class='articles__status' id='articles__status'></div>
                <!--avatar block(by ext file to make "live render")-->
                <div class="articles__avatar">
                    <?php include_once MODELS_PATH . 'users/avatar-block.php'; ?>
                </div>
            </div>
            <!--user block(right sidebar)-->
            <section class='user'>
                <?php include_once MODELS_PATH . 'user-block.php'; ?>
            </section>
        </main>
    </div>
    <script src='<?php echo JS_PATH ?>articles/render.js' defer></script>
    <script src='<?php echo JS_PATH ?>users/avatar.js' defer></script>
    <script src="<?php echo JS_PATH ?>articles/menu-block.js" defer></script>
</body>
</html>