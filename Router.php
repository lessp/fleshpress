<?php

    require_once('Request.php');

    class Router
    {
        private static $getRoutes;
        private static $postRoutes;

        private $method;
        private $uri;
        private $req;

        public function __construct(Request $req)
        {
            self::$getRoutes = [];
            self::$postRoutes = [];

            $this->uri = $req->getPath();

            if ($req->isGET()) {
                $this->method = 'GET';
                $this->req = $_GET;
            } elseif ($req->isPOST()) {
                $this->method = 'POST';
                $this->req = $_POST;
            }
        }

        public function get(string $path, $func)
        {
            if ($this->method === 'POST') return;

            self::$getRoutes[] = [
                'path' => $path,
                'function' => $func
            ];

            $route = $this->match(self::$getRoutes);
            if ($route)
                $this->execute($route);
        }

        public function post(string $path, $func)
        {
            if ($this->method === 'GET') return;

            self::$postRoutes[] = [
                'path' => $path,
                'function' => $func
            ];

            $route = $this->match(self::$postRoutes);
            if ($route)
                $this->execute($route);
        }

        private function execute(array $route)
        {
            $route['function']($this->req);
        }

        private function match(array $routes)
        {
            print_r($routes);
            foreach($routes as $key => $route)
            {
                if ($this->uri === $route['path'])
                {
                    return $route;
                }
            } 
        }

    }

?>