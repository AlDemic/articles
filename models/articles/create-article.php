<?php
    require_once dirname(__DIR__, 2) . '/core/init.php';

    //page is for logged users
    notLogged();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create article</title>
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
                <h3>Create article:</h2>
                <div id="articles__status"></div>
                <form id='addArticle'>
                    <label>
                        Title(min 5 / max 128):<br/>
                        <input type="text" name="title" minlength="5" maxlength="128" required>
                    </label>
                    <br/>
                    <label>
                        Category:<br/>
                        <select name="ctgry" id="ctgry">
                            <option value="1">No category</option>
                            <option value="2">Science</option>
                            <option value="3">Cinema</option>
                            <option value="4">Animation</option>
                            <option value="5">Games</option>
                        </select>
                    </label>
                    <br/>
                    <label>
                        Short description(max - 255):<br/>
                        <textarea name="short-desc" rows="5" cols="33" maxlength="255" placeholder="If empty - auto generation(takes first 255 symbols from full description"></textarea>
                    </label>
                    <br/>
                    <label>
                        Full description(min - 100, max - 2025):<br/>
                        <textarea name="full-desc" rows="5" cols="33" minlength="100" maxlength="2025" placeholder="Full text of article"></textarea>
                    </label>
                    <button type='submit'>Add</button>
                </form>
            </div>
            <!--user block(right sidebar)-->
            <section class='user'>
                <?php include_once MODELS_PATH . 'user-block.php'; ?>
            </section>
            
        </main>
    </div>
    <script src='<?php echo JS_PATH ?>articles/create-article.js' defer></script>
    <script src='<?php echo JS_PATH ?>articles/menu-block.js' defer></script>
</body>
</html>