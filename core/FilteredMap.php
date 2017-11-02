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
            if ($this->has($name)) {
                return $this->map[$name];
            } else {
                throw new Exception ('Hejhej');
            }

        }
        public function getInt(string $name) { return (int) $this->get($name); }
        public function getNumber(string $name) { return (float) $this->get($name); }
        public function parse() { return $this->map; }
        
        public function getString(string $name, bool $filter = true)
        {
            $value = (string) $this->get($name);
            return $filter ? addslashes($value) : $value;
        }
    }

?>