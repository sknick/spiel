<?php
    namespace Spiel;
    
    /**
     * This class encapsulates a standard response from a web service defined
     * using Spiel. To return data to the client from within a service,
     * instantiate an instance of this class, filling in the data particular to
     * the service as the `$data` parameter of the constructor, and return it
     * from the service's implementation of the `execute()` method.
     */
    class ServiceResponse implements JSONEncodable
    {
        /**
         * One of the `StatusCodes` constants indicating the status associated
         * with this response.
         */
        public $status;
        
        /**
         * The message to associate with this response.
         */
        public $message;
        
        /**
         * The data to associate with this response.
         */
        public $data;
        
        /**
         * Constructor.
         * @param integer $status One of the `StatusCodes` constants indicating
         * the status associated with this response. Additional integer values
         * can be specified that are application-specific; such values must
         * start at 100 (see the `StatusCodes` enumeration for more
         * information).
         * @param string $message The message to associate with this response.
         * @param array|object $data The data to associate with this response.
         * If not `NULL`, the data must be a scalar value, an object that
         * implements `JSONEncodable`, an array of scalar values, or an array of
         * objects that implement `JSONEncodable`.
         * @throws \Exception if the `$data` parameter does not conform to one
         * of the types mentioned in the `$data` parameter documentation.
         */
        public function __construct($status, $message, $data = NULL)
        {
            if ($data !== NULL)
            {
                if (!is_array($data))
                {
                    if (!is_scalar($data) && !($data instanceof JSONEncodable))
                    {
                        throw new \Exception("The \$data member of a ServiceResponse must implement JSONEncodable if it is not NULL and not a scalar value.");
                    }
                }
                else if (count($data) > 0)
                {
                    if ( ($data[0] !== NULL) && !is_scalar($data[0]) && !($data[0] instanceof JSONEncodable) )
                    {
                        throw new \Exception("The items in the \$data member of a ServiceResponse must implement JSONEncodable if the \$data member is not NULL and the items are not scalar values.");
                    }
                }
            }
            
            $this->status = $status;
            $this->message = $message;
            $this->data = $data;
        }
        
        /**
         * Sends this response to the client.
         */
        public function send()
        {
            header("HTTP/1.0 200");
            header("Cache-Control: no-cache, must-revalidate");
            header("Expires: Mon, 28 May 1973 09:00:00 GMT");
            header("Content-type: application/json");
            
            echo json_encode($this);
        }
    }
?>
