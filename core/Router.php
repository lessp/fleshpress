<?php

    require_once('Request.php');
    require_once('Response.php');

    class Router
    {
        private static $ROUTES;

        private $req;

        public function __construct()
        {
            self::$ROUTES = [
                'GET' => [], 
                'POST' => [],
                'PUT' => []
            ];

            $this->req = new Request($_SERVER);
        }

        public function get(string $route, $func, string $method = 'GET')
        {
            self::$ROUTES[$method][] = [
                'route' => $route,
                'function' => $func,
                'params' => $this->extractParams($route, $this->req->path()) // sätt bara param
            ];
        }

        public function post(string $route, $func) { return $this->get($route, $func, 'POST'); }
        public function put(string $route, $func) { return $this->get($route, $func, 'PUT'); }

        private function execute(array $route, array $params = null)
        {
            if (! isset($route)) {
                $res = new Response();
                return $res->send('ERROR: Not found', 404);
            }

            // if ($params !== null) {
            //     $this->req->setParams($params);
            // }

            $route['function'] (
                $this->req,
                new Response(),
                $params
            );
        }

        private function match(array $routes, string $method)
        {
            foreach($routes[$method] as $key => $route)
            {
                if (empty($route['params'])) {

                    if ($this->req->path() === $route['route'])
                    {
                        return $this->execute($route);
                    }
                } else {
                    // TODO: see if path matches
                    return $this->execute($route, $route['params']);
                }
            }
        }

        private function extractParams(string $route, string $path) 
        {
            $routeParts = explode('/', $route);
            $pathParts = explode('/', $path);
            $params = [];
            
            foreach($routeParts as $key => $routePart) {
                if (strpos($routePart, ':') === 0) {
                    $paramName = substr($routePart, 1);
                    $params[$paramName] = $pathParts[$key];
                }
            }

            return $params;
        }

        public function start()
        {
            $this->match(self::$ROUTES, $this->req->method());
        }

    }

?>