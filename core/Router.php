<?php

    require_once('./core/utils/Singleton.php');
    require_once('./core/MiddleWare.php');
    
    require_once('Request.php');
    require_once('Response.php');

    class Router
    {
        private static $ROUTES;
        private static $MIDDLEWARE;

        private static $REQUEST_METHOD;
        private static $REQUEST_URI;

        public function __construct() {}

        public static function add(string $route, $funcs, string $method) 
        {
            self::$ROUTES[$method][$route] = [
                'route' => $route,
                'funcs' => $funcs,
                'urlVars' => self::extractURLVars($route)
            ];
        }

        public static function addMiddleWare(MiddleWare $middleWare) 
        {
            self::$MIDDLEWARE[] = $middleWare;
        }

        public static function getRoutes(): array 
        {
            return self::$ROUTES;
        }

        private static function extractURLVars(string $route): array 
        {
            $routeParts = explode('/', $route);
            $params = [];

            foreach($routeParts as $key => $routePart) 
            {
                if (strpos($routePart, ':') === 0) 
                {
                    $paramName = substr($routePart, 1);
                    $params[$paramName] = '';
                }
            }

            return $params;
        }

        private static function execute(array $route, array $params = null)
        {   
            $req = new Request(self::$REQUEST_METHOD, self::$REQUEST_URI, $params, self::$MIDDLEWARE);
            $res = new Response();
            
            if (! empty(self::$MIDDLEWARE)) 
            {
                foreach(self::$MIDDLEWARE as $middleWare) 
                {
                    $middleWare($req, $res);
                }
            } 
            
            foreach($route['funcs'] as $func) 
            {
                $func($req, $res);
            }
        }

        public static function match(string $path, string $method)
        {
            self::$REQUEST_METHOD = $method;
            self::$REQUEST_URI = $path;

            if (array_key_exists($path, self::$ROUTES[$method])) {
                return self::execute(self::$ROUTES[$method][$path]);
            } else {

                foreach(self::$ROUTES[$method] as $key => $route) 
                {
                    
                    if (empty($route['urlVars'])) 
                    {
                        continue;
                    }

                    $purePath = substr($path, 1);
                    $pureRoute = substr($route['route'], 1);
                    
                    $pathParts = explode('/', $purePath);
                    $routeParts = explode('/', $pureRoute);

                    if ((count($pathParts) - 1) === count($route['urlVars'])) 
                    {
                        if ($pathParts[0] === $routeParts[0]) 
                        {
                            unset($pathParts[0]);
                            
                            $i = 1;
                            foreach($route['urlVars'] as $key => $urlVar) 
                            {
                                $route['urlVars'][$key] = $pathParts[$i];
                                $i++;
                            }

                            return self::execute($route, $route['urlVars']);
                        }   
                    }
                }
            }

            // No match
            $res = new Response();
            $res->error([
                'status_code' => 404, 
                'message' => "Pretty sure that route does not exist. Duh!"
            ], 404);
        }
    }

?>