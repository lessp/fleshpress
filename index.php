<?php

    require_once('Request.php');
    require_once('Router.php');

    $app = new Router(new Request($_SERVER));

    $app->get('/', function($req) {
        echo '<h1>Index with a GET-request</h1>';
    });

    $app->post('/', function($req) {
        print_r($params['req']);
    });

    $app->get('/posts', function($req) {
        render('./Views/Posts.php');
    });

    $app->post('/posts', function($req) {
        print_r($req);
    });

    function render(string $template = null, array $params = [])
    {
        ob_start();
        if (file_exists($template)) 
        {
            include($template);
        }
        return print ob_get_clean();
    }

?>