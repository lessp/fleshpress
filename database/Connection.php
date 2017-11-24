<?php 

    require_once('./core/Config.php');
    require_once('./utils/Singleton.php');
    
    class Connection
    {
        use Singleton;

        public $handler;

        public function __construct()
        {
            try {

                $config = Config::getInstance();
    
                $this->handler = new PDO(
                    $config['db']['dsn'],
                    $config['db']['user'],
                    $config['db']['password']
                );
    
                $this->handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $error) {
                echo $error->getMessage();
            }
        }
        
    }

?>
