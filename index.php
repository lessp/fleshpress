<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('./core/App.php');
    require_once('./models/PostModel.php');

    $app = new App();

    $app->get('/', function($req, $res) {
        $res->render_template('start.html', ['req' => $req]);
    });

    $app->get('/auth', 'requireLogin', function($req, $res) {
        $res->render_template('start.html', ['req' => $req]);
    });

    $app->get('/posts', function($req, $res) {
        $posts = Post::findAll();
        $post = Post::findById(1);

        $updatedPost = Post::findByIdAndUpdate(1, [
            'title' => 'Woohoo, brand new title!',
            'content' => 'And some kind of, half new content!'
        ], true);

        $res->render_template('posts.html', [
            'posts' => $posts, 
            'post' => $post, 
            'updatedPost' => $updatedPost
        ]);
    });

    $app->post('/posts', function($req, $res) {
        try {
            $result = $req->body();

            $newPost = new Post(
                $result->get('title'), 
                $result->get('content')
            );
            
            $postToReturn = $newPost->save();

            $res->json($postToReturn, 200);

        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $app->get('/posts/:id', function($req, $res, $params) {
        try {
            $post = Post::findOneById($params['id']);

            $res->render_template('post.html', ['post' => $post]);
        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $app->get('/posts/:id', function($req, $res, $params) {
        try {
            $post = Post::findById($params['id']);

            $res->json($post, 200);
        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $app->start();

?>