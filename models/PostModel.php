<?php 

    require_once('./core/Model.php');

    class Post extends Model
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

    }

    Post::setTableName('posts');

?>