# 05-dynamisk-webbplats-php-lessp

This is a very basic `PHP` MVC framework inspired by `Express (NodeJS)` and `Flask (Python)` made as my first PHP-assignment at Chas Academy - Fullstack Web Developer.

## Basic Route Example

```php
$app = new App();

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

`BlogPostModel::findAll()`

`BlogPostModel::find(array $params)`

`BlogPostModel::findById(int $id)`

`BlogPostModel::findOneById(int $id)`

`BlogPostModel::findByIdAndUpdate(int $id, array $params, bool $returnUpdatedItem = false, string $idDenominator = 'id')`

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
        $res->render_template('error.html', ['message' => $err->getMessage()], 500)
    }
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

You can also add custom objects to the Request object.

Eg.

```php
$app->use(new SomeMiddleware);
```

```
[req] => Request Object
(
    [data:Request:private] => Array
    (
        [method] => GET
        [path] => /
        [cookies] => Array()
        [params] => Array
            (
                [GET] => Array()

            )

        [somemiddleware] => SomeMiddleware Object
        (
                [data:SessionsMiddleware:private] => Array
                    (
                        [example] => Example Middleware
                    )

                [data:Request:private] => 
        )
    )
)
```