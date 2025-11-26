<?php 
    require_once dirname(__DIR__, 2) . "/core/init.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Article full</title>
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
                <h3>Articles:</h2>
                <div class='articles__filters'></div>
                <div class="articles__block" id="articles__block"></div>
                <div class="articles__pagination" id="articles__pagination"></div>
                <div class="articles__comments" id="articles__comments">
                    <div class="comments__notif"></div>
                    <div class="comments__block" id="comments__block"></div>
                    <?php if(isset($_SESSION['user']['id'])): ?>
                        <form id="addComment">
                            <textarea name="comment" id="comment" minlength="3" maxlength="512" required placeholder="min 3, max 512 symbols"></textarea>
                            <button type="submit">Add</button>
                        </form>
                    <?php else: ?>
                        <div class="comment"><p>Login to write comments</p></div>
                    <?php endif; ?>
                </div>
            </div>

            <!--user block(right sidebar)-->
            <section class='user'>
                <?php include_once MODELS_PATH . 'user-block.php'; ?>
            </section>
            
        </main>
    </div>
    <script src="<?php echo JS_PATH ?>articles/render.js" defer></script>
    <script src="<?php echo JS_PATH ?>articles/comments.js" defer></script>
    <script src="<?php echo JS_PATH ?>articles/aFull.js" defer></script>
    <script src="<?php echo JS_PATH ?>articles/add-comment.js" defer></script>
    <script src="<?php echo JS_PATH ?>articles/del-comment.js" defer></script>
    <script src="<?php echo JS_PATH ?>articles/menu-block.js" defer></script>
</body>
</html>