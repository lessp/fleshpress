<?php

    require_once('Request.php');
    require_once('Utils.php');

    class Router
    {
        private static $GET_ROUTES;
        private static $POST_ROUTES;
        private static $ROUTES;

        private $req;

        public function __construct(Request $req)
        {
            self::$ROUTES = ['GET' => [], 'POST' => []];

            $this->req = $req;
        }

        public function get(string $path, $func)
        {
            if ($this->req->isPOST()) return;

            self::$ROUTES['GET'][] = [
                'path' => $path,
                'function' => $func
                // 'params' => $this->extractParams($path)
            ];
        }

        public function post(string $path, $func)
        {
            if ($this->req->isGET()) return;

            self::$ROUTES['POST'][] = [
                'path' => $path,
                'function' => $func
            ];
        }

        private function execute($route)
        {
            if (!isset($route)) {
                return render_response(404, 'Not found');
            }

            $route['function']($this->req->getParams());
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