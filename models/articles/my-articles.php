<?php
    require_once dirname(__DIR__, 2) . '/core/init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My articles</title>
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
            <div class='articles' id='articles'>
                <h3>My articles:</h2>
                <div class="articles__block" id="articles__block"></div>
                <div class="articles__pagination" id="articles__pagination"></div>
            </div>
            <!--user block(right sidebar)-->
            <section class='user'>
                <?php include_once MODELS_PATH . 'user-block.php'; ?>
            </section>
            
        </main>
    </div>
    <script src="<?php echo JS_PATH ?>articles/render.js" defer></script>
    <script src="<?php echo JS_PATH ?>articles/menu-block.js" defer></script>
    <script src="<?php echo JS_PATH ?>articles/my-articles.js" defer></script>
</body>
</html>