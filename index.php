<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('./core/App.php');
    require_once('./core/Request.php');

    require_once('./models/PostModel.php');
    require_once('./models/CategoryModel.php');
    require_once('./models/PostCategoryModel.php');
    require_once('./models/UserModel.php');

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

    $app->get('/posts', function($req, $res) {

        $posts = Post::findAll();
        $post = Post::findById(1);

        $categories = Category::findAll();
        $postCategories = PostCategory::findAll();

        foreach ($posts as $key => &$newPost) {
            foreach ($postCategories as $postCategory) {
                if ($newPost['id'] === $postCategory['post_id']) {
                    foreach($categories as $category) {
                        if($category['id'] === $postCategory['category_id']) {
                            $newPost['category'] = $category['name'];
                        }
                    }
                }
            }
        }

        $updatedPost = Post::findByIdAndUpdate(1, [
            'title' => 'Woohoo, brand new title!',
            'content' => 'And some kind of, half new content!'
        ], true);

        $res->render_template('posts.html', [
            'posts' => $posts, 
            'post' => $post, 
            'updatedPost' => $updatedPost,
            'isAuthed' => $req->isAuthed
        ]);
    });

    $app->get('/auth', 'isAuthed', function($req, $res) {
        $categories = Category::findAll();

        $res->render_template('auth.html', ['req' => $req, 'categories' => $categories]);
    });

    $app->post('/auth', function($req, $res) {
        try {

            $userFound = User::find([
                'username' => $req->body['username']
            ]);

            $password = $req->body['password'];

            if ($password === $userFound['password']) {
                setcookie('id', $userFound['id'], time()+3600);

                $res->redirect('/auth');
            }

        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $app->post('/posts', function($req, $res) {
        try {

            $newPost = new Post(
                $req->body['title'], 
                $req->body['content'],
                $req->cookies['id']
            );
            
            $postToReturn = $newPost->save();

            $addPostCategory = new PostCategory(
                $postToReturn['id'],
                $req->body['category_id']
            );

            $addPostCategory->save();

            $res->redirect('http://localhost:8080/posts');

        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $app->get('/posts/:id', function($req, $res) {
        try {

            $post = Post::findOneById($req->params['id']);

            $res->render_template('post.html', ['post' => $post, 'req' => $req]);

        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $app->get('/posts/:userID/:categoryID', function($req, $res) {
        try {

            $userID = $req->params['userID'];
            $categoryID = $req->params['categoryID'];

            $postCategories = PostCategory::find(['category_id' => $categoryID]);

            if (empty($postCategories)) {
                throw new Exception("Woops. The arguments are probably out of range. Try again!");
            }

            $postsWithUserAndCategory = [];
            foreach($postCategories as $postCategory) {
                $postsWithUserAndCategory[] = Post::find(['id' => $postCategory['post_id'], 'user_id' => $userID]);
            }
            
            $category = Category::find(['id' => $categoryID]);   
            
            foreach($postsWithUserAndCategory as &$newPost) {
                $newPost['category'] = $category['name'];
            }
            
            $res->json(['posts' => $postsWithUserAndCategory], 200);

        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $app->get('/admin', 'requireLogin', function ($req, $res) {
        try {
            $userFound = User::findOneById($req->cookies['id']);
    
            unset($userFound['password']);
    
            $res->render_template('admin.html', ['user' => $userFound]);
        } catch (Exception $err) {
            $res->json(["message" => "Oops. Something went wrong."]);
        }
    });

    // fake auth
    function isAuthed($req, $res) {
        if (isset($req->cookies['id'])) {
            $req->isAuthed = true;
        } else {
            $req->isAuthed = false;
        }
    }

    function requireLogin($req, $res) {
        if (isset($req->cookies['id'])) {
            $req->isAuthed = true;
        } else {
            $req->isAuthed = false;
        }

        if (! $req->isAuthed) {
            $res->render_template('error.html', [
                'status_code' => 401, 
                'message' => "Oops, seems as though you're not authorized to view this page."
            ], 401);
        }
    }

    $app->start();

?>