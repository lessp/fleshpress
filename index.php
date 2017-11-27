<?php

    require_once('./core/Fleshpress.php');

    $app = new Fleshpress();

    $app->get('/', function($req, $res) {
        $res->send('<h1>Hello World!</h1>');
    });

    $app->start();

?>
