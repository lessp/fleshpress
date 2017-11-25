# Fleshpress

A very basic `PHP` MVC framework inspired by `Express (NodeJS)` and `Flask (Python)` made as my first PHP-assignment at [Chas Academy - Fullstack Web Developer](https://chasacademy.se "Chas Academy").

## Basic Route Example

```php
$app = new Fleshpress();

$app->get('/', function($req, $res) {
    $res->send('Hello World!', 200);
});

$app->start();
```

## Models

Custom models extend from `Model` and implement the abstract function `abstract static public function register();` and a schema.

Here's a basic example.

```php
class BlogPostModel extends Model {

    protected static $tableName;
    protected $schema;

    public function __construct(string $title, string $content) {
        $this->schema = [
            'title' => $title,
            'content' => $content
        ];
    }

    public static function register()
    {
        static::$tableName = 'blog_posts';
    }
}

BlogPostModel::register();

```

The base model currently supports the following queries.

```php
Model::findAll();
Model::find(array $params);
Model::findById(int $id);
Model::findOneById(int $id);
Model::findByIdAndUpdate(
    int $id, 
    array $params, 
    bool $returnUpdatedItem = false, 
    string $idDenominator = 'id' // may be removed
);
Model::delete(array $params);
Model::deleteOneById(int $id);
```

To create a new Blog Post in this example we would use the following syntax:

```php
    $newBlogPost = new BlogPostModel(
        'This is a title',
        'And this is the content'
    );

    $newBlogPost->save();
```

## Middleware Functions

To use a middleware function we can use the following syntax:

```php
$app->get('/protected', 'requireLogin', function($req, $res) {
    $res->render_template('protected.html');
});

function requireLogin($req, $res) {
    $isAuthed = false;

    if (! $isAuthed) {
        $res->json(["message" => "Oops. It seems as though you're not logged in."], 401);
    }
}
```

## More Examples

```php
$app->get('/posts', function($req, $res) {
    try {

        $posts = BlogPostModel::findAll();

        $res->render_template('posts.html', ['posts' => $posts]);

    } catch (Exception $err) {
        $res->error(['message' => $err->getMessage()], 500) // the Response error-method defaults to error.html
    }
});

$app->get('/post/:id', function($req, $res) {
    $post = BlogPostModel::findOneById($req->params['id']);

    $res->json(['post' => $post]);
});

$app->get('/post/:id/:anotherID', function($req, $res) {
    $id = $req->params['id']; // eg. 1
    $anotherID = $req->params['anotherID']; // eg. 3
});

$app->post('/post', function($req, $res) {
    try {

        $newPost = new BlogPostModel(
            $req->body['title'],
            $req->body['content']
        );

        $newPost->save();

        $res->redirect('/posts');

    } catch (Exception $err) {
        $res->json(["message" => $err->getMessage()], 500);
    }
});
```

## Middleware

You can also add global middlewares like this.

Eg.

```php
$app->use(new SomeMiddleware);
```

And to roll your own extend from the MiddleWare-class.

See [Session Middleware](middlewares/Session.php) for an example.

The Session Middleware is included on every page which can be useful for logging in/out user or similar.

```php

$app = new Fleshpress();

$app->use(new Session());
$app->config['db'] = [
    'dsn' => 'mysql:host=127.0.0.1;dbname=blog;charset=utf8',
    'user' => 'root',
    'password' => 'root'
];

$app->post('/login', function($req, $res) {

    $userFound = UserModel::find(['email' => $req->body['email']]);

    if (! empty($userFound)) {
        if (password_verify($req->body['password'], $userFound['password'])) {
            $req->session->user = [
                'id' => $userFound['id'],
                'name' => $userFound['name']
            ];

            $res->redirect('/admin');
        }
    }

    $res->redirect('/login');

});
```
