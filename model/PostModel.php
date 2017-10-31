<?php 

    class Post extends Model
    {

        public function __construct()
        {
            
        }

        protected function _exec($q)
        {
            print_r($q);
        }

    }

?>