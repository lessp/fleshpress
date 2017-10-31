<?php 

    require_once('./core/Model.php');

    class Post extends Model
    {

        static $tableName;

        private $title;
        private $content;

        public function __construct(string $title, string $content)
        {
            $this->title = $title;
            $this->content = $content;
        }

        public function save()
        {
            // sql statement here..
        }

        public static function setTableName(string $tableName)
        {
            self::$tableName = $tableName;
        }

    }

?>