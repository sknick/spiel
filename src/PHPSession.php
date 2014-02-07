<?php
    namespace Spiel;
    
    /**
     * Session implementation which looks up the login of the current client in
     * the PHP global `$_SESSION` variable. In order for an instance of this
     * object to look up the current login of a client, the login system in use
     * must have put the login into the current session using the key identified
     * by the `CURRENT_LOGIN` constant of this class.
     */
    class PHPSession implements Session
    {
        /**
         * The key used to identify the login in the current PHP session.
         */
        const CURRENT_LOGIN = "spiel_current_login";
        
        /**
         * Constructor.
         */
        function __construct()
        {
            @session_start();
        }
        
        /**
         * @see Session#getCurrentLogin()
         */
        public function getCurrentLogin()
        {
            $retVal = NULL;
            
            if (array_key_exists(PHPSession::CURRENT_LOGIN, $_SESSION))
            {
                $retVal = $_SESSION[PHPSession::CURRENT_LOGIN];
            }
            
            return $retVal;
        }
    }
?>