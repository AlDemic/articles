<?php
    require_once dirname(__DIR__,2) . '/core/init.php';
    require_once API_PATH . 'articles/admin-panel.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin panel</title>
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
                <h3>Admin panel:</h2>
                <a href='on-moderation.php'><button>Articles on moderation</button></a>
            </div>
            <!--user block(right sidebar)-->
            <section class='user'>
                <?php include_once MODELS_PATH . 'user-block.php'; ?>
            </section>
        </main>
    </div>
    <script src='<?php echo JS_PATH ?>articles/menu-block.js' defer></script>
</body>
</html>