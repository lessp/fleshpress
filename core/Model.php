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

        public static function findOneById(int $id)
        {
            try {
                $sql = 'SELECT * FROM ' . static::$tableName . ' WHERE id = :id LIMIT 1';
                $statement = self::getDB()->prepare($sql);
                $statement->execute([':id' => $id]);
                $result = $statement->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    return $result;
                }

            } catch (PDOException $err) {
                echo $err->getMessage();
            }
        }

        /**
         *
         * Finds an item and updates it with corresponding table parameters
         *
         * @param int table itemId
         * @param array parameters to update
         * @param bool whether to return the updated item
         * @param string if id-identifer is different than 'id'
         */
        public static function findByIdAndUpdate(
            int $id, 
            array $params, 
            bool $returnUpdatedItem = false,
            string $idDenominator = 'id'
        ) 
        {

            try {

                $paramLength = count($params);
                $paramsToUpdate = '';
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

                // echo '<pre>';
                //     print_r('theSQLQuery: ' . $sql);
                // echo '</pre>';

                self::getDB()->beginTransaction();
                $statement = self::getDB()->prepare($sql);

                foreach($params as $key => $param) {
                    $params[':' . $key] = $param;
                    unset($params[$key]);
                }
                
                $params[':' . $idDenominator] = $id;
                // echo '<pre>';
                //     print_r($params);
                // echo '</pre>';

                $statement->execute($params);

                self::getDB()->commit();

                $updatedItems = $statement->rowCount();

                if ($updatedItems > 0) {
                    if ($returnUpdatedItem) 
                    {
                        return self::findOneById($id);
                    } else {
                        return true;
                    }
                }

            } catch (PDOException $err) {
                self::getDB()->rollBack();
                echo $err->getMessage();
            }
        }


        /**
         *
         * Saves an item based on a Models schema and returns the newly saved item
         *
         */
        public function save()
        {
            $params = $this->schema;
            
            try {

                $i = 0;
                $paramsLength = count($params);
                $paramsToUpdate;
                $paramsToUpdatePlaceholders;
                foreach($params as $key => $param) {
                    if ($i === 0) {
                        $paramsToUpdate .= '(' . $key . ', ';
                        $paramsToUpdatePlaceholders .= '(:' . $key . ', ';
                    } elseif ($i === $paramsLength - 1) {
                        $paramsToUpdate .= $key . ')';
                        $paramsToUpdatePlaceholders .= ':' . $key . ')';
                    } else {
                        $paramsToUpdate .= $key . ', ';
                        $paramsToUpdatePlaceholders .= ':' . $key . ', ';
                    }
                  
                    $i++;
                }

                $sql = (
                    'INSERT INTO ' . static::$tableName . 
                    ' ' .
                    $paramsToUpdate . 
                    ' VALUES ' . 
                    $paramsToUpdatePlaceholders
                );
                  
                self::getDB()->beginTransaction();
                $statement = self::getDB()->prepare($sql);

                foreach($params as $key => $param) {
                    $params[':' . $key] = $param;
                    unset($params[$key]);
                }

                $statement->execute($params);

                $newItemId = self::getDB()->lastInsertId();
                self::getDB()->commit();

                if ($newItemId !== 0) {
                    return self::findOneById($newItemId);
                }

            } catch (PDOException $err) {
                self::getDB()->rollBack();
                return $err->getMessage();
            }
        }

        public static function setTableName(string $tableName) { static::$tableName = $tableName; }
        public static function getTableName(): string { return static::$tableName; }

    }

    Model::init();

?>
