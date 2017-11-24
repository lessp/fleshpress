<?php

    require_once('./core/Config.php');

    class Response 
    {

        private $preferences;

        public function __construct() 
        {
            $this->preferences = include('./config/preferences.php');
        }

        /**
        *
        * Renders a specified file
        *
        * @param string The file to render
        * @param array  Parameters to pass and extract to the view
        * @param int    Status code to send
        */
        public function render_file(string $filePath = null, array $params = [], int $statusCode = null)
        {
            if (isset($statusCode)) {
                http_response_code($statusCode);
            }

            extract($params);

            ob_start();
            if (file_exists($filePath)) {
                include($filePath);
            }
            $renderedView = ob_get_clean();
            return exit(print($renderedView));
        }
        
        /**
        *
        * Renders a template file
        *
        * The template file's base directory is based on what settings that is provided 
        * in the preferences file.
        *
        * By default this is: /static/templates/
        *
        * @param string The template file to render
        * @param array  Parameters to pass and extract to the view
        * @param int    Status code to send
        */
        public function render_template(string $template_path = null, array $params = [], int $statusCode = null)
        {
            $template_path = Config::getInstance()['templatesFolder'] . $template_path;
            return $this->render_file($template_path, $params, $statusCode);
        }

        /**
        *
        * Sends JSON-formatted data
        *
        * By default this is: /static/templates/
        *
        * @param array  The data to send as JSON
        * @param int    Status code to send
        */
        public function json(array $data, int $statusCode = null)
        {
            if (isset($statusCode)) {
                http_response_code($statusCode);
            }

            $data = json_encode($data, JSON_PRETTY_PRINT);

            return exit(print($data));
        }

        /**
        *
        * Redirects to a different URL
        *
        * @param string  The URL to redirect to
        */
        public function redirect(string $url)
        {
            return header('Location:'. $url);
        }

        /**
        *
        * Sends a HTTP Status Code
        *
        * @param int  The status code to send
        */
        public function status(int $statusCode = null)
        {
            http_response_code($statusCode);
        }


        /**
        *
        * Prints the data
        *
        */
        public function send($data, int $statusCode = null)
        {
            if (isset($statusCode)) {
                http_response_code($statusCode);
            }

            return exit(print($data));
        }

    }

?>
