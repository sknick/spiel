<?php
    require_once("lib/spiel.php");
    
    /**
     * An implementation of the `\Spiel\ServiceUser` abstract class for
     * identifying the current user of the EmployeeManager example's web
     * services. A typical implementation might associate additional data with
     * an instance of this object such as the user's full name as well as the
     * permissions they have, etc. The permissions data could be used, for
     * example, to determine whether or not the `hasPermission()` method should
     * return `TRUE` or `FALSE`.
     */
    class User extends \Spiel\ServiceUser
    {
        /**
         * Constructor.
         * @param string $username The username of the user this object
         * represents.
         */
        public function __construct($username)
        {
            parent::__construct($username);
        }
        
        /**
         * @see \Spiel\ServiceUser#hasPermission($permission)
         */
        public function hasPermission($permission)
        {
            // Always return TRUE for this example, but in typical
            // implementations, this user's set of permissions would likely be
            // referenced to determine if they have the specified permission.
            // The `$permission` being provided is the one the web service being
            // invoked has identified as being required to access it. For the
            // EmployeeManager example, none of the web services require any
            // permission to be used.
            return TRUE;
        }
    }
?>