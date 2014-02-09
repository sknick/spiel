<?php
    /**
     * This web service implements the ubiquitous "Hello World!" functionality.
     * The first few class definitions support the actual service implementation
     * at the bottom.
     */
    
    require_once("lib/spiel.php");
    
    /**
     * Our \Spiel\Session implementation. You often can just let Spiel use the
     * \Spiel\PHPSession implementation, but this instance provides us with a
     * guaranteed login.
     */
    class Session implements \Spiel\Session
    {
        function getCurrentLogin()
        {
            return "ExampleUser";
        }
    }
    
    /**
     * Our \Spiel\ServiceUser implementation. Other implementations could have
     * more data associated with the user (e.g., first and last name).
     */
    class User extends \Spiel\ServiceUser
    {
        function __construct($username)
        {
            parent::__construct($username);
        }
        
        public function hasPermission($permission)
        {
            $retVal = FALSE;
            
            switch ($permission)
            {
            case Permissions::SPEAK:
                $retVal = TRUE;
                break;
            
            case Permissions::YELL:
                $retVal = FALSE;
                break;
            }
            
            return $retVal;
        }
    }
    
    /**
     * Our \Spiel\ServiceUserManager implementation which simply provides a new
     * User object. Other implementations could look up the user's information
     * in a database, for example, and return an object with such information.
     */
    class UserDatabase implements \Spiel\ServiceUserManager
    {
        public function getUser($username)
        {
            return new User($username);
        }
    }
    
    /**
     * Represents the permissions our web services can require.
     */
    class Permissions extends \Spiel\Enum
    {
        const SPEAK     = 0;
        const YELL      = 1;
        
        function __construct()
        {
            parent::__construct(array(Permissions::SPEAK => "Speak",
                                      Permissions::YELL => "Yell"));
        }
    }
    
    /**
     * And finally, our \Spiel\Service implementation--the actual web service.
     * Note that all of the preceding class definitions are typically defined
     * elsewhere within the larger context of your web application and simply
     * included. As a result, service implementation files typically just
     * consist of your \Spiel\Service implementation and then the instantation
     * of an instance of it.
     */
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
            // Note that if we returned Permissions::YELL, you wouldn't be able
            // to use this service since the User object is "hardcoded" to only
            // have the SPEAK permission.
            return Permissions::SPEAK;
        }
        
        protected function getDataReturned()
        {
            return "";
        }
    }
    
    // Instantiate the service so it can do its work!
    new SayHelloService(new UserDatabase());
?>