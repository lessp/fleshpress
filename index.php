<?php

    require_once('./core/Request.php');
    require_once('./core/Router.php');
    require_once('./utils/Utils.php');
    require_once('./models/PostModel.php');

    $route = new Router(new Request($_SERVER));

    $route->get('/', function($req) {
        render_view('./views/start.php');
    });

    $route->get('/posts', function($req) {
        $posts = Post::findAll();
        $post = Post::findById(1);

        $didUpdate = Post::findByIdAndUpdate(1, [
            'title' => 'Pew pew, brand new title',
            'content' => 'And some kinda, half new content!'
        ]);

        if ($didUpdate) {
            print_r('Whoa, success!');
        }

        render_view('./views/posts.php', ['posts' => $posts, 'post' => $post]);
    });

    $route->post('/posts', function($req) {
        try {
            print_r($req);
        } catch (Exception $err) {
            render_response(500, $err->getMessage());
        }
    });

    $route->get('/posts/:id', function($req) {
        print_r($req);
    });

    $route->get('/admin', function($req) {
        render_view('./admin.php');
    });

    $route->start();

?>