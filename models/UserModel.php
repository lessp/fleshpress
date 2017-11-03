<?php 

    require_once('./core/Model.php');

    class User extends Model
    {

        protected static $tableName;
        protected $schema;

        public function __construct(string $username, string $password)
        {
            $this->schema = [
                'username' => $username,
                'password' => $password
            ];
        }

        public static function register()
        {
            static::$tableName = 'users';
        }

    }

    User::register();

?>