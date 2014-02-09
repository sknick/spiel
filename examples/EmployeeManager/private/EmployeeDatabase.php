<?php
    require_once("Employee.php");
    
    /**
     * In-memory database which stores information on employees and skills.
     * In most systems utilizing Spiel, such a class would typically be the
     * interface to an actual database running on the server (e.g., this class
     * might extend PHP's `PDO` class, for example, and connect to a MySQL
     * database).
     */
    class EmployeeDatabase
    {
        const DB_INSTANCE = "employee_database";
        
        private $employees  = array();
        private $skills     = array();
        
        /**
         * Returns the one-and-only instance of this class. It is maintained in
         * the current session established when one of the web services in this
         * example is first requested.
         */
        public static function getInstance()
        {
            @session_start();
            
            if (!array_key_exists(EmployeeDatabase::DB_INSTANCE, $_SESSION))
            {
                $_SESSION[EmployeeDatabase::DB_INSTANCE] = new EmployeeDatabase();
            }
            
            return $_SESSION[EmployeeDatabase::DB_INSTANCE];
        }
        
        /**
         * Retrieves the employees defined in the system.
         * @param string $lastName The last name of an employee for which to be
         * searched.
         * @param string $firstName The first name of an employee for which to
         * be searched.
         * @return array An array of `Employee` objects indicating the employees
         * defined in the database. If the `$lastName` and/or `$firstName`
         * parameters were provided, the `Employee` objects returned will be the
         * ones matching those parameters.
         */
        public function getEmployees($lastName = NULL, $firstName = NULL)
        {
            $retVal = array();
            
            // If no parameters were provided
            if ( ($lastName === NULL) && ($firstName === NULL) )
            {
                // Return all employees
                $retVal = $this->employees;
            }
            // Else parameters were indicated
            else
            {
                // So go through and only add the employees to the return value
                // that match those parameters
                foreach ($this->employees as $employee)
                {
                    if ( ($lastName !== NULL) && ($firstName === NULL) )
                    {
                        if ($employee->lastName == $lastName)
                        {
                            array_push($retVal, $employee);
                        }
                    }
                    else if ( ($lastName === NULL) || ($firstName !== NULL) )
                    {
                        if ($employee->firstName == $firstName)
                        {
                            array_push($retVal, $employee);
                        }
                    }
                    else if ( ($lastName !== NULL) || ($firstName !== NULL) )
                    {
                        if ( ($employee->lastName == $lastName) && ($employee->firstName == $firstName) )
                        {
                            array_push($retVal, $employee);
                        }
                    }
                }
            }
            
            return $retVal;
        }
        
        /**
         * @return array An array of strings indicating the names of all of the
         * skills defined in the database.
         */
        public function getSkills()
        {
            return $this->skills;
        }
        
        /**
         * Adds an employee to the database.
         * @param Employee $employee An `Employee` objecting indicating the
         * employee to be added.
         * @return boolean `TRUE` if the Employee was successfully added to the
         * database or `FALSE` if it could not be added because another employee
         * with the same first and last names is already present.
         */
        public function addEmployee(Employee $employee)
        {
            $retVal = FALSE;
            
            $employeeKey = $this->getEmployeeKey($employee);
            if (!array_key_exists($employeeKey, $this->employees))
            {
                $this->employees[$employeeKey] = $employee;
                
                ksort($this->employees);
                
                $retVal = TRUE;
            }
            
            return $retVal;
        }
        
        /**
         * Adds a skill to the database.
         * @param string $name The name of the skill to be added to the
         * database.
         * @return boolean `TRUE` if the specified skill was successfully added
         * to the database or `FALSE` if the skill is already defined.
         */
        public function addSkill($name)
        {
            $retVal = FALSE;
            
            if (!array_search($name, $this->skills))
            {
                array_push($this->skills, $name);
                
                sort($this->skills);
                
                $retVal = TRUE;
            }
            
            return $retVal;
        }
        
        // The constructor is private since this class is a singleton managed by
        // the current session. To get an instance of this class, the static
        // getInstance() method is used.
        private function __construct()
        {
            // Add some initial data to our example database:
            $this->addSkill("C");
            $this->addSkill("CAD");
            $this->addSkill("C++");
            $this->addSkill("Git");
            $this->addSkill("Java");
            $this->addSkill("MATLAB");
            $this->addSkill("Microsoft Project");
            $this->addSkill("PHP");
            $this->addSkill("VxWorks");
            
            $employee = new Employee("Knick", "Scott", Positions::SOFTWARE_ENGINEER);
            $employee->addSkill(new Skill("C++", Experience::EXPERT));
            $employee->addSkill(new Skill("PHP", Experience::EXPERT));
            $employee->addSkill(new Skill("Git", Experience::BEGINNER));
            
            $this->addEmployee($employee);
            
            $employee = new Employee("Smith", "Bob", Positions::MECHANICAL_ENGINEER);
            $employee->addSkill(new Skill("CAD", Experience::EXPERT));
            
            $this->addEmployee($employee);
            
            $employee = new Employee("Doe", "Jane", Positions::MANAGER);
            $employee->addSkill(new Skill("Microsoft Project", Experience::EXPERT));
            $employee->addSkill(new Skill("Java", Experience::EXPERT));
            
            $this->addEmployee($employee);
        }
        
        // Returns a key that can be used in this class' $employees attribute
        // to uniquely identify an employee managed by this database.
        private function getEmployeeKey(Employee $employee)
        {
            return $employee->lastName . ", " . $employee->firstName;
        }
    }
?>