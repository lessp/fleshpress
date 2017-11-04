<?php 

    require_once('./core/Model.php');

    class Post extends Model
    {

        protected static $tableName;
        protected $schema;

        public function __construct(string $title, string $content, int $userId)
        {
            $this->schema = [
                'title' => $title,
                'content' => $content,
                'user_id' => $userId
            ];
        }

        public static function register()
        {
            static::$tableName = 'posts';
        }

    }

    Post::register();

?>