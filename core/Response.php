<?php

    require_once('./utils/Preferences.php');

    class Response 
    {

        private $templateDirectory;

        public function __construct()
        {}

        /**
        *
        * Renders a specified template file
        *
        * @param string template path 
        * @param array parameters to pass to the view
        */
        public function render_file(string $filePath = null, array $params = [], int $statusCode = null)
        {
            if (isset($statusCode))
            {
                http_response_code($statusCode);
            }

            extract($params);

            ob_start();
            if (file_exists($filePath)) 
            {
                include($filePath);
            }
            $renderedView = ob_get_clean();
            return exit(print($renderedView));
        }
        
        public function render_template(string $template_path = null, array $params = [], int $statusCode = null)
        {
            $template_path = Preferences::$templateDirectory . $template_path;
            return $this->render_file($template_path, $params, $statusCode);
        }

        public function json($data, int $statusCode = null)
        {
            if (isset($statusCode)) 
            {
                http_response_code($statusCode);
            }

            $data = json_encode($data, JSON_PRETTY_PRINT);

            return exit(print($data));
        }

        public function redirect(string $url)
        {
            return header('Location:'. $url);
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

            return exit(print($data));
        }

    }

    class jsonSerialize implements JsonSerializable {
        function __construct($value) { $this->value = $value; }
        function jsonSerialize() { return $this->value; }
    }

?>