<?php
    namespace Spiel;
    
    /**
     * This class represents a web service as can be implemented using Spiel. If
     * the client requests the implementing web service without an "x"
     * parameter, the superclass will return helpful interface documentation to
     * the client. If the client requests the implementing web service with an
     * "x" parameter, this function will ensure that the client has specified
     * any other required parameters as indicated by the $requiredParams
     * argument to the constructor, and then it will call the execute() method
     * on the subclass implementation. As a result, the actual implementation of
     * the web service is primarily done within the implemented execute()
     * method. The execute() method's return value is a ServiceResponse object
     * which will be sent to the requesting client.
     * 
     * Typical usage of this superclass is defining the subclass, implementing
     * its execute() method, and then instantiating a new instance of it. For
     * example:
     * 
     * <?php
     *     require_once("spiel.php");
     *     
     *     class ServiceImpl extends \Spiel\Service
     *     {
     *         public function __construct()
     *         {
     *             // Pass arguments specific to this service to the superclass
     *             parent::__construct(...);
     *         }
     *         
     *         public function execute(\Spiel\ServiceUser $currentUser, $params)
     *         {
     *             // Implement the service as you see fit
     *             ...
     *             
     *             // Make sure you return a response, for example:
     *             return new \Spiel\SuccessServiceResponse();
     *         }
     *     }
     *     
     *     new ServiceImpl();
     * ?>
     */
    abstract class Service
    {
        /**
         * Constructor.
         * 
         * @param \Spiel\ServiceUserManager $userManager The ServiceUserManager
         * that can be used to query for information on the current user making
         * the service request.
         * 
         * @param string $file The full path to the file containing the web
         * service implementation. Passing the value of the __FILE__ constant is
         * an easy way to provide this.
         * 
         * @param string $description A description of this web service that
         * will be put in the interface documentation accessible to the client.
         * 
         * @param \Spiel\Session $session The Session object that can be used to
         * look up the current login session information. If NULL, a new
         * instance of a PHPSession class will be used.
         * 
         * @param boolean $trimParamValues TRUE if parameter values to this web
         * service should have whitespace trimmed from their ends prior to
         * passing them to the execute() method or FALSE if not.
         */
        function __construct(ServiceUserManager $userManager,
                             $file,
                             $description,
                             Session $session = NULL,
                             $trimParamValues = TRUE)
        {
            $timeStart = microtime(TRUE);
            
            if ($session === NULL)
            {
                $session = new PHPSession();
            }
            
            $currentLogin = $session->getCurrentLogin();
            
            // If the current user has not yet been set for the session
            if ($currentLogin === NULL)
            {
                // Send back a "login needed" response
                $statusCodes = new StatusCodes();
                $code = StatusCodes::LOGIN_NEEDED;
                
                $response = new ServiceResponse($code, $statusCodes->$code);
                $response->send();
            }
            // Else if the client did not specify the "x" parameter
            else if (!array_key_exists("x", $_REQUEST))
            {
                // Then we'll output interface documentation for this service
                $this->echoDocs($file, $description);
            }
            // Else we can continue with the service invocation...
            else
            {
                try
                {
                    $currentUser = $userManager->getUser($currentLogin);
                    
                    // If we can't look up the currently logged-in user
                    if ($currentUser === NULL)
                    {
                        // Then return an "invalid user" error to the client
                        $statusCodes = new StatusCodes();
                        $code = StatusCodes::INVALID_USER;
                        
                        $response = new ServiceResponse($code, $statusCodes->$code);
                        $response->send();
                    }
                    // Else the currently logged-in user was found
                    else
                    {
                        $permissionRequired = $this->getPermissionRequired();
                        
                        // If permission is required to access this web service
                        // but the current user doesn't have permission
                        if (($permissionRequired !== NULL) && !$currentUser->hasPermission($permissionRequired))
                        {
                            // Return a "permission denied" error
                            $statusCodes = new StatusCodes();
                            $code = StatusCodes::PERMISSION_DENIED;
                            
                            $response = new ServiceResponse($code, $statusCodes->$code);
                            $response->send();
                        }
                        // Else if no permission is required to access this web
                        // service *or* the current user has the permission
                        // needed
                        else
                        {
                            $requiredParams = $this->getRequiredParameters();
                            
                            // Check that all required parameters are present in
                            // the request
                            $requiredParamsPresent = TRUE;
                            foreach ($requiredParams as $k => $v)
                            {
                                if (!array_key_exists($k, $_REQUEST))
                                {
                                    $requiredParamsPresent = FALSE;
                                    
                                    break;
                                }
                            }
                            
                            // If the required parameters are not all present
                            if (!$requiredParamsPresent)
                            {
                                // Send back a "required params missing" error
                                // to the client
                                $statusCodes = new StatusCodes();
                                $code = StatusCodes::REQUIRED_PARAMS_MISSING;
                                
                                $response = new ServiceResponse($code, $statusCodes->$code);
                                $response->send();
                            }
                            // Else the required parameters are all present
                            else
                            {
                                try
                                {
                                    // So now build up the full parameter set
                                    // specified by the client, URL-decoding the
                                    // parameter values, trimming them if we're
                                    // supposed to, and specifying FALSE for
                                    // optional parameters that aren't in the
                                    // request
                                    $params = array();
                                    
                                    foreach ($requiredParams as $k => $v)
                                    {
                                        $params[$k] = $trimParamValues ? trim(urldecode($_REQUEST[$k])) : urldecode($_REQUEST[$k]);
                                    }
                                    
                                    $optionalParams = $this->getOptionalParameters();
                                    
                                    foreach ($optionalParams as $k => $v)
                                    {
                                        if (array_key_exists($k, $_REQUEST))
                                        {
                                            $params[$k] = $trimParamValues ? trim(urldecode($_REQUEST[$k])) : urldecode($_REQUEST[$k]);
                                        }
                                        else
                                        {
                                            $params[$k] = FALSE;
                                        }
                                    }
                                    
                                    // And finally, tell the implementing web
                                    // service to execute, passing it
                                    // information on the current user and the
                                    // full set of parameters
                                    $response = $this->execute($currentUser, $params);
                                    
                                    // If the web service actually returns
                                    // something and didn't do something else
                                    // like start a file download
                                    if ($response !== NULL)
                                    {
                                        $timeEnd = microtime(TRUE);
                                    
                                        // If the service execution took less than a
                                        // second, we'll delay very briefly as
                                        // returning too fast can cause problems for
                                        // some clients
                                        $execTime = $timeEnd - $timeStart;
                                        if ($execTime < 1)
                                        {
                                            usleep((1 - $execTime) * 1000000);
                                        }
                                    
                                        $response->send();
                                    }
                                }
                                catch (Exception $e)
                                {
                                    $response = new ServiceResponse(StatusCodes::ERROR, $e->getMessage());
                                    $response->send();
                                }
                            }
                        }
                    }
                }
                catch (Exception $e)
                {
                    $response = new ServiceResponse(StatusCodes::ERROR, $e->getMessage());
                    $response->send();
                }
            }
        }
        
        /**
         * This method can be overridden by service implementations to specify
         * any parameters the service requires in order to execute. By default,
         * this method returns an empty array, meaning that there are no
         * required parameters.
         * @return array An associative array of any required request parameters
         * that must be present in order for the web service to execute. The key
         * is the request parameter name; the value is a short description of
         * that parameter which will be put in the interface documentation
         * accessible to the client. If this web service does not require any
         * parameters to execute, return an empty array.
         */
        protected function getRequiredParameters()
        {
            return array();
        }
        
        /**
         * This method can be overridden by service implementations to specify
         * any optional parameters the service can use while executing. By
         * default, this method returns an empty array, meaning that there are
         * no optional parameters.
         * @return array An associative array of any optional request parameters
         * that can be present that this web service will use when it executes.
         * The key is the request parameter name; the value is a short
         * description of that parameter which will be put in the interface
         * documentation accessible to the client.
         */
        protected function getOptionalParameters()
        {
            return array();
        }
        
        /**
         * This method can be overridden by service implementations to specify
         * the data returned from the web service. By default, this method
         * returns NULL, meaning that no data is returned in the data property
         * of the response from the web service.
         * @return A string or non-associative array which describes the data
         * returned in the data property of the JSON-encoded object returned
         * from this web service. If no data is provided by this web service,
         * return NULL. If the web service doesn't return a JSON-encoded object
         * at all (for example, the web service starts a file download), return
         * FALSE. Otherwise, this return value can be one of three things:
         * 
         * 1. A string identifying the class name of the type of JSONEncodable
         *    object the result is. Note that simple scalar values can be
         *    returned by specifying an empty string.
         * 2. A non-associative array containing one value which is the string
         *    identifying the class name of the type of JSONEncodable objects
         *    present in each value of the array. You would use this type of
         *    value if the data consists of an array of objects.
         * 3. A non-associative array containing *no* values. This means that
         *    the data value is an array of scalar values.
         */
        protected function getDataReturned()
        {
            return NULL;
        }
        
        /**
         * This method can be overridden by service implementations to specify
         * the type of permission required to access the web service. By
         * default, this method returns NULL, meaning no permission is required
         * to use the web service.
         * @return \Spiel\Enum An Enum object indicating the category of
         * permission required by this web service. If no permissions are
         * required to access this web service, return NULL.
         */
        protected function getPermissionType()
        {
            return NULL;
        }
        
        /**
         * This method can be overridden by service implementations to specify
         * the permission required to access the web service. By default, this
         * method returns NULL, meaning no permission is required to use the web
         * service.
         * @return integer A value from the permission Enum that identifies the
         * permission required to access this web service. If no permissions are
         * required to access this web service, return NULL.
         */
        protected function getPermissionRequired()
        {
            return NULL;
        }
        
        /**
         * This method must be implemented by subclasses and will be called
         * when the implementing web service is being invoked and all required
         * prerequisites for invocation have been met (e.g., client's user is
         * logged in, required parameters are present in the request, etc.).
         * Note that any Exceptions thrown by this method will be caught and an
         * error response will be sent to the client. The method should return
         * a ServiceResponse object indicating the result of the service; this
         * response will be sent to the client.
         * @param \Spiel\ServiceUser $currentUser A ServiceUser object providing
         * information on the user currently logged into the system on the
         * client.
         * @param array $params An associative array of parameter names to
         * parameter values indicating the parameters applicable to this web
         * service and the values the client passed. For any optional parameters
         * which the client did not specify, the value will be FALSE. Note that
         * values are already urldecoded before being passed to this method.
         * @return \Spiel\ServiceResponse The response to return to the client.
         * If the web service does not return a standard response but instead
         * does something like start a file download, return NULL.
         */
        abstract public function execute(ServiceUser $currentUser, $params);
        
        // Echoes HTML interface documentation for a web service to the client.
        private function echoDocs($file,
                                  $description)
        {
            $name = basename(dirname($file)) . " / " . basename($file);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>


<head>

<title><?php echo $name; ?> Info</title>

<style type="text/css">

<?php echo $this->getDocStyles(); ?>

</style>

<script type="text/javascript">

function toggleEnum(id)
{
    var enumDiv = document.getElementById(id);
    if (enumDiv.style.display == "none")
    {
        enumDiv.style.display = "block";
    }
    else
    {
        enumDiv.style.display = "none";
    }
}

</script>

</head>


<body>

<h2><?php echo $name; ?></h2>

<hr size="1"/>

<p>
<?php echo $description; ?>
</p>

<hr size="1"/>

<br/>

<table>
<tr><th>Permission Required</th></tr>
<tr>
<?php
            $permissionType = $this->getPermissionType();
            $permissionRequired = $this->getPermissionRequired();
            
            if ($permissionRequired !== NULL)
            {
?>
<td>This web service requires that the requesting client has <i><?php echo $permissionType->$permissionRequired; ?></i> permission.</td>
<?php
            }
            else
            {
?>
<td>This web service does not require any permissions to be accessed.</td>
<?php
            }
?>
</tr>
</table>

<br/>

<table>
<tr><th colspan="2">Required Request Parameters</th></tr>
<?php
            $requiredParams = $this->getRequiredParameters();
            if (count($requiredParams) > 0)
            {
                foreach ($requiredParams as $k => $v)
                {
?>
<tr><td class="leftColumn"><b><?php echo $k; ?></b></td><td><?php echo $v; ?></td></tr>

<?php
                }
            }
            else
            {
?>
<tr><td colspan="2">[None]</td></tr>
<?php
            }
?>
</table>

<br/>

<table>
<tr><th colspan="2">Optional Request Parameters</th></tr>
<tr><td><b>x</b></td><td>Suppresses this help information and invokes the web service.</td></tr>

<?php
            $optionalParams = $this->getOptionalParameters();
            if (count($optionalParams) > 0)
            {
                foreach ($optionalParams as $k => $v)
                {
?>
<tr><td class="leftColumn"><b><?php echo $k; ?></b></td><td><?php echo $v; ?></td></tr>

<?php
                }
            }
?>
</table>

<br/>

<table>
<tr><th>Response</th></tr>
<tr><td>

<?php
            $dataReturned = $this->getDataReturned();
            if (isset($dataReturned) && ($dataReturned === FALSE))
            {
?>
This web service does not return a JSON-encoded response because its output is something else such as a file download, etc.<br/>
<?php
            }
            else
            {
?>

The response from using this web service is a JSON-encoded object that has the following structure:
<ul>
<li><b>status</b> - A status code indicating success (0) or failure (> 0). If failure is indicated, the data property will be null.</li>
<li><b>message</b> - A textual message associated with the success or failure of the web service.</li>
<?php
                if (isset($dataReturned))
                {
                    if (is_array($dataReturned))
                    {
                        // If there is something in the array, then it must be
                        // the name of a class
                        if (count($dataReturned) > 0)
                        {
?>
<li><b>data</b> - An array of objects, each of which is composed of the following:</li>

<?php
                            // The data returned can be an array or complex
                            // object, so spit out information on it
                            $this->echoDataReturned($dataReturned);
                        }
                        // Else there is nothing in the array, so it must be
                        // composed of scalar values
                        else
                        {
?>
<li><b>data</b> - An array of scalar values</li>

<?php
                        }
                    }
                    else if ($dataReturned != "")
                    {
?>
<li><b>data</b></li>

<?php
                        // The data returned can be an array or complex object,
                        // so spit out information on it
                        $this->echoDataReturned($dataReturned);
                    }
                    else
                    {
?>
<li><b>data</b> - A scalar value</li>

<?php
                    }
                }
                else
                {
?>
<li><b>data</b> - [null]</li>

<?php
                }
?>
</ul>

<?php
            }
?>
</td></tr>
</table>

</body>


</html>
<?php
        }
        
        // Used by the echoDocs() method.
        private function echoDataReturned($value, $propertyName = NULL)
        {
            // If the specified value is not an array, then it must be an object
            if (!is_array($value))
            {
                // So set up our class to inspect the object
                $class = new ReflectionClass($value);
            }
            // Else the specified value is an array
            else if (count($value) > 0)
            {
                // So pull the object name from the first value of the array and
                // set up our reflection class
                $class = new ReflectionClass($value[0]);
            }
            
            // If a property name is set
            if (isset($propertyName))
            {
                // If the specified value is an array
                if (is_array($value))
                {
                    // If there is something in the array, then it must be the
                    // name of a class, so output the property name
                    if (count($value) > 0)
                    {
                        echo "<li><b>" . $propertyName . "</b> - An array of objects, each of which is composed of the following:</li>\n";
                    }
                    // Else there is nothing in the array, so it must be
                    // composed of scalar values
                    else
                    {
                        echo "<li><b>" . $propertyName . "</b> - An array of scalar values</li>\n";
                    }
                }
                // Else the specified value is not an array
                else
                {
                    echo "<li><b>" . $propertyName . "</b></li>\n";
                }
            }
            
            // If we were able to establish a reflection class
            if (isset($class))
            {
                // Let's show information on it
                echo "<ul>\n";
                
                $properties = $class->getProperties();
                
                $objectProperties = array();
                $enumProperties = array();
                
                foreach ($properties as $property)
                {
                    // If this is the $objectProperties or $enumProperties
                    // static array
                    if ($property->isStatic())
                    {
                        if ($property->getName() == "objectProperties")
                        {
                            $objectProperties = $property->getValue();
                        }
                        else if ($property->getName() == "enumProperties")
                        {
                            $enumProperties = $property->getValue();
                        }
                    }
                }
                
                // If the class has at least one property
                if (count($properties) > 0)
                {
                    // For each such property
                    foreach ($properties as $property)
                    {
                        // If the property is public AND it's not static
                        if ($property->isPublic() && !$property->isStatic())
                        {
                            // If this property is enumerated in the class'
                            // $objectProperties array
                            if (isset($objectProperties[$property->getName()]))
                            {
                                $objectPropertyType = $objectProperties[$property->getName()];
                                
                                // If the specified property type is not an
                                // array, then it must be an object
                                if (!is_array($objectPropertyType))
                                {
                                    $objectPropertyClassName = $objectPropertyType;
                                }
                                // Else the specified property type is an array
                                else if (count($objectPropertyType) > 0)
                                {
                                    // So pull the object name from the first
                                    // value of the array
                                    $objectPropertyClassName = $objectPropertyType[0];
                                }
                                
                                // If this property isn't actually of the same
                                // type as its parent
                                if (!isset($objectPropertyClassName) || ($objectPropertyClassName != $class->getName()))
                                {
                                    // Then we'll descend into it and output its
                                    // information
                                    $this->echoDataReturned($objectProperties[$property->getName()], $property->getName());
                                }
                                // Else this property is of the same type as its
                                // parent and will therefore result in infinite
                                // recursion if we descend into it
                                else
                                {
                                    if (is_array($objectPropertyType))
                                    {
                                        echo "<li><b>" . $property->getName() . "</b> - An array of objects which are of the same type as the parent of this property</li>\n";
                                    }
                                    else
                                    {
                                        echo "<li><b>" . $property->getName() . "</b> - An object which is the same type as the parent of this property</li>\n";
                                    }
                                }
                            }
                            // Else if this property is enumerated in the class'
                            // $enumProperties array
                            else if (isset($enumProperties[$property->getName()]))
                            {
                                $enumPropertyType = $enumProperties[$property->getName()];
                                
                                // If the specified property is not an array,
                                // then it must be an object
                                if (!is_array($enumPropertyType))
                                {
                                    $enumPropertyClassName = $enumPropertyType;
                                }
                                // Else the specified property type is an array
                                else if (count($enumPropertyType) > 0)
                                {
                                    // So pull the object name from the first
                                    // value of the array
                                    $enumPropertyClassName = $enumPropertyType[0];
                                }
                                
                                $enumClass = new ReflectionClass($enumPropertyClassName);
                                $enumeration = $enumClass->newInstance();
                                
                                if (count($enumeration) > 0)
                                {
                                    // Used to make sure that two properties
                                    // with the same name in an object don't
                                    // both toggle the same enumeration
                                    $propIdSuffix = rand();
                                    
                                    if (!is_array($enumPropertyType))
                                    {
                                        echo "<li><b>" . $property->getName() . "</b> - An integer value from <a href=\"javascript:toggleEnum('" . $property->getName() . $propIdSuffix . "');\">this enumeration</a>.</li>\n";
                                    }
                                    else
                                    {
                                        echo "<li><b>" . $property->getName() . "</b> - An array of integer values from <a href=\"javascript:toggleEnum('" . $property->getName() . $propIdSuffix . "');\">this enumeration</a>.</li>\n";
                                    }
                                    echo "<div id=\"" . $property->getName() . $propIdSuffix . "\" style=\"display: none;\">\n";
                                    echo "<ul>\n";
                                    
                                    $enumValues = $enumeration->getValues();
                                    foreach ($enumValues as $enumValue)
                                    {
                                        echo "<li><b>" . $enumValue . "</b> - " . $enumeration->$enumValue . "</li>\n";
                                    }
                                    
                                    echo "</ul>\n";
                                    echo "</div>\n";
                                }
                                else
                                {
                                    echo "<li><b>" . $property->getName() . "</b></li>\n";
                                }
                            }
                            // Else if this property isn't enumerated
                            else
                            {
                                // Then we'll have to assume it's a simple
                                // scalar value and output its name
                                echo "<li><b>" . $property->getName() . "</b></li>\n";
                            }
                        }
                    }
                }
                // Else it has no properties on which we want to provide
                // information
                else
                {
                    echo "<li>[No properties]</li>\n";
                }
                echo "</ul>\n";
            }
        }
        
        private function getDocStyles($leftColumnWidth = 200)
        {
            return "body\n" .
                   "{\n" .
                   "    font-family:            \"Verdana\", \"Arial\";\n" .
                   "    font-size:              13px;\n" .
                   "}\n" .
                   "\n" .
                   "table\n" .
                   "{\n" .
                   "    font-size:              12px;\n" .
                   "    border: 1px solid       #4f4f6f;\n" .
                   "    border-collapse:        collapse;\n" .
                   "    width:                  100%;\n" .
                   "}\n" .
                   "\n" .
                   "th\n" .
                   "{\n" .
                   "    background:             #4f4f6f;\n" .
                   "    color:                  white;\n" .
                   "    padding:                2px 5px;\n" .
                   "    text-align:             left;\n" .
                   "}\n" .
                   "\n" .
                   "tr\n" .
                   "{\n" .
                   "    background:             #ffffff;\n" .
                   "    color:                  #1f1f27;\n" .
                   "}\n" .
                   "\n" .
                   "td\n" .
                   "{\n" .
                   "    border:                 1px solid #4f4f6f;\n" .
                   "    line-height:            150%;\n" .
                   "    padding:                4px 10px;\n" .
                   "}\n" .
                   "\n" .
                   "td.leftColumn\n" .
                   "{\n" .
                   "    vertical-align:         top;\n" .
                   "    width:                  " . $leftColumnWidth . "px;\n" .
                   "}\n";
        }
    }
?>