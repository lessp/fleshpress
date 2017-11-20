<?php 

    require_once('./core/Model.php');

    require_once('./models/PostTagsModel.php');
    require_once('./models/PostCategoryModel.php');

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

        public static function findAll() 
        {
            $statement = Post::getDB()->query(
                'SELECT DISTINCT p.id, p.title, p.content, p.user_id,
                        GROUP_CONCAT(DISTINCT "{\"id\":", pc.category_id, ",", "\"name\":", "\"", c.name, "\"}") as categories,
                        GROUP_CONCAT(DISTINCT "{\"id\":", pt.tag_id, ",", "\"name\":", "\"", t.name, "\"}") as tags,
                        GROUP_CONCAT(DISTINCT u.first_name, " ", u.last_name) as author
                    FROM posts p
                        LEFT JOIN post_categories pc ON p.id = pc.post_id
                        LEFT JOIN categories c ON pc.category_id = c.id
                        LEFT JOIN post_tags pt ON p.id = pt.post_id
                        LEFT JOIN tags t ON pt.tag_id = t.id
                        LEFT JOIN users u ON p.user_id = u.id
                GROUP BY p.id'
            );
    
            $statement->execute();
            $posts = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach($posts as &$post) {
                $post['categories'] = json_decode('[' . $post['categories'] . ']', true);
                $post['tags'] = json_decode('[' . $post['tags'] . ']', true);
            }
            
            return $posts;
        }

        public static function findOneById(int $postId)
        {
            $query = 'SELECT DISTINCT p.id, p.title, p.content, p.user_id, u.first_name, u.last_name,
                        GROUP_CONCAT(DISTINCT "{\"id\":", pc.category_id, ",", "\"name\":", "\"", c.name, "\"}") as categories,
                        GROUP_CONCAT(DISTINCT "{\"id\":", pt.tag_id, ",", "\"name\":", "\"", t.name, "\"}") as tags
                    FROM posts p
                        LEFT JOIN post_categories pc ON p.id = pc.post_id
                        LEFT JOIN categories c ON pc.category_id = c.id
                        LEFT JOIN post_tags pt ON p.id = pt.post_id
                        LEFT JOIN tags t ON pt.tag_id = t.id
                        LEFT JOIN users u ON p.user_id = u.id
                    WHERE p.id = :postId';

            $statement = static::getDB()->prepare($query);
            $statement->bindValue(':postId', $postId);
            $statement->execute();
            
            $post = $statement->fetch(PDO::FETCH_ASSOC);

            $post['categories'] = json_decode('[' . $post['categories'] . ']', true);
            $post['tags'] = json_decode('[' . $post['tags'] . ']', true);
            
            return $post;
        }

        public static function deleteOneById(int $postId) {
            try {
                $sql = 'DELETE FROM ' . PostCategory::getTableName() . ' WHERE post_id = :id';
                $statement = self::getDB()->prepare($sql);
                $statement->execute([':id' => $postId]);
                $result = $statement->rowCount();  

                $sql = 'DELETE FROM ' . PostTags::getTableName() . ' WHERE post_id = :id';
                $statement = self::getDB()->prepare($sql);
                $statement->execute([':id' => $postId]);
                $result = $statement->rowCount();  

                $sql = 'DELETE FROM ' . static::$tableName . ' WHERE id = :id';
                $statement = self::getDB()->prepare($sql);
                $statement->execute([':id' => $postId]);
                $result = $statement->rowCount();

                if ($result > 0) {
                    return true;
                } else {
                    throw new Exception ("That's an error.");
                }

            } catch (PDOException $err) {
                return $err->getMessage();
            }
        }

    }

    Post::register();

?>