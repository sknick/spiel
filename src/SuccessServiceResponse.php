<?php
    namespace Spiel;
    
    /**
     * Convenience class for constructing an instance of a `ServiceResponse`
     * that indicates success.
     */
    class SuccessServiceResponse extends ServiceResponse
    {
        /**
         * Constructor.
         * @param array|object $data The data to associate with this response.
         * If not `NULL`, the data must be a scalar value, an object that
         * implements `JSONEncodable`, an array of scalar values, or an array of
         * objects that implement `JSONEncodable`.
         * @throws \Exception if the `$data` parameter does not conform to one
         * of the types mentioned in the `$data` parameter documentation.
         */
        public function __construct($data = NULL)
        {
            $statusCodesEnum = new StatusCodes();
            $successCode = StatusCodes::SUCCESS;
            
            parent::__construct($successCode, $statusCodesEnum->$successCode, $data);
        }
    }
?>
