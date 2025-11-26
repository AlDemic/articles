<?php 
    //php logic of changing avatar

    require_once dirname(__DIR__, 2) . '/core/init.php';

    header('Content-Type: application/json;charset=utf-8'); //return json

    //if wrong method
    wrongMethod('POST', 'json');

    //if not logged in
    notLogged('json');

    try {
        //work with file

        //get from global
        $ava = $_FILES['avatar'];

        $user_id = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        if($user_id <= 0) return json_error("Wrong user id", 200);

        //old avatar ext(to remove)
        $ext_old = isset($_SESSION['user']['avatar']) ? $_SESSION['user']['avatar'] : '';
        if($ext_old === '') json_error("You have incorrect old ext of avatar.", 200);

        //check for errors
        if($ava['error'] !== UPLOAD_ERR_OK) json_error("Error due to loading avatar", 200);

        //check size (not more than 2 mb):
        if($ava['size'] > 2 * 1024 * 1024) json_error("Picture is more than 2mb, change size.", 200);

        //check for extension
        $ext_array = ['png', 'jpg', 'jpeg', 'webp'];
        $ext_ava = strtolower(pathinfo($ava['name'], PATHINFO_EXTENSION));

        if(!in_array($ext_ava, $ext_array)) json_error("Wrong extension of pic. Change it.", 200);

        //where to save picture:
        $save_path = $_SERVER['DOCUMENT_ROOT'] . "/img/avatars/$user_id.$ext_ava";

        //remove old avatar if exist
        foreach($ext_array as $ext) {
            $oldAvatar = $_SERVER['DOCUMENT_ROOT'] . "/img/avatars/{$user_id}.{$ext_old}";
            if(file_exists($oldAvatar)) unlink($oldAvatar);
        }

        //save it
        if(!move_uploaded_file($ava['tmp_name'], $save_path)) json_error("Problem due to saving pic. Try again", 200);

        //change avatar status in db (0 - no have; 1 - have ava)
        $db_ava_save = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $db_ava_save->bindValue(1, $ext_ava, PDO::PARAM_STR);
        $db_ava_save->bindValue(2, (int)$user_id, PDO::PARAM_INT);
        $db_ava_save->execute();

        //update session
        $_SESSION['user']['avatar'] = $ext_ava;

        //send json
        //json_ok("Avatar is changed!");
        echo json_encode([
            'status' => 'ok',
            'msg' => "Changed!",
            'userId' => $user_id,
            'ext' => $ext_ava
        ]);
    } catch(PDOException $e) {
        debug($e->getMessage());
        json_error("Problem with db", 500);
    } catch(Exception $e) {
        debug($e->getMessage());
        json_error("Problem with server", 500);
    }
?>