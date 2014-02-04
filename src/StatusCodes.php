<?php
    namespace Spiel;
    
    /**
     * Defines the standard status values that can go into the *status* member
     * of a response returned to the client from a Spiel web service. Additional
     * application-specific status codes can be defined, but 0 - 99 are
     * reserved, so such status codes must start at 100.
     */
    class StatusCodes extends Enum
    {
        const SUCCESS                   = 0;
        const ERROR                     = 1;
        const INVALID_USER              = 2;
        const LOGIN_NEEDED              = 3;
        const PERMISSION_DENIED         = 4;
        const REQUIRED_PARAMS_MISSING   = 5;
        
        /**
         * Constructor.
         */
        public function __construct()
        {
            parent::__construct(array(StatusCodes::SUCCESS                      => "Success",
                                      StatusCodes::ERROR                        => "Unspecified error",
                                      StatusCodes::INVALID_USER                 => "Current user is invalid",
                                      StatusCodes::LOGIN_NEEDED                 => "Login needed",
                                      StatusCodes::PERMISSION_DENIED            => "Permission denied",
                                      StatusCodes::REQUIRED_PARAMS_MISSING      => "Required parameters missing"));
        }
    }
?>