<?php

    require_once('./core/Request.php');
    require_once('./core/Router.php');
    require_once('./utils/Utils.php');
    require_once('./models/PostModel.php');

    $route = new Router(new Request($_SERVER));

    $route->get('/', function($req, $res) {
        $res->render_template('./views/start.html');
    });

    $route->get('/posts', function($req, $res) {
        $posts = Post::findAll();
        $post = Post::findById(1);

        $updatedPost = Post::findByIdAndUpdate(1, [
            'title' => 'Wooah, brand new title!',
            'content' => 'And some kind of, half new content!'
        ], true);

        $res->render_template('./views/posts.html', [
            'posts' => $posts, 
            'post' => $post, 
            'updatedPost' => $updatedPost
        ]);
    });

    $route->post('/posts', function($req, $res) {
        try {
            $result = $req->getBody();

            $newPost = new Post(
                $result->get('title'), 
                $result->get('content')
            );
            
            $postToReturn = $newPost->save();

            $res->json($postToReturn, 200);

        } catch (Exception $err) {
            $res->json(['message' => 'Oops. There was an error.'], 500);
        }
    });

    $route->get('/posts/:id', function($req, $res) {
        print_r($req);
    });

    $route->get('/admin', function($req, $res) {
        render_view('./admin.php');
    });

    $route->start();

?>