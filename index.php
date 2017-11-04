<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('./core/App.php');
    require_once('./core/Request.php');

    require_once('./models/PostModel.php');

    $app = new App();

    class Sessions extends Request {
        
        private $data;

        public function __construct() {
            $this->data['example_session_status'] = 'Session started';
            $this->data['example_session_id'] = 'Session ID';
        }
    }

    class SomethingElse extends Request {
        
        private $data;

        public function __construct() {
            $this->data['another_middleware_param'] = 'Some data';
            $this->data['another_middleware_param2'] = 'Some other data';
        }
    }

    $app->use(new Sessions);
    $app->use(new SomethingElse);

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

            $newPost = new Post(
                $req->body['title'], 
                $req->body['content']
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

    $app->get('/posts/:categoryID/:postID', function($req, $res, $params) {
        try {
            $res->json(
                [
                    'category' => $params['categoryID'],
                    'post' => $params['postID']
                ],
                200
            );
        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    // fake auth
    function requireLogin($req, $res) {
        $isAuthed = false;
        if (! $isAuthed) {
            $res->json(["message" => "You need to be logged in."]);
        }
    }

    $app->start();

?>