<?php

    require_once('./Core/Request.php');
    require_once('./Core/Router.php');
    require_once('Utils.php');

    $route = new Router(new Request($_SERVER));

    $route->get('/', function($req) {
        render_view('./Views/index.php');
    });

    $route->get('/posts', function($req) {
        $dummyPosts = [
            [
                'title' => 'Dummy Title',
                'content' => 'This is some dummy content.',
                'author' => 'Tom Ekander'
            ],
            [
                'title' => 'Another Dummy Title',
                'content' => 'Also some dummy content.',
                'author' => 'Whomever'
            ]
        ];

        render_view('./Views/posts.php', ['posts' => $dummyPosts]);
    });

    $route->post('/posts', function($req) {
        try {
            print_r($req);
        } catch (Exception $err) {
            $err->getMessage();
        }
    });

    $route->get('/posts/:id', function($req) {
        print_r($req);
    });

?>