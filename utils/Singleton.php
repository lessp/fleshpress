<?php 

    trait Singleton 
    {
        private static $instances = array();

        protected function __construct() 
        {}

        public static function getInstance()
        {
            $class = get_called_class();

            if (! isset(self::$instances[$class]))
            {
               self::$instances[$class] = new static();
            }

            return self::$instances[$class];
            
        }
    }

?>