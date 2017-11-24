<?php

    require_once('./core/Fleshpress.php');

    $app = new Fleshpress();

    $app->get('/', function($req, $res) {
        $res->send('Hello World!');
    });

    $app->start();

?>
