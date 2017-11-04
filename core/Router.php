<?php

    require_once('./utils/Singleton.php');
    require_once('Request.php');
    require_once('Response.php');

    class Router extends Singleton
    {
        private static $ROUTES;
        private static $MIDDLEWARE;

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

        public static function addMiddleWare($middleWare) 
        {
            self::$MIDDLEWARE[] = $middleWare;
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
            $req = new Request(self::$REQUEST_METHOD, self::$REQUEST_URI, self::$MIDDLEWARE);
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
                        return self::execute($route);
                    }
                } else {
                    $purePath = substr($path, 1);
                    $pureRoute = substr($route['route'], 1);

                    $pathParts = explode('/', $purePath);
                    $routeParts = explode('/', $pureRoute);

                    if ((count($pathParts) - 1) === count($route['urlVars'])) {
                        if ($pathParts[0] === $routeParts[0]) {

                            unset($pathParts[0]);

                            $i = 1;
                            foreach($route['urlVars'] as $key => $urlVar) {
                                $route['urlVars'][$key] = $pathParts[$i];
                                $i++;
                            }

                            return self::execute($route, $route['urlVars']);
                        }   
                    }

                }

            }

            $res = new Response();
            return $res->send('ERROR: Not found', 404);
        }
    }

?>