<?php 

    require_once('./database/Model.php');

    class Tags extends Model
    {

        protected static $tableName;
        protected $schema;

        public function __construct(string $tagName)
        {
            $this->schema = [
                'name' => $tagName
            ];
        }

        public static function register()
        {
            static::$tableName = 'tags';
        }

    }

    Tags::register();

?>