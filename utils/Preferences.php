<?php

    class Preferences
    {
        static $staticDirectory = './static/';
        static $templateDirectory = './static/templates/';

        public function setTemplatesDirectory(string $string)
        {
            self::$templateDirectory = self::$staticDirectory .= self::$templateDirectory;
        }

    }


?>