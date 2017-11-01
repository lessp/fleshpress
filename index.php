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

        $updatedPost = Post::findByIdAndUpdate(1, [
            'title' => 'Woohoo, brand new title!',
            'content' => 'And some kind of, half new content!'
        ], true);

        $newPost = new Post('Title', 'Content');

        $newlySavedPost = $newPost->save();

        print_r($newlySavedPost);

        render_view('./views/posts.php', ['posts' => $posts, 'post' => $post, 'updatedPost' => $updatedPost]);
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