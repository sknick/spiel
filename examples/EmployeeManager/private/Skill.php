<?php
    require_once("lib/spiel.php");
    require_once("Experience.php");
    
    /**
     * One of the domain model classes managed by the "EmployeeManager" example.
     * As a `\Spiel\JSONEncodable` instance, its public instance attributes will
     * be put into the JSON structure returned to the client. The
     * `$objectProperties` and `$enumProperties` static members identify which
     * parts of this object are composed of other `JSONEncodable` or
     * `\Spiel\Enum` objects themselves, respectively.
     */
    class Skill implements \Spiel\JSONEncodable
    {
        public static $objectProperties     = array();
        public static $enumProperties       = array("experience" => "Experience");
        
        public $name;
        public $experience;
        
        /**
         * Constructor.
         * @param string $name The name of this skill.
         * @param integer $experience One of the values of the `Experience`
         * enumeration indicating the level of experience an employee has with
         * this skill.
         */
        public function __construct($name, $experience)
        {
            $this->name = $name;
            $this->experience = $experience;
        }
        
        /**
         * Array sorting function for use with, for example, PHP's `usort`
         * function. Compares the specified Skill objects so they can be sorted
         * by name.
         */
        public static function compareForSort(Skill $a, Skill $b)
        {
            if ($a->name < $b->name)
            {
                return -1;
            }
            else if ($a->name == $b->name)
            {
                return 0;
            }
            else if ($a->name > $b->name)
            {
                return 1;
            }
        }
    }
?>