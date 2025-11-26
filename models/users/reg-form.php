<?php
    require_once dirname(__DIR__,2) . '/core/init.php';

    //reg page only for not logged users
    loggedInAlready();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Articles Project - Registration</title>
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
                <h3>Registration:</h2>
                <form action="../../api/users/reg.php" method="POST" class="user__reg-form">
                    <label>
                        <b>Nick:</b>
                        <input type="text" name="nick" maxlength="64" required>
                    </label>
                    <label>
                        <b>Email:</b>
                        <input type="email" name="email" maxlength="128" required>
                    </label>
                    <label>
                        <b>Password:</b>
                        <input type="password" name="pass" maxlength="32" required>
                    </label>
                    <div class="user__login-btns">
                        <button type="submit" class="btn-primary">Send</button>
                        <a href='/'><button type='button' class="btn-secondary">Back to Main</button></a>
                    </div>
                </form>
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