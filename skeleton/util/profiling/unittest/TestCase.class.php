<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.AssertionFailedError',
    'lang.MethodNotImplementedException'
  );

  /**
   * Test case
   *
   * @see      php://assert
   * @purpose  Base class
   */
  class TestCase extends Object {
    var
      $name     = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      $this->name= $name;
      assert_options(ASSERT_ACTIVE, 1);
      assert_options(ASSERT_WARNING, 1);
      assert_options(ASSERT_CALLBACK, array('TestCase', 'fail'));
      parent::__construct();
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Private helper method
     *
     * @model   static
     * @access  private
     * @param   mixed* arg
     * @return  mixed* arg
     */
    function store() {
      static $store;
      
      if (0 == func_num_args()) {
        return $store;
      }
      $store= func_get_args();
    }

    /**
     * Private helper method
     *
     * @access  private
     * @param   mixed expr
     * @param   string reason
     * @param   mixed actual default NULL
     * @return  mixed expr
     */
    function test($expr, $reason, $actual= NULL) {
      TestCase::store($reason, $actual);
      return $expr;
    }
    
    /**
     * Callback for assert
     *
     * @model   static
     * @access  magic
     * @param   string filee
     * @param   int line
     * @param   string code
     */
    function fail($file, $line, $code) {
      list($reason, $actual)= TestCase::store();
      throw(new AssertionFailedError(
        $reason, 
        $actual, 
        substr($code, 12, strpos($code, '"')- 14)
      ));
    }
    
    /**
     * Assert that a value's type is boolean
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notbool'
     * @return  bool
     */
    function assertBoolean($var, $error= 'notbool') {
      return assert('$this->test(is_bool($var), $error, gettype($var))');
    }
    
    /**
     * Assert that a value's type is float
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notfloat'
     * @return  bool
     */
    function assertFloat($var, $error= 'notfloat') {
      return assert('$this->test(is_float($var), $error, gettype($var))');
    }
    
    /**
     * Assert that a value's type is integer
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notinteger'
     * @return  bool
     */
    function assertInteger($var, $error= 'notinteger') {
      return assert('$this->test(is_int($var), $error, gettype($var))');
    }

    /**
     * Assert that a value's type is string
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notstring'
     * @return  bool
     */
    function assertString($var, $error= 'notstring') {
      return assert('$this->test(is_string($var, $error, gettype($var)))');
    }

    /**
     * Assert that a value's type is null
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notnull'
     * @return  bool
     */
    function assertNull($var, $error= 'notnull') {
      return assert('$this->test(is_null($var), $error, gettype($var))');
    }
    
    /**
     * Assert that a value is an array
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notarray'
     * @return  bool
     */
    function assertArray($var, $error= 'notarray') {
      return assert('$this->test(is_array($var), $error, gettype($var))');
    }
    
    /**
     * Assert that a value is an object
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notobject'
     * @return  bool
     */
    function assertObject(&$var, $error= 'notobject') {
      return assert('$this->test(is_object($var), $error, gettype($var))');
    }
    
    /**
     * Assert that a value is empty
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     * @param   string error default 'notempty'
     * @see     php://empty
     */
    function assertEmpty($var, $error= 'notempty') {
      assert('$this->test(empty($var), $error, $var)');
    }

    /**
     * Assert that a value is not empty
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     * @param   string error default 'empty'
     * @see     php://empty
     */
    function assertNotEmpty($var, $error= 'empty') {
      assert('$this->test(!empty($var), $error, $var)');
    }

    /**
     * Assert that two values are equal
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @param   string error default 'notequal'
     * @return  bool
     */
    function assertEquals($a, $b, $error= 'notequal') {
      return assert('$this->test($a === $b, $error, array($a, $b))');
    }
    
    /**
     * Assert that two values are not equal
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @param   string error default 'equal'
     * @return  bool
     */
    function assertNotEquals($a, $b, $error= 'equal') {
      return assert('$this->test($a !== $b, $error, array($a, $b))');
    }

    /**
     * Assert that a value is true
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'nottrue'
     * @return  bool
     */
    function assertTrue($var, $error= 'nottrue') {
      if ($r= $this->assertBoolean($var, $error)) {
        $r= assert('$this->test($var === TRUE, $error, $var)');
      }
      return $r;
    }
    
    /**
     * Assert that a value is false
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notfalse'
     * @return  bool
     */
    function assertFalse($var, $error= 'notfalse') {
      if ($r= $this->assertBoolean($var, $error)) {
        $r= assert('$this->test($var === FALSE, $error, $var)');
      }
      return $r;
    }
    
    /**
     * Assert that a value matches a given pattern
     *
     * @access  public
     * @param   mixed var
     * @param   string pattern
     * @param   string error default 'nomatches'
     * @return  bool
     * @see     php://preg_match
     */
    function assertMatches($var, $pattern, $error= 'nomatches') {
      return assert('$this->test(preg_match($var, $pattern), $error, array($var, $pattern))');
    }
    
    /**
     * Assert that a given object is of a specified class
     *
     * @access  public
     * @param   &lang.Object var
     * @param   string name
     * @param   string error default 'notequal'
     * @return  bool
     */
    function assertClass(&$var, $name, $error= 'notequal') {
      if ($r= $this->assertObject($var, $error)) {
        $r= assert('$this->test($var->getClassName() === $name, $error, $var->getClassName())');
      }
      return $r;
    }

    /**
     * Assert that a given object is a subclass of a specified class
     *
     * @access  public
     * @param   &lang.Object var
     * @param   string name
     * @param   string error default 'notsubclass'
     * @return  bool
     */
    function assertSubclass(&$var, $name, $error= 'notsubclass') {
      if ($r= $this->assertObject($var, $error)) {
        $c= array_search($name, $GLOBALS['php_class_names']);
        $r= assert('$this->test(is_a($var, $c), $error, $name)');
      }
      return $r;
    }
    
    /**
     * Assert that a value is contained in a list
     *
     * @access  public
     * @param   mixed var
     * @param   array list
     * @param   string error default 'notinlist'
     * @return  bool
     */
    function assertIn($var, $list, $error= 'notinlist') {
      return assert('$this->test(in_array($var, $list, TRUE), $error, $list)');
    }

    /**
     * Set up this test. Overwrite in subclasses.
     *
     * @model   abstract
     * @access  public
     * @return  mixed anything except NULL to indicate this test should be skipped
     */
    function setUp() { }
    
    /**
     * Tear down this test case. Overwrite in subclasses.
     *
     * @model   abstract
     * @access  public
     */
    function tearDown() { }
    
    /**
     * Run this test case.
     *
     * @access  public
     * @return  &mixed return value of test method
     * @throws  lang.MethodNotImplementedException
     */
    function &run() {
      if (!method_exists($this, $this->name)) {
        return throw(new MethodNotImplementedException(
          'Method '.$this->name.' does not exist'
        ));
      }
      return call_user_func(array(&$this, $this->name));
    }
  }
?>
