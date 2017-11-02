<?php

    class FilteredMap
    {
        private $map;

        public function __construct(array $baseMap)
        {
            $this->map = $baseMap;
        }

        public function has(string $name): bool { return isset($this->map[$name]); }
        public function get(string $name) 
        { 
            return $this->map[
                $this->_exists($name)
            ];
        }
        public function getInt(string $name) { return (int) $this->get($name); }
        public function getNumber(string $name) { return (float) $this->get($name); }
        public function parse() { return $this->map; }
        
        public function getString(string $name, bool $filter = true)
        {
            $value = (string) $this->get($name);
            return $filter ? addslashes($value) : $value;
        }

        private function _exists(string $name)
        {
            if (! $this->has($name)) {
                throw new Exception('Parameter does not exist.');
            }

            return $name;
        }
    }

?>