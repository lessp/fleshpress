<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('./core/Fleshpress.php');
    require_once('./core/Request.php');

    require_once('./models/PostModel.php');
    require_once('./models/CategoryModel.php');
    require_once('./models/PostCategoryModel.php');
    require_once('./models/UserModel.php');

    $app = new Fleshpress();

    class Session {
        
        private $_settings;

        public function __construct(
            int $lifeTime = 3600,
            string $path = '/',
            string $domain = null,
            bool $secure = false,
            bool $httpOnly = false
        ) {

            $this->_settings['name'] = 'fleshpress_session';

            $this->_settings['lifeTime'] = $lifeTime;
            $this->_settings['path'] = $path;
            $this->_settings['domain'] = $domain;
            $this->_settings['secure'] = $secure;
            $this->_settings['httpOnly'] = $httpOnly;

        }

        public function __invoke(Request $req, Response $res) {
            
            session_set_cookie_params(
                $this->_settings['lifeTime'],
                $this->_settings['path'],
                $this->_settings['domain'],
                $this->_settings['secure'],
                $this->_settings['httpOnly']
            );
                    
            session_name($this->_settings['name']);
            session_start();

        }
    
        public function __set($name, $value)
        {
            $_SESSION[$this->_settings['name']][$name] = $value;
        }

        public function __get($name)
        {

            if (array_key_exists($name, $_SESSION[$this->_settings['name']])) {
                return $_SESSION[$this->_settings['name']][$name];
            }

            return null;
        }

        public function __isset($name)
        {
            return isset($_SESSION[$this->_settings['name']][$name]);
        }

        public function __unset($name)
        {
            unset($_SESSION[$this->_settings['name']][$name]);
        }

    }

    $app->use(new Session());

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
                            $newPost['category_id'] = $category['id'];
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
            'categories' => $categories,
            'isAuthed' => $req->isAuthed
        ]);
    });

    $app->get('/auth', 'isAuthed', function($req, $res) {
        $categories = Category::findAll();

        $res->render_template('auth.html', ['req' => $req, 'categories' => $categories]);
    });

    $app->get('/auth/logout', 'isAuthed', function($req, $res) {
        unset($req->session->user);

        $res->redirect('/auth');
    });

    $app->post('/auth', function($req, $res) {
        try {

            $userFound = User::find([
                'username' => $req->body['username']
            ]);

            $password = $req->body['password'];
            
            if (password_verify($password, $userFound['password'])) {

                $req->session->user = [
                    'id' => $userFound['id'],
                    'first_name' => $userFound['first_name'],
                    'last_name' => $userFound['last_name'],
                    'username' => $userFound['username']
                ];
            
            }
            
            $res->redirect('/auth');

        } catch (Exception $err) {
            $res->render_template('error.html', [
                'status_code' => 400, 
                'message' => $err->getMessage()
            ], 400);
        }
    });

    $app->post('/posts', 'requireLogin', function($req, $res) {
        try {

            if (! empty($req->body['title']) &&
                ! empty($req->body['content'])) {

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

                    $res->redirect('/posts');
            }

            $res->redirect('/auth');

        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $app->get('/post/:id', function($req, $res) {
        try {

            $post = Post::findOneById($req->params['id']);

            $res->render_template('post.html', ['post' => $post, 'req' => $req]);

        } catch (Exception $err) {
            $res->json($err->getMessage(), 400);
        }
    });

    $app->get('/posts/:categoryID', function($req, $res) {
        try {

            $categoryID = $req->params['categoryID'];
            
            $postCategories = PostCategory::find(['category_id' => $categoryID]);
            $categoryName = Category::findOneById($categoryID);

            $posts;
            foreach($postCategories as $key => $postCategory) {
                $posts[$key] = Post::findOneById($postCategory['post_id']);
                $posts[$key]['category'] = $categoryName['name'];
            }

            $res->render_template('category.html', ['posts' => $posts, 'req' => $req]);

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

    $app->post('/user', function($req, $res) {
        try {

            $hashedPassword = generateHashedPassword($req->body['password']);
            
            $newUser = new User(
                $req->body['first_name'],
                $req->body['last_name'],
                $req->body['username'],
                $hashedPassword
            );

            $newUser->save();

            $res->redirect('/auth');

        } catch (Exception $err) {
            $res->render_template('error.html', ['message' => $err->getMessage()], 500);
        }
    });

    $app->get('/admin', 'requireLogin', function ($req, $res) {
        try {
            $userFound = User::findOneById($req->session->user['id']);
    
            unset($userFound['password']);
    
            $res->render_template('admin.html', ['user' => $userFound]);
        } catch (Exception $err) {
            $res->json(["message" => "Oops. Something went wrong."]);
        }
    });

    function isAuthed($req, $res) {

        if (isset($req->session->user)) {
            $req->isAuthed = true;
        } else {
            $req->isAuthed = false;
        }
    }

    function requireLogin($req, $res) {

        if (isset($req->session->user)) {
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

    function generateHashedPassword(string $password) {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
    }

    $app->start();

?>