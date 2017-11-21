<?php

    require_once('./core/Connection.php');

    abstract class Model
    {

        protected static $db;

        public static function init()
        {
            self::$db = Connection::getInstance();
        }

        public static function getDB()
        {
            return self::$db->handler;
        }

        public static function findAll()
        {
            try {
                $statement = self::getDB()->query('SELECT * from ' . static::$tableName);
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    
                if (! empty($results)) {
                    return $results;
                } else {
                    throw new Exception("Could not fetch results.");
                }

            } catch (PDOException $err) {
                return $err->getMessage();
            }
        }

        public static function find(array $params)
        {
            try {
                
                $paramLength = count($params);
                $paramsToLookFor = '';
                $i = 0;

                foreach($params as $key => $param) {
                    if ($i === $paramLength - 1) {
                        $paramsToLookFor .= $key . ' = :' . $key;
                    } else {
                        $paramsToLookFor .= $key . ' = :' . $key . ' AND ';
                    }
                    $i++;
                }

                $sql = (
                    'SELECT * FROM ' . static::$tableName . ' WHERE ' . $paramsToLookFor
                );

                
                $statement = self::getDB()->prepare($sql);

                foreach($params as $key => $param) {
                    $params[':' . $key] = $param;
                    unset($params[$key]);
                }

                if ($statement->execute($params)) {

                    if ($statement->rowCount() > 1) {
                        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $results = $statement->fetch(PDO::FETCH_ASSOC);
                    }

                    return $results;

                } else {
                    throw new Exception ("That's an error.");
                }

            } catch (PDOException $err) {
                self::getDB()->rollBack();
                return $err->getMessage();
            }
        }

        public static function findById(int $id)
        {
            try {
                $sql = 'SELECT * FROM ' . static::$tableName . ' WHERE id = :id';
                $statement = self::getDB()->prepare($sql);
                $statement->execute([':id' => $id]);
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                if (! empty($results)) {
                    return $results;
                } else {
                    throw new Exception ("That's an error.");
                }

            } catch (PDOException $err) {
                return $err->getMessage();
            }
        }

        public static function findOneById(int $id)
        {
            try {
                $sql = 'SELECT * FROM ' . static::$tableName . ' WHERE id = :id LIMIT 1';
                $statement = self::getDB()->prepare($sql);
                $statement->execute([':id' => $id]);
                $result = $statement->fetch(PDO::FETCH_ASSOC);  

                if (! empty($result)) {
                    return $result;
                } else {
                    throw new Exception ("That's an error.");
                }

            } catch (PDOException $err) {
                return $err->getMessage();
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
                
                self::getDB()->beginTransaction();
                $statement = self::getDB()->prepare($sql);
                
                foreach($params as $key => $param) {
                    $params[':' . $key] = $param;
                    unset($params[$key]);
                }
                
                $params[':' . $idDenominator] = $id;

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
                return $err->getMessage();
            }
        }

        public static function delete(array $params)
        {

            try {
                
                $paramLength = count($params);
                $paramsToLookFor = '';
                $i = 0;

                foreach($params as $key => $param) {
                    if ($i === $paramLength - 1) {
                        $paramsToLookFor .= $key . ' = :' . $key;
                    } else {
                        $paramsToLookFor .= $key . ' = :' . $key . ' AND ';
                    }
                    $i++;
                }

                $sql = (
                    'DELETE FROM ' . static::$tableName . ' WHERE ' . $paramsToLookFor
                );

                $statement = self::getDB()->prepare($sql);

                foreach($params as $key => $param) {
                    $params[':' . $key] = $param;
                    unset($params[$key]);
                }

                if ($statement->execute($params)) {

                    return $statement->rowCount();

                } else {
                    throw new Exception ("That's an error.");
                }

            } catch (PDOException $err) {
                return $err->getMessage();
            }
        }

        public static function deleteOneById(int $id)
        {
            try {
                $sql = 'DELETE FROM ' . static::$tableName . ' WHERE id = :id LIMIT 1';
                $statement = self::getDB()->prepare($sql);
                $statement->execute([':id' => $id]);
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

                $paramsToSave = '';
                $paramsToSavePlaceholders = '';

                if ($paramsLength === 1) {
                    $paramsToSave .= '(' . key($params) . ')';
                    $paramsToSavePlaceholders .= '(:' . key($params) . ')';
                } else {
                    foreach($params as $key => $param) {
                        if ($i === 0) {
                            $paramsToSave .= '(' . $key . ', ';
                            $paramsToSavePlaceholders .= '(:' . $key . ', ';
                        } elseif ($i === $paramsLength - 1) {
                            $paramsToSave .= $key . ')';
                            $paramsToSavePlaceholders .= ':' . $key . ')';
                        } else {
                            $paramsToSave .= $key . ', ';
                            $paramsToSavePlaceholders .= ':' . $key . ', ';
                        }
                      
                        $i++;
                    }
                }

                $sql = (
                    'INSERT INTO ' . static::$tableName . 
                    ' ' .
                    $paramsToSave . 
                    ' VALUES ' . 
                    $paramsToSavePlaceholders
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
                } else {
                    throw new Exception('Item could not be inserted.');
                }

            } catch (Exception $err) {
                self::getDB()->rollBack();
                return $err->getMessage();
            }
        }

        public static function getTableName(): string { return static::$tableName; }
        abstract static public function register();

    }

    Model::init();

?>
