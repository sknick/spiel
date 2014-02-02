<?php
    namespace Spiel;
    
    /**
     * A class for defining an enumeration. Since the __get() method is
     * overridden by this class, you can access the description of a particular
     * value using property access on an instantiated instance of this class
     * (e.g., $enumObj->0).
     */
    class Enum
    {
        private $items;
        
        /**
         * Constructor.
         * @param array $items An associative array of integer values to string
         * descriptions of each value. For example, an enumeration of colors
         * might be defined with this associative array:
         * 
         * array(0 => "Red",
         *       1 => "Green",
         *       2 => "Blue",
         *       3 => "White",
         *       4 => "Black")
         */
        public function __construct($items)
        {
            foreach ($items as $value => $description)
            {
                $this->add($value, $description);
            }
        }
        
        /**
         * @param integer $value The value to be added to this enumeration.
         * @param string $description The string description to associate with
         * this value.
         */
        public function add($value, $description)
        {
            $this->items[$value] = $description;
        }
        
        /**
         * @return array The values of this enumeration.
         */
        public function getValues()
        {
            return array_keys($this->items);
        }
        
        /**
         * Overriden __get() method.
         */
        public function __get($value)
        {
            return $this->items[$value];
        }
    }
?>