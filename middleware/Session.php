<?php

    require_once('./core/Request.php');
    require_once('./core/Response.php');

    class Session {
        
        private $_settings;

        public function __construct(
            int $lifeTime = 3600,
            string $path = '/',
            string $domain = null,
            bool $secure = false,
            bool $httpOnly = false
        ) {

            $this->_settings['name'] = 'fleshpress_session';

            $this->_settings['lifeTime'] = $lifeTime;
            $this->_settings['path'] = $path;
            $this->_settings['domain'] = $domain;
            $this->_settings['secure'] = $secure;
            $this->_settings['httpOnly'] = $httpOnly;

        }

        public function __invoke(Request $req, Response $res) {
            
            session_set_cookie_params(
                $this->_settings['lifeTime'],
                $this->_settings['path'],
                $this->_settings['domain'],
                $this->_settings['secure'],
                $this->_settings['httpOnly']
            );
                    
            session_name($this->_settings['name']);
            session_start();

        }
    
        public function __set($name, $value)
        {
            $_SESSION[$this->_settings['name']][$name] = $value;
        }

        public function __get($name)
        {
            if (! empty($_SESSION[$this->_settings['name']])) {
                if (array_key_exists($name, $_SESSION[$this->_settings['name']])) {
                    return $_SESSION[$this->_settings['name']][$name];
                }
            }


            return null;
        }

        public function __isset($name)
        {
            return isset($_SESSION[$this->_settings['name']][$name]);
        }

        public function __unset($name)
        {
            unset($_SESSION[$this->_settings['name']][$name]);
        }

    }

?>