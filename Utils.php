<?php

    function render_view(string $template = null, array $params = [])
    {
        ob_start();
        if (file_exists($template)) 
        {
            include($template);
        }
        return print ob_get_clean();
    }

    function render_response(int $responseCode, string $message)
    {
        http_response_code($responseCode);

        return print (
            '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Document</title>
            </head>
            <body>'
                . $message .
            '</body>
            </html>'
        );

        exit();
    }

?>