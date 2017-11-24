<?php 

    require_once('./database/Model.php');

    class PostModel extends Model
    {

        protected static $tableName;
        protected $schema;

        public function __construct(string $title, string $content)
        {
            $this->schema = [
                'title' => $title,
                'content' => $content
            ];
        }

        public static function register()
        {
            static::$tableName = 'posts';
        }

    }

    PostModel::register();

?>