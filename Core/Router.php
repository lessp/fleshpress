<?php

    require_once('Request.php');
    require_once('Utils.php');

    class Router
    {
        private static $GET_ROUTES;
        private static $POST_ROUTES;

        private $req;

        public function __construct(Request $req)
        {
            self::$GET_ROUTES = [];
            self::$POST_ROUTES = [];

            $this->req = $req;
        }

        public function get(string $path, $func)
        {
            if ($this->req->isPOST()) return;

            // TODO: support /path/:id
            // echo '<pre>';
            //     print_r(explode(':', $path));
            // echo '</pre>';

            self::$GET_ROUTES[] = [
                'path' => $path,
                'function' => $func
            ];

            // echo '<pre>';
            //     print_r(['GET_ROUTES' => self::$GET_ROUTES]);
            // echo '</pre>';

            $route = $this->match(self::$GET_ROUTES);

            $this->execute($route);
        }

        public function post(string $path, $func)
        {
            if ($this->req->isGET()) return;

            self::$POST_ROUTES[] = [
                'path' => $path,
                'function' => $func
            ];

            // echo '<pre>';
            //     print_r(['POST_ROUTES' => self::$POST_ROUTES]);
            // echo '</pre>';

            $route = $this->match(self::$POST_ROUTES);

            $this->execute($route);
        }

        private function execute($route)
        {
            if (! isset($route)) {
                return render_response(404, 'Not found');
            }

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