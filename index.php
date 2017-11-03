<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('./core/Router.php');
    require_once('./models/PostModel.php');
    require_once('./models/UserModel.php');

    $route = new Router();

    $route->get('/', function($req, $res) {
        $res->render_template('start.html', ['req' => $req]);
    });

    $route->get('/posts', function($req, $res) {
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

    $route->get('/auth', function($req, $res) {

        $isAuthed = $req->cookies()->has('id') ? true : false;

        $res->render_template('auth.html', [
            'req' => $req, 
            'isAuthed' => $isAuthed
        ]);
    });

    $route->post('/auth', function($req, $res) {
        try {
            $result = $req->body();

            $userFound = User::find([
                'username' => $result->get('username')
            ]);

            $password = $result->get('password');

            if ($password === $userFound['password']) {
                setcookie('id', $userFound['id'], time()+3600);

                // $res->render_template('auth.html', ['user' => $userFound]);
                $res->redirect('/auth');
            }

        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $route->post('/posts', function($req, $res) {
        try {
            $result = $req->body();

            $newPost = new Post(
                $result->get('title'), 
                $result->get('content')
            );
            
            $postToReturn = $newPost->save();

            // $res->json($postToReturn, 200);
            $res->redirect('/posts');

        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $route->get('/posts/:id', function($req, $res, $params) {
        try {
            $post = Post::findOneById($params['id']);

            $res->render_template('post.html', ['post' => $post]);
        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });


    // $route->get('/posts/:id', function($req, $res, $params) {
    //     try {
    //         $post = Post::findById($params['id']);

    //         $res->json($post, 200);
    //     } catch (Exception $err) {
    //         $res->json($err->getMessage(), 400);
    //     }
    // });

    $route->start();

?>