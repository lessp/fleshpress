<?php

    use PDO;

    abstract class Model
    {

        protected static function getDB()
        {
            static $db = null;

            if ($db === null)
            {

                try {

                    $config = require_once('./config/config.php');
                    $dsn = $config['db']['dsn'];
                    
                    $db = new PDO($dsn, $config['db']['user'], $config['db']['password']);
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    return $db;

                } catch (PDOException $err) {
                    echo $err->getMessage();
                }

            }
        }

        public function findAll()
        {
            $list = [];

            $req = $this->getDB()->query('SELECT * from $this->tableName');

            foreach($req->fetchAll() as $item)
            {
                $list[] = $item;
            }

            return $list;
        }

        abstract public static function setTableName(string $tableName);

    }

?>