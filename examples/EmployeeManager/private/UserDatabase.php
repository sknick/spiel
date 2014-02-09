<?php
    require_once("lib/spiel.php");
    require_once("User.php");
    
    /**
     * A `\Spiel\ServiceUserManager` implementation which simply provides a new
     * User object given a username. In typical implementations, the specified
     * username might be looked up in a database, for example, and additional
     * application-specific data might be associated with the returned
     * `\Spiel\ServiceUser` object.
     */
    class UserDatabase implements \Spiel\ServiceUserManager
    {
        /**
         * @see \Spiel\ServiceUserManager#getUser($username)
         */
        public function getUser($username)
        {
            return new User($username);
        }
    }
?>