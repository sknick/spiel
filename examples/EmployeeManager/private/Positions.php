<?php
    require_once("lib/spiel.php");
    
    /**
     * A `\Spiel\Enum` subclass which identifies the possible positions an
     * employee might be in.
     */
    class Positions extends \Spiel\Enum
    {
        const ELECTRICAL_ENGINEER   = 1;
        const MANAGER               = 2;
        const MECHANICAL_ENGINEER   = 3;
        const SOFTWARE_ENGINEER     = 4;
        const SYSTEMS_ENGINEER      = 5;
        
        /**
         * Constructor.
         */
        public function __construct()
        {
            parent::__construct(array(Positions::ELECTRICAL_ENGINEER    => "Electrical Engineer",
                                      Positions::MANAGER                => "Manager",
                                      Positions::MECHANICAL_ENGINEER    => "Mechanical Engineer",
                                      Positions::SOFTWARE_ENGINEER      => "Software Engineer",
                                      Positions::SYSTEMS_ENGINEER       => "Systems Engineer"));
        }
    }
?>