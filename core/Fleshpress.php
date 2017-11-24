<?php 

    require_once('./core/Config.php');

    require_once('./core/Router.php');
    require_once('./core/MiddleWare.php');

    /**
    * Fleshpress
    *
    * This class mainly exists as an abstraction layer between the user and the Router-class.
    * As a side-effect it also helps in keeping things a bit more tidy and gives a better overview.
    *
    */
    class Fleshpress extends Router
    {   

        public $config;

        public function __construct() 
        {
            $this->config = Config::getInstance();
        }

        /**
        * Adds a specific route for a specific request method to Router
        *
        * @param string $route  The route to add.
        * @param array  $funcs  Functions to run on execution.
        */
        public function get(string $route, ...$funcs)    { parent::add($route, $funcs, 'GET'); }
        public function post(string $route, ...$funcs)   { parent::add($route, $funcs, 'POST'); }
        public function put(string $route, ...$funcs)    { parent::add($route, $funcs, 'PUT'); }
        public function delete(string $route, ...$funcs) { parent::add($route, $funcs, 'DELETE'); }

        /**
        * Attaches middleware
        *
        * This differs from the "regular" functions passed to a route 
        * as it also attaches itself to the Request-object.
        *
        */
        public function use(MiddleWare $middleWare) 
        {
            parent::addMiddleWare($middleWare);
        }
        
        /**
        * Start
        *
        * Tells the Router to start matching on registered routes.
        *
        */
        public function start() 
        {
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            
            // shame
            if (isset($_POST['_method'])) {
                $requestMethod = $_POST['_method'];
            };

            parent::match($_SERVER['REQUEST_URI'], $requestMethod);
        }
    }
?>