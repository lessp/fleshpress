<?php

    require_once('./core/Connection.php');

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

        public static function findByIdAndUpdate(int $id, $params, string $idDenominator = 'id') 
        {

            try {

                $paramLength = count($params);
                $paramsToUpdate = '';
                $paramsToUpdatePlaceholders = '';

                $i = 0;
                foreach($params as $key => $param) {
                    if ($i === $paramLength - 1) {
                        $paramsToUpdate .= $key . ' = :' . $key;
                    } else {
                        $paramsToUpdate .= $key . ' = :' . $key . ', ';
                    }
                    $i++;
                }

                $sql = (
                    'UPDATE ' . static::$tableName . 
                    ' SET ' .
                    $paramsToUpdate . 
                    ' WHERE ' . $idDenominator . ' = :' . $idDenominator
                );

                echo '<pre>';
                    print_r('theSQLQuery: ' . $sql);
                echo '</pre>';

                self::getDB()->beginTransaction();
                $statement = self::getDB()->prepare($sql);

                foreach($params as $key => $param) {
                    $params[':' . $key] = $param;
                    unset($params[$key]);
                }
                
                $params[':' . $idDenominator] = $id;
                echo '<pre>';
                    print_r($params);
                echo '</pre>';

                $statement->execute($params);

                self::getDB()->commit();

                $updatedItems = $statement->rowCount();

                if ($updatedItems > 0) {
                    return true;
                }

            } catch (PDOException $err) {
                self::getDB()->rollBack();
                echo $err->getMessage();
            }
        }

        public static function setTableName(string $tableName) { static::$tableName = $tableName; }
        public static function getTableName(): string { return static::$tableName; }

    }

    Model::init();

?>
