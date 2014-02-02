<?php
    namespace Spiel;
    
    /**
     * Identifies the interface a class must implement in order to provide
     * information on the user of a web service implemented using Spiel.
     */
    interface ServiceUserManager
    {
        /**
         * @param string $username The username of the user.
         * @return \Spiel\ServiceUser A ServiceUser object providing information
         * on the specified user. If the specified user is not defined, NULL
         * should be returned.
         * @throws Exception if an error occurs.
         */
        public function getUser($username);
    }
?>