<?php

    class Request
    {

        private $method;
        private $path;

        function __construct($req)
        {
            $this->method = $req['REQUEST_METHOD'];
            $this->path = $req['REQUEST_URI'];
        }

        public function isGET(): bool { return $this->method === 'GET' ? true : false; }
        public function isPOST(): bool { return $this->method === 'POST' ? true : false; }
        public function getPath(): string { return $this->path; }

    }

?>