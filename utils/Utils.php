<?php

    /**
     *
     * Renders a specified template file
     *
     * @param string template path 
     * @param array parameters to pass to the view
     */
    function render_view(string $template_path = null, array $params = [])
    {
        ob_start();
        if (file_exists($template_path)) 
        {
            include($template_path);
        }
        $renderedView = ob_get_clean();
        return print $renderedView;
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
            <body>
                <h2>'
                    . $message .
                '</h2>
            </body>
            </html>'
        );

        exit();
    }

?>