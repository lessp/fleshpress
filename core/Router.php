<?php

    require_once('Request.php');
    require_once('Response.php');
    require_once('./utils/Utils.php');

    class Router
    {
        private static $GET_ROUTES;
        private static $POST_ROUTES;
        private static $ROUTES;

        private $req;

        public function __construct(Request $req)
        {
            self::$ROUTES = [
                'GET' => [], 
                'POST' => [],
                'PUT' => []
            ];

            $this->req = $req;
        }

        public function get(string $path, $func, string $method = 'GET')
        {
            self::$ROUTES[$method][] = [
                'path' => $path,
                'function' => $func
                // 'params' => $this->extractParams($path)
            ];
        }

        public function post(string $path, $func) { return $this->get($path, $func, 'POST'); }
        public function put(string $path, $func) { return $this->get($path, $func, 'PUT'); }

        private function execute($route)
        {
            if (! isset($route)) {
                return render_response(404, 'Not found');
            }

            $route['function'](
                $this->req, 
                new Response()
            );
        }

        private function match(array $routes, string $method)
        {
            foreach($routes[$method] as $key => $route)
            {
                if ($this->req->getPath() === $route['path'])
                {
                    return $route;
                }
            }
        }

        private function extractParams($path)
        {
            $pathParts = explode('/', $path);
            $params = [];

            foreach($pathParts as $key => $part)
            {
                if (strpos($part, ':') === 0) {
                    $name = substr($part, 1);
                    $params[$name] = $pathParts[$key+1];
                }
            }
        }

        public function start()
        {
            $route = $this->match(self::$ROUTES, $this->req->getMethod());

            $this->execute($route);
        }

    }

?>