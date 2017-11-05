# 05-dynamisk-webbplats-php-lessp

My first PHP assignment in Chas Academy - Fullstack Developer.

___

A very basic `PHP` MVC framework inspired by `Express (NodeJS)` and `Flask (Python)`. 

## Basic Route Example

```php
$app = new App();

$app->get('/', function($req, $res) {
    $res->send('Hello World!', 200);
});
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

```
$app->get('/protected', 'requireLogin', function($req, $res) {
    $res->render_template('protected.html');
});

function requireLogin($req, $res) {
    $isAuthed = false;

    if (! $isAuthed) {
        $res->json(["message" => "Oops. It seems as though you're not logged in.], 401);
    }
}
```
