<?php 

    require_once('./core/Model.php');

    class Post extends Model
    {

        protected static $tableName;

        private $title;
        private $content;

        public function __construct(string $title, string $content)
        {
            $this->title = $title;
            $this->content = $content;
        }

        public function save()
        {

            if (! isset($this->title) || ! isset($this->content)) return;

            try {
                $statement = self::getDB()->prepare(
                    'INSERT INTO ' . static::$tableName . ' (title, content) VALUES (:title, :content)'
                );
                print_r($statement);
                $statement->bindValue(':title', $this->title);
                $statement->bindValue(':content', $this->content);


                $result = $statement->execute();

                return $result;
            } catch (PDOException $err) {
                echo $err->getMessage();
            }
        }

    }

    Post::setTableName('posts');

?>