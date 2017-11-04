<?php

    class Request {

        private $data;

        public function __construct(
            string $method, 
            string $path, 
            array $urlVars = null,
            ...$middleWares
        ) 
        {
            $this->data['method'] = $method;
            $this->data['path'] = $path;
            $this->data['cookies'] = $_COOKIE;

            if (! empty($urlVars)) {
                foreach($urlVars as $key => $urlVar) {
                    $this->data['params'][$key] = $urlVar;
                }
            }

            switch ($method) {
                case 'GET': $this->data['params']['GET'] = $_GET; break;
                case 'POST': $this->data['body'] = $_POST; break;
                case 'PUT': /* TODO */; break;
                case 'DELETE': /* TODO */; break;
            }
            
            foreach($middleWares as $middleWare) {
                foreach($middleWare as $class) {
                    $this->data[strtolower(get_class($class))] = $class;
                }
            }
        }

        public function __set($name, $value)
        {
            $this->data[$name] = $value;
        }

        public function __get($name)
        {

            if (array_key_exists($name, $this->data)) {
                return $this->data[$name];
            }

            // throw new Exception;
            return null;
        }
    }

?>