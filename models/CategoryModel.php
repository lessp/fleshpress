<?php 

    require_once('./database/Model.php');

    class Category extends Model
    {

        protected static $tableName;
        protected $schema;

        public function __construct(string $categoryName)
        {
            $this->schema = [
                'name' => $categoryName
            ];
        }

        public static function register()
        {
            static::$tableName = 'categories';
        }

    }

    Category::register();

?>