<?php
    require_once("lib/spiel.php");
    require_once("ArtificialSession.php");
    require_once("EmployeeDatabase.php");
    require_once("UserDatabase.php");
    
    /**
     * A `\Spiel\Service` subclass which returns the skills defined in the
     * system.
     */
    class ServiceImpl extends \Spiel\Service
    {
        /**
         * Constructor.
         */
        public function __construct()
        {
            parent::__construct(new UserDatabase(),
                                __FILE__,
                                "Returns all of the skills currently defined in the system.",
                                new ArtificialSession());
        }
        
        /**
         * @see \Spiel\Service#execute($currentUser, $params)
         */
        public function execute(\Spiel\ServiceUser $currentUser, $params)
        {
            $db = EmployeeDatabase::getInstance();
            
            return new \Spiel\SuccessServiceResponse($db->getSkills());
        }
        
        /**
         * @see \Spiel\Service#getDataReturned()
         */
        public function getDataReturned()
        {
            // This service returns an array of strings, so specify this by
            // returning an empty array. See the documentation for the
            // getDataReturned() method of the \Spiel\Service class for
            // information on the possible return values.
            return array();
        }
    }
    
    // Create an instance of this service to get things moving
    new ServiceImpl();
?>