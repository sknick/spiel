<?php
    namespace Spiel;
    
    /**
     * This abstract class identifies a user of a web service implemented using
     * Spiel.
     */
    abstract class ServiceUser implements JSONEncodable
    {
        /**
         * The username of this user.
         */
        public $username;
        
        /**
         * Constructor.
         * @param string $username The username of this user.
         */
        public function __construct($username)
        {
            $this->username = $username;
        }
        
        /**
         * Must be implemented to determine whether or not the user has the
         * specified permission.
         * @param integer $permission The permission for which to be checked.
         * @return boolean `TRUE` if the user has the specified permission;
         * `FALSE` if not.
         */
        abstract public function hasPermission($permission);
    }
?>