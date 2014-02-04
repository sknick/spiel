<?php
    namespace Spiel;
    
    /**
     * Identifies a class which can provide access to the login information for
     * the current user (client) of a Spiel web service.
     */
    interface Session
    {
        /**
         * Must be implemented to provide the login currently in place for the
         * user of a web service.
         * 
         * If the client has not yet logged in, NULL should be returned.
         * @return string The login (typically a username) for the client
         * currently invoking the web service. If the client has not yet logged
         * in, NULL should be returned.
         */
        public function getCurrentLogin();
    }
?>