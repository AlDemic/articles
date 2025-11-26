<?php
    require_once dirname(__DIR__,2) . '/core/init.php';
    require_once API_PATH . 'articles/admin-panel.php'; //for admin only
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Moderation page</title>
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
                <h3>Articles on moderation:</h2>
                <div class='articles__filters'></div>
                <div class='articles__info'>
                    <h4>Votes info:</h3>
                    <span><b>Approved article:</b> Sum >= 10 point <br/> <b>Declined article:</b> Sum <= -5 point
                        <br/><i>Sum = appr + decl (points)</i>
                    </span>
                    <span>
                        <p>Each rank has his own points for appr/decl:</p>
                        Moderator: +-3 <br/>
                        Administrator: +-5
                    </span>
                </div>
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
    <script src='<?php echo JS_PATH ?>articles/menu-block.js' defer></script>
    <script src="<?php echo JS_PATH ?>articles/on-moderation.js" defer></script>
    <script src="<?php echo JS_PATH ?>articles/approve-decline.js" defer></script>
</body>
</html>