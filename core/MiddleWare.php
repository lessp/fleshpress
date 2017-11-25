<?php

    require_once('./core/Request.php');

    abstract class MiddleWare 
    {

        abstract public function __invoke(Request $req);
        abstract public function __set($name, $value);
        abstract public function __get($name);
        abstract public function __isset($name);
        abstract public function __unset($name);

    }

?>