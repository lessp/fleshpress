<?php

    require_once('./core/FilteredMap.php');

    class Request
    {
        private $method;
        private $path;
        private $cookies;
        private $body;
        private $params;

        function __construct(string $requestMethod, string $requestedPath)
        {
            $this->method = $requestMethod;
            $this->path = $requestedPath;
            
            switch ($this->method) {
                case 'GET': $this->params = $_GET; break;
                case 'POST': $this->body = $_POST; break;
                case 'PUT': /* TODO */; break;
                case 'DELETE': /* TODO */; break;
            }
            
            $this->cookies = $_COOKIE;
        }

        public function params(): array { return $this->params; }
        public function method(): string { return $this->method; }
        public function path(): string { return $this->path; }
        public function cookies() { return $this->cookies; }
        public function body(): FilteredMap { return $this->body; }

    }

?>