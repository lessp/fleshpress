<?php 

    require_once('./database/Model.php');

    class PostCategory extends Model
    {

        protected static $tableName;
        protected $schema;

        public function __construct(int $postId, int $categoryId)
        {
            $this->schema = [
                'post_id' => $postId,
                'category_id' => $categoryId
            ];
        }

        public static function register()
        {
            static::$tableName = 'post_categories';
        }

    }

    PostCategory::register();

?>