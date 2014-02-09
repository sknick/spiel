<?php
    require_once("lib/spiel.php");
    
    /**
     * A `\Spiel\Enum` subclass which identifies the possible levels of
     * experience an employee might have with a particular skill.
     */
    class Experience extends \Spiel\Enum
    {
        const BEGINNER      = 1;
        const INTERMEDIATE  = 2;
        const EXPERT        = 3;
        
        /**
         * Constructor.
         */
        public function __construct()
        {
            parent::__construct(array(Experience::BEGINNER        => "Beginner",
                                      Experience::INTERMEDIATE    => "Intermediate",
                                      Experience::EXPERT          => "Expert"));
        }
    }
?>