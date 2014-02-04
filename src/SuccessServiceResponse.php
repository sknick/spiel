<?php
    namespace Spiel;
    
    /**
     * Convenience class for constructing an instance of a ServiceResponse that
     * indicates success.
     */
    class SuccessServiceResponse extends ServiceResponse
    {
        /**
         * Constructor.
         * @param array|object $data The data to associate with this response.
         * If not NULL, the data must be either an object that implements
         * JSONEncodable or an array of objects that implement JSONEncodable.
         * @throws \Exception if the $data parameter is not NULL and is either
         * an object which does not implement JSONEncodable or is an array
         * containing objects of a type which does not implement JSONEncodable.
         */
        public function __construct($data = NULL)
        {
            $statusCodesEnum = new StatusCodes();
            $successCode = StatusCodes::SUCCESS;
            
            parent::__construct($successCode, $statusCodesEnum->$successCode, $data);
        }
    }
?>