<?php
    require_once("spiel.php");
    
    class Session implements \Spiel\Session
    {
        function getCurrentLogin()
        {
            return "ExampleUser";
        }
    }
    
    class User extends \Spiel\ServiceUser
    {
        function __construct($username)
        {
            parent::__construct($username);
        }
        
        public function hasPermission($permission)
        {
            return TRUE;
        }
    }
    
    class UserDatabase implements \Spiel\ServiceUserManager
    {
        public function getUser($username)
        {
            return new User($username);
        }
    }
    
    class Permissions extends \Spiel\Enum
    {
        const SPEAK     = 0;
        const READ      = 1;
        
        function __construct()
        {
            parent::__construct(array(Permissions::SPEAK => "Speak",
                                      Permissions::READ => "Read"));
        }
    }
    
    class SayHelloService extends \Spiel\Service
    {
        function __construct($db)
        {
            parent::__construct($db,
                                __FILE__,
                                "Example \"Hello World!\" Spiel service.",
                                new Session());
        }
        
        public function execute(\Spiel\ServiceUser $currentUser, $params)
        {
            return new \Spiel\SuccessServiceResponse("Hello World!");
        }
        
        protected function getPermissionType()
        {
            return new Permissions();
        }
        
        protected function getPermissionRequired()
        {
            return Permissions::SPEAK;
        }
        
        protected function getDataReturned()
        {
            return "";
        }
    }
    
    new SayHelloService(new UserDatabase());
?>