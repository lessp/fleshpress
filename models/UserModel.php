<?php 

    require_once('./core/Model.php');

    class User extends Model
    {

        protected static $tableName;
        protected $schema;

        public function __construct(string $first_name, string $last_name, string $username, string $password, string $email = '')
        {
            $this->schema = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'username' => $username,
                'password' => $password,
                'email' => $email
            ];
        }

        public static function register()
        {
            static::$tableName = 'users';
        }

    }

    User::register();

?>