# Fleshpress

A very basic `PHP` MVC framework inspired by `Express (NodeJS)` and `Flask (Python)` made as my first PHP-assignment at [Chas Academy - Fullstack Web Developer](https://chasacademy.se "Chas Academy").

Please note that this code is obviously __not__ something you would use in production!

# Table of Contents

[Basic Route Example](#basic-route-example)<br>
[Models](#models)<br>
[Middleware Functions](#middleware-functions)<br>
[More Examples](#more-examples)<br>
[Middleware Classes](#middleware)<br>
[Config](#config)<br>

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

Here's a basic example where `'title'` and `'content'` are the names of the columns in your table and `'blog_posts'` is the actual table name.

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
/* Protected Route */
$app->get('/protected', 'requireLogin', function($req, $res) {
    $res->render_template('protected.html');
});

/* Middleware function, has acccess to the Request and Response-object */
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

The Session Middleware is included on every page which can be useful for logging in/out a user or similar.

```php

$app = new Fleshpress();

$app->use(new Session());
$app->config['db'] = [
    'dsn' => 'mysql:host=127.0.0.1;dbname=blog;charset=utf8',
    'user' => 'root',
    'password' => 'root'
];

/* Login */
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

/* Logout */
$app->get('/logout', function($req, $res) {
    $req->session->user = null;

    $res->redirect('/');
});
```

## Config

Although I've tried to keep things as un-opinionated as possible, there are some default settings 
and some that needs to be followed, at the moment.

### Template Folder

The template path defaults to /static/templates, this can be overriden by calling the config on the Fleshpress-object.

```php
$app = new Fleshpress();

$app->config['templatesFolder'] = '/some/path/';
```

### Database Connection

Database connections are optional, but whenever you use it you must provide the config with the following information.

```php
$app = new Fleshpress();

$app->config['db'] = [
    'dsn' => 'mysql:host=HOST;dbname=DBNAME;charset=utf8',
    'user' => 'USER',
    'password' => 'PASSWORD'
];
```

## Authors

* **Tom Ekander** - *Initial work* - [lessp](https://github.com/lessp)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* [axelolsson](https://github.com/axelolsson) - for help, trying to teach us this stuff and for letting me borrow bits and pieces
* [Flask](http://flask.pocoo.org/), [Express](https://expressjs.com/), [Mongoose](http://mongoosejs.com/) - for inspiration
