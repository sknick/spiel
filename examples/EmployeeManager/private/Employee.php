<?php
    require_once("lib/spiel.php");
    require_once("Positions.php");
    require_once("Skill.php");
    
    /**
     * One of the domain model classes managed by the "EmployeeManager" example.
     * As a `\Spiel\JSONEncodable` instance, its public instance attributes will
     * be put into the JSON structure returned to the client. The
     * `$objectProperties` and `$enumProperties` static members identify which
     * parts of this object are composed of other `JSONEncodable` or
     * `\Spiel\Enum` objects themselves, respectively.
     */
    class Employee implements \Spiel\JSONEncodable
    {
        public static $objectProperties     = array("skills"    => array("Skill"));
        public static $enumProperties       = array("position"  => "Positions");
        
        public $lastName;
        public $firstName;
        public $position;
        public $skills = array();
        
        /**
         * Constructor.
         * @param string $lastName The last name of this employee.
         * @param string $firstName The first name of this employee.
         * @param integer $position One of the values of the Positions
         * enumeration indicating this employee's position.
         */
        public function __construct($lastName, $firstName, $position)
        {
            $this->lastName = $lastName;
            $this->firstName = $firstName;
            $this->position = $position;
        }
        
        /**
         * Adds a skill to this employee.
         * @param Skill $skill A Skill object providing information on a skill
         * this employee has.
         */
        public function addSkill(Skill $skill)
        {
            array_push($this->skills, $skill);
            
            usort($this->skills, Skill::compareForSort);
        }
    }
?>