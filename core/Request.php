<?php

    require_once('./core/FilteredMap.php');

    class Request
    {

        private $method;
        private $path;
        private $cookies;
        private $body;

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

        public function getMethod(): string { return $this->method; }
        public function isGET(): bool { return $this->method === 'GET' ? true : false; }
        public function isPOST(): bool { return $this->method === 'POST' ? true : false; }
        public function getPath(): string { return $this->path; }
        public function getCookies(): FilteredMap { return $this->cookies; }
        public function getBody(): FilteredMap { return $this->body; }

    }

?>