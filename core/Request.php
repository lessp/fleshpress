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

        $trace = debug_backtrace();
            trigger_error(
                'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE
            );

            return null;
        }
    }

?>