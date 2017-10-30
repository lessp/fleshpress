<?php

    require_once('Request.php');

    class Router
    {
        private $routes;
        private $getRoutes;
        private $postRoutes;

        private $method;
        private $uri;
        private $req;

        public function __construct(Request $req)
        {
            $this->routes = [];
            $this->uri = $req->getPath();

            if ($req->isGET()) {
                $this->method = 'GET';
                $this->req = $_GET;
            } elseif ($req->isPOST()) {
                $this->method = 'POST';
                $this->req = $_POST;
            }
        }

        public function route(string $route, array $methods, $func)
        {
            $this->routes[] = [
                'path' => $route,
                'methods' => $methods,
                'function' => $func
            ];

            foreach($this->routes as $key => $route)
            {
                if ($this->uri === $route['path'])
                {
                    call_user_func(
                        $route['function'], 
                        [
                            'method' => $this->method,
                            'req' => $this->req
                        ]
                    );
                }
            }
        }

        public function get(string $route, $func)
        {
            if ($this->method === 'POST') return;

            $this->getRoutes[] = [
                'path' => $route,
                'function' => $func
            ];

            foreach($this->getRoutes as $key => $route)
            {
                if ($this->uri === $route['path'])
                {
                    call_user_func(
                        $route['function'], 
                        [
                            'req' => $this->req
                        ]
                    );
                }
            }
        }

        public function post(string $route, $func)
        {
            if ($this->method === 'GET') return;

            $this->postRoutes[] = [
                'path' => $route,
                'function' => $func
            ];

            foreach($this->postRoutes as $key => $route)
            {
                if ($this->uri === $route['path'])
                {
                    call_user_func(
                        $route['function'], 
                        [
                            'req' => $this->req
                        ]
                    );
                }
            }
        }

    }

    $app = new Router(new Request($_SERVER));

    $app->get('/', function($params) {
        echo '<h1>/ with a GET-request</h1>';
    });

    $app->post('/', function($params) {
        print_r($params['req']);
    });

    $app->get('/boots', function($params) {
        echo '<h1>/boots with a GET-request</h1>';
        print_r($params);
    });

    $app->post('/boots', function($params) {
        print_r($params);
    });
);


?>