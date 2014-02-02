<?php
    namespace Spiel;
    
    /**
     * This is a tagging interface which identifies classes whose instances can
     * be encoded using the json_encode() function of PHP. Implementing classes
     * must declare the attributes to be included in the encoded value as
     * public. In addition, you can define two public static properties of the
     * implementing class which provide additional metadata to Spiel when
     * interface documentation is produced for a service returning an instance
     * of the class:
     * 
     *     public static $objectProperties      = array();
     *     public static $enumProperties        = array();
     * 
     * The $objectProperties array provides information on any public attributes
     * of the class which are themselves composed of JSONEncodable objects. The
     * $enumProperties array provides information on any public attributes which
     * are actually instances of an Enum object. In both cases, the array
     * element is a key/value pair with the name of the attribute as the key and
     * the name of its type as its value. If the described attribute actually
     * provides an array, then the value of the key/value pair would be an array
     * containing a string identifying the name of each element's type. Consider
     * the following example:
     * 
     * class Employee implements \Spiel\JSONEncodable
     * {
     *     public static $objectProperties =    array("education"    => "EducationInfo",
     *                                                "skills"       => array("Skill"));
     *     
     *     public static $enumProperties =      array("roles"        => array("EmployeeRole"),
     *                                                "status"       => "EmploymentStatus");
     *     
     *     public $firstName;   // Scalar value
     *     public $lastName;    // Scalar value
     *     public $employeeId;  // Scalar value
     *     public $education;   // An EducationInfo object
     *     public $skills;      // An array of Skill objects
     *     public $roles;       // An array of EmployeeRole enumeration values
     *     public $status;      // An EmploymentStatus enumeration value
     *     
     *     ...
     * }
     * 
     * Note that one thing to keep in mind is that the actual JSONEncodable
     * object returned in a particular service invocation may be an instance of
     * a derived class of that identified in the $objectProperties attribute. In
     * such a case, more data than is indicated by the class identified can be
     * returned if the object's derived class has more public attributes which
     * can be encoded. This will not necessarily cause an issue but it can
     * result in more data than you expected being returned for certain
     * invocations of the service in question. One possible approach to dealing
     * with this is to implement a "slicing copy" method in the base class which
     * returns a copy of the object but as a new instance of the base class. The
     * following service example illustrates this approach.
     * 
     * <?php
     *     require_once("spiel.php");
     *     require_once("Permissions.php");
     *     require_once("ServiceUserManagerImpl.php");
     *     
     *     class Base implements \Spiel\JSONEncodable
     *     {
     *         public $prop1;
     *         public $prop2;
     *         
     *         public function __construct($prop1, $prop2)
     *         {
     *             $this->prop1 = $prop1;
     *             $this->prop2 = $prop2;
     *         }
     *         
     *         public static function slicingCopy(Base $source = NULL)
     *         {
     *             $retVal = NULL;
     *             
     *             if ($source !== NULL)
     *             {
     *                 // Ignore $source->prop3 as it's not in Base
     *                 $retVal = new Base($source->prop1, $source->prop2);
     *             }
     *             
     *             return $retVal;
     *         }
     *     }
     * 
     *     class Sub extends Base
     *     {
     *         public $prop3;
     *         
     *         public function __construct($prop1, $prop2, $prop3)
     *         {
     *             parent::__construct($prop1, $prop2);
     *             
     *             $this->prop3 = $prop3;
     *         }
     *     }
     *     
     *     class ServiceImpl extends \Spiel\Service
     *     {
     *         public function __construct()
     *         {
     *             parent::__construct(new ServiceUserManagerImpl(),
     *                                 __FILE__,
     *                                 "Example service which shows the use of a \"slicing copy\" to enforce the data that is returned.",
     *                                 new Permissions(),
     *                                 NULL,
     *                                 NULL,
     *                                 NULL,
     *                                 "Base");
     *         }
     *         
     *         public function execute(\Spiel\ServiceUser $currentUser, $params)
     *         {
     *             $dataReturned = $this->getBaseInstance();
     *             $dataReturned = Base::slicingCopy($dataReturned);
     *             
     *             return new \Spiel\SuccessServiceResponse($dataReturned);
     *         }
     *         
     *         private function getBaseInstance()
     *         {
     *             // Imagine this method doing more first, but the point is that it is
     *             // returning an instance of a Sub object
     *             return new Sub("prop1 value", "prop2 value", "prop3 value");
     *         }
     *     }
     *     
     *     new ServiceImpl();
     * ?>
     * 
     */
    interface JSONEncodable { }
?>