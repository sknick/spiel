<?php
    require_once("lib/spiel.php");
    require_once("AppStatusCodes.php");
    require_once("ArtificialSession.php");
    require_once("EmployeeDatabase.php");
    require_once("UserDatabase.php");
    
    /**
     * A `\Spiel\Service` subclass which allows a new skill to be added to the
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
                                "Adds a new skill to the system and returns all of the skills now defined.",
                                new ArtificialSession());
        }
        
        /**
         * @see \Spiel\Service#execute($currentUser, $params)
         */
        public function execute(\Spiel\ServiceUser $currentUser, $params)
        {
            $db = EmployeeDatabase::getInstance();
            
            if ($db->addSkill($params["name"]))
            {
                return new \Spiel\SuccessServiceResponse($db->getSkills());
            }
            else
            {
                $appStatusCodes = new AppStatusCodes();
                $statusCode = AppStatusCodes::ITEM_ALREADY_EXISTS;
                
                return new \Spiel\ServiceResponse($statusCode, $appStatusCodes->$statusCode);
            }
        }
        
        /**
         * @see \Spiel\Service#getRequiredParameters()
         */
        public function getRequiredParameters()
        {
            return array("name" => "The name of the skill.");
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