<?php 

    require_once('./core/Router.php');

    class App
    {
        private static $router;
        
        public function __construct() 
        {
            $router = Router::getInstance();
        }

        public function get(string $route, $func, string $method = 'GET') 
        {
            router::add($route, $func, $method);
        }

        public function post(string $route, $func)   { return $this->get($route, $func, 'POST'); }
        public function put(string $route, $func)    { return $this->get($route, $func, 'PUT'); }
        public function delete(string $route, $func) { return $this->get($route, $func, 'DELETE'); }
        
        public function start() {
            router::match($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
        }
    }
?>