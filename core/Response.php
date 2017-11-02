<?php

    class Response 
    {

        public function __construct()
        {}

        /**
        *
        * Renders a specified template file
        *
        * @param string template path 
        * @param array parameters to pass to the view
        */
        public function render_template(string $template_path = null, array $params = [], int $statusCode = null)
        {
            if (isset($statusCode))
            {
                http_response_code($statusCode);
            }

            ob_start();
            if (file_exists($template_path)) 
            {
                include($template_path);
            }
            $renderedView = ob_get_clean();
            return print($renderedView);
        }

        public function json($data, int $statusCode = null)
        {
            if (isset($statusCode)) 
            {
                http_response_code($statusCode);
            }

            $data = json_encode($data);

            return print($data);
        }

        public function status(int $statusCode = null)
        {
            http_response_code($statusCode);
        }

        public function send($data, int $statusCode = null)
        {
            if (isset($statusCode)) 
            {
                http_response_code($statusCode);
            }

            return print($data);
        }

    }

?>