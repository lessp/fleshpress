<?php

    require_once('Request.php');
    require_once('Utils.php');

    class Router
    {
        private static $getRoutes;
        private static $postRoutes;

        private $req;

        public function __construct(Request $req)
        {
            self::$getRoutes = [];
            self::$postRoutes = [];

            $this->req = $req;
        }

        public function get(string $path, $func)
        {
            if ($this->req->isPOST()) return;

            // TODO: support /path/:id
            // echo '<pre>';
            //     print_r(explode(':', $path));
            // echo '</pre>';

            self::$getRoutes[] = [
                'path' => $path,
                'function' => $func
            ];

            // echo '<pre>';
            //     print_r(['getRoutes' => self::$getRoutes]);
            // echo '</pre>';

            $route = $this->match(self::$getRoutes);

            $this->execute($route);
        }

        public function post(string $path, $func)
        {
            if ($this->req->isGET()) return;

            self::$postRoutes[] = [
                'path' => $path,
                'function' => $func
            ];

            // echo '<pre>';
            //     print_r(['postRoutes' => self::$postRoutes]);
            // echo '</pre>';

            $route = $this->match(self::$postRoutes);

            $this->execute($route);
        }

        private function execute($route)
        {
            if (! isset($route)) return;

            $route['function']($this->req->getParams());
        }

        private function match(array $routes)
        {
            foreach($routes as $key => $route)
            {
                if ($this->req->getPath() === $route['path'])
                {
                    return $route;
                }
            }

            return null;
        }

    }

?>