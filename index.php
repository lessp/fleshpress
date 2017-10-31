<?php

    require_once('./core/Request.php');
    require_once('./core/Router.php');
    require_once('./Utils.php');
    require_once('./model/PostModel.php');

    // initialisers
    Post::setTableName('posts');

    $route = new Router(new Request($_SERVER));

    $route->get('/', function($req) {
        render_view('./views/start.php');
    });

    $route->get('/posts', function($req) {
        // $dummyPosts = [
        //     [
        //         'title' => 'Dummy Title',
        //         'content' => 'This is some dummy content.',
        //         'author' => 'Tom Ekander'
        //     ],
        //     [
        //         'title' => 'Another Dummy Title',
        //         'content' => 'Also some dummy content.',
        //         'author' => 'Whomever'
        //     ]
        // ]; 

        

        render_view('./views/posts.php', ['posts' => $dummyPosts]);
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