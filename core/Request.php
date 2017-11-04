<?php

    class Request {

        private $data;

        public function __construct(
            string $method, 
            string $path, 
            ...$middleWares
        ) 
        {
            $this->data['method'] = $method;
            $this->data['path'] = $path;
            $this->data['cookies'] = $_COOKIE;

            switch ($method) {
                case 'GET': $this->data['params'] = $_GET; break;
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

            throw new Exception;
        }
    }

?>