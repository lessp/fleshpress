<?php

    /**
     * Abstract model
     * 
     * Används endast för att forma SQL-queries (generella)
     * Klassmodellerna som ärver denna får hantera sin egen db-abstraktion via _perform
     */
    abstract class Model
    {
        protected $table;
        protected $columns;
        protected $primaryKey;
        
        public function findAll()
        {
            $q = sprintf(
                'SELECT * FROM %s',
                $this->table
            );

            return $this->_exec($q);
        }
        
        public function findById($id){
            $q = sprintf(
                'SELECT * FROM %s WHERE id=%s', 
                $this->table,
                $id
            );

            return $this->_exec($q);   
        }
        
        abstract protected function _exec($q);
    }

?>