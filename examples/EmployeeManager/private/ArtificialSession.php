<?php
    require_once("lib/spiel.php");
    
    /**
     * This subclass of `\Spiel\PHPSession` automatically logs you into the
     * system the first time you use a web service in this example. Normally, a
     * `PHPSession` instance will look for the current login in the `$_SESSION`
     * global variable, having expected another part of your system (e.g., your
     * login system) to have put it there after a successful login occurred.
     */
    class ArtificialSession extends \Spiel\PHPSession
    {
        /**
         * Constructor.
         */
        public function __construct()
        {
            parent::__construct();
            
            // If the current session doesn't already have our login
            if (!array_key_exists(\Spiel\PHPSession::CURRENT_LOGIN, $_SESSION))
            {
                // Set it
                $_SESSION[\Spiel\PHPSession::CURRENT_LOGIN] = "ArtificialUser";
            }
        }
    }
?>