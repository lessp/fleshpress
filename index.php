<?php

    require_once('Request.php');
    require_once('Router.php');
    require_once('Utils.php');

    $app = new Router(new Request($_SERVER));

    $app->get('/', function($req) {
        echo '<h1>Index with a GET-request</h1>';
    });

    $app->get('/posts', function($req) {
        render('./Views/Posts.php');
    });

    $app->post('/posts', function($req) {
        print_r($req);
    });

?>