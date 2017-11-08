<?php 

    require_once('./core/Model.php');

    class PostTags extends Model
    {

        protected static $tableName;
        protected $schema;

        public function __construct(int $postId, int $tagId)
        {
            $this->schema = [
                'post_id' => $postId,
                'tag_id' => $tagId
            ];
        }

        public static function register()
        {
            static::$tableName = 'post_tags';
        }

    }

    PostTags::register();

?>