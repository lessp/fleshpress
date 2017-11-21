<?php 

    require_once('./core/Model.php');

    class PasswordToken extends Model
    {

        protected static $tableName;
        protected $schema;

        public function __construct(string $token, int $userId, DateTime $expires)
        {
            $this->schema = [
                'token' => $token,
                'user_id' => $userId,
                'expires' => $expires->modify('+1 hour')->format('Y-m-d H:i:s')
            ];
        }

        public static function register()
        {
            static::$tableName = 'password_tokens';
        }

    }

    PasswordToken::register();

?>