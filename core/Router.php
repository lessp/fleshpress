<?php

    require_once('./utils/Singleton.php');
    require_once('Request.php');
    require_once('Response.php');

    class Router extends Singleton
    {
        private static $ROUTES;

        private static $REQUEST_METHOD;
        private static $REQUEST_URI;

        public function __construct()
        {}

        public static function add(string $route, $funcs, string $method) 
        {
            self::$ROUTES[$method][] = [
                'route' => $route,
                'funcs' => $funcs,
                'urlVars' => self::extractURLVars($route)
            ];
        }

        public static function getRoutes(): array {
            return self::$ROUTES;
        }

        private static function extractURLVars(string $route): array 
        {
            $routeParts = explode('/', $route);
            $params = [];

            foreach($routeParts as $key => $routePart) {
                if (strpos($routePart, ':') === 0) {
                    $paramName = substr($routePart, 1);
                    $params[$paramName] = '';
                }
            }

            return $params;
        }

        private static function execute(array $route, array $params = null)
        {
            $req = new Request(self::$REQUEST_METHOD, self::$REQUEST_URI);
            $res = new Response();

            foreach($route['funcs'] as $func) {
                $func($req, $res, $params);
            }
        }

        public static function match(string $path, string $method)
        {
            self::$REQUEST_METHOD = $method;
            self::$REQUEST_URI = $path;

            foreach(self::$ROUTES[$method] as $key => $route) {

                if (empty($route['urlVars'])) {
                    if ($path === $route['route']) {
                        self::execute($route);
                    }
                } else {
                    return self::execute($route, $route['urlVars']);
                }

            }
        }
    }

?>