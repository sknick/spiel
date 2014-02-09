<?php
    require_once("lib/spiel.php");
    
    /**
     * Defines application-specific status codes that can be returned from the
     * EmployeeManager example's web services. By extending
     * `\Spiel\StatusCodes`, the complete set of possible status codes that can
     * be returned from the example's web services can be referenced from this
     * one class.
     */
    class AppStatusCodes extends \Spiel\StatusCodes
    {
        const ITEM_ALREADY_EXISTS = 100;
        
        /**
         * Constructor.
         */
        public function __construct()
        {
            parent::__construct();
            
            // Use the \Spiel\Enum class' add() method to add the additional
            // application-specific status codes
            $this->add(AppStatusCodes::ITEM_ALREADY_EXISTS, "Item already exists in database");
        }
    }
?>