<?php 

    require_once('./core/utils/Singleton.php');
    
    class Config extends ArrayObject
    {
        use Singleton;

        public function __construct()
        {
            $this['templatesFolder'] = './static/templates/';
        }
        
    }

?>
