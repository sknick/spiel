<?php
    require_once("lib/spiel.php");
    require_once("ArtificialSession.php");
    require_once("EmployeeDatabase.php");
    require_once("UserDatabase.php");
    
    /**
     * A `\Spiel\Service` subclass which returns the employees defined in the
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
                                "Returns the employees currently defined in the system.",
                                new ArtificialSession());
        }
        
        /**
         * @see \Spiel\Service#execute($currentUser, $params)
         */
        public function execute(\Spiel\ServiceUser $currentUser, $params)
        {
            $db = EmployeeDatabase::getInstance();
            
            $lastName = NULL;
            if ($params["lastName"])
            {
                $lastName = $params["lastName"];
            }
            
            $firstName = NULL;
            if ($params["firstName"])
            {
                $firstName = $params["firstName"];
            }
            
            return new \Spiel\SuccessServiceResponse($db->getEmployees($lastName, $firstName));
        }
        
        /**
         * @see \Spiel\Service#getOptionalParameters()
         */
        public function getOptionalParameters()
        {
            return array("lastName" => "The last name of an employee for which to be searched.",
                         "firstName" => "The first name of an employee for which to be searched.");
        }
        
        /**
         * @see \Spiel\Service#getDataReturned()
         */
        public function getDataReturned()
        {
            // Since this service returns an array of Employee objects, we
            // indicate this by specifying pretty much exactly that. If instead
            // this service, for example, returned just one Employee object, we
            // would specify the return value of this method like so:
            // 
            //     return "Employee";
            // 
            // See the documentation for the getDataReturned() method of the
            // \Spiel\Service class for information on the possible return
            // values.
            return array("Employee");
        }
    }
    
    // Create an instance of this service to get things moving
    new ServiceImpl();
?>