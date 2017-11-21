<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('./core/Fleshpress.php');
    require_once('./core/Request.php');

    require_once('./middleware/Session.php');
    require_once('./middleware/Utils.php');

    require_once('./models/PostModel.php');
    require_once('./models/CategoryModel.php');
    require_once('./models/PostCategoryModel.php');
    require_once('./models/UserModel.php');
    require_once('./models/TagModel.php');
    require_once('./models/PostTagsModel.php');
    require_once('./models/PasswordToken.php');

    $app = new Fleshpress();

    $app->use(new Session());

    $app->get('/', function($req, $res) {
        $res->render_template('start.html', ['req' => $req]);
    });

    require_once('./routes/posts.php');
    require_once('./routes/auth.php');

    function generateHashedPassword(string $password) {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
    }

    $app->start();

?>
