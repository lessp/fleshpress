<?php

    require_once('./core/FilteredMap.php');

    class Request
    {

        private $method;
        private $path;
        private $cookies;
        private $body;
        private $params;

        function __construct($req)
        {
            $this->method = $req['REQUEST_METHOD'];
            $this->path = $req['REQUEST_URI'];

            if ($this->isGET()) { 
                $this->body = new FilteredMap($_GET);
            } elseif ($this->isPOST()) {
                $this->body = new FilteredMap($_POST);
            }

            $this->cookies = new FilteredMap($_COOKIE);
        }

        public function setParams($params) { $this->params = new FilteredMap($params); }

        public function params(): FilteredMap { return $this->params; }
        public function isGET(): bool { return $this->method === 'GET' ? true : false; }
        public function isPOST(): bool { return $this->method === 'POST' ? true : false; }
        public function method(): string { return $this->method; }
        public function path(): string { return $this->path; }
        public function cookies(): FilteredMap { return $this->cookies; }
        public function body(): FilteredMap { return $this->body; }

    }

?>