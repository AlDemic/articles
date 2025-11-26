<?php
    require_once dirname(__DIR__,2) . '/core/init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Promotion panel</title>
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
                <div class='articles__info'>
                    <h2>Promotion info</h2>
                    <p>To increase your user's rank you should release articles that will be approved by administration group. <br/>
                        <b>Requirement:</b><br/>
                        <i>Moderator:</i> 3 approved article.<br/>
                        <i>Administrator:</i> 5 approved article.<br/>
                        Since you'll reach neccessary amounts - button for rank up will be appeared. 
                    </p>
                    <div class='articles__stats'></div>
                </div>
            </div>
            <!--user block(right sidebar)-->
            <section class='user'>
                <?php include_once MODELS_PATH . 'user-block.php'; ?>
            </section>
        </main>
    </div>
    <script src='<?php echo JS_PATH ?>articles/render.js' defer></script>
    <script src='<?php echo JS_PATH ?>users/promotion-stats.js' defer></script>
    <script src='<?php echo JS_PATH ?>users/promotion-btn.js' defer></script>
    <script src='<?php echo JS_PATH ?>articles/menu-block.js' defer></script>
</body>
</html>