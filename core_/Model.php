<?php

    require_once('./core/Connection.php');

    // use \PDO;

    abstract class Model
    {

        protected static $db;

        public static function init()
        {
            self::$db = Connection::getInstance();
        }

        protected static function getDB()
        {
            return self::$db->handler;
        }

        public static function findAll()
        {
            try {
                $statement = self::getDB()->query('SELECT * from ' . static::$tableName);
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    
                if ($results) {

                    $list = [];
                    foreach($results as $item) {
                        $list[] = $item;
                    }
    
                    return $list;
                }

            } catch (PDOException $err) {
                echo $err->getMessage();
            }
        }

        public static function findById(int $id)
        {
            try {
                $sql = 'SELECT * FROM ' . static::$tableName . ' WHERE id = :id';
                $statement = self::getDB()->prepare($sql);
                $statement->execute([':id' => $id]);
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                if ($results) {
                    return $results;
                }

            } catch (PDOException $err) {
                echo $err->getMessage();
            }
        }

        public static function findByIdAndUpdate(int $id, $params) {
            // 
        }

        public static function setTableName(string $tableName) { static::$tableName = $tableName; }
        public static function getTableName(): string { return static::$tableName; }

    }

    Model::init();

?>
