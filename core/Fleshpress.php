<?php 

    require_once('./core/Router.php');

    class Fleshpress
    {
        private static $router;
        
        public function __construct() 
        {
            $router = Router::getInstance();
        }

        public function get(string $route, ...$funcs) {
            router::add($route, $funcs, 'GET');
        }

        public function post(string $route, ...$funcs)   { router::add($route, $funcs, 'POST'); }
        public function put(string $route, ...$funcs)    { router::add($route, $funcs, 'PUT'); }
        public function delete(string $route, ...$funcs) { router::add($route, $funcs, 'DELETE'); }

        public function use($middleWare) {
            router::addMiddleWare($middleWare);
        }
        
        public function start() {
            // shame
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            if (isset($_POST['_method'])) {
                $requestMethod = $_POST['_method'];
            };

            router::match($_SERVER['REQUEST_URI'], $requestMethod);
        }
    }
?>