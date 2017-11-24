<?php 

    require_once('./database/Model.php');

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

        public static function findById(int $tagId, string $idD = 'id') {
            $query = 'SELECT DISTINCT t.id, pt.post_id as post_id 
            FROM tags t
                LEFT JOIN post_tags pt ON t.id = pt.tag_id
            WHERE t.id = :tagId
            GROUP BY post_id';

            $statement = static::getDB()->prepare($query);
            $statement->bindValue(':tagId', $tagId);
            $statement->execute();

            $posts = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (! empty($posts[0]['post_id'])) {
                return $posts;
            } else {
                return [];
            }

        }

    }

    PostTags::register();

?>