<?php
/* This class is part of the XP framework
 *
 * $Id: FinderMethod.class.php 10589 2007-06-08 16:27:58Z friebe $ 
 */

  namespace rdbms::finder;

  /**
   * Represents a finder method. Finder methods are methods inside
   * a rdbms.finder.Finder subclass that are decorated with the
   * "finder" annotation.
   *
   * There are two kinds of finder methods
   * <ol>
   *   <li>Those that return a single entity, finder(kind= ENTITIY)</li>
   *   <li>Those that return a collection fo entities, finder(kind= COLLECTION)</li>
   * </ol>
   *
   * @see      xp://rdbms.finder.Finder
   * @purpose  Method wrapper
   */
  class FinderMethod extends lang::Object {
    protected
      $finder= NULL, 
      $method= NULL;

    /**
     * Constructor
     *
     * @param   rdbms.finder.Finder finder
     * @param   lang.reflect.Method method
     */
    public function __construct($finder, $method) {
      $this->finder= $finder;
      $this->method= $method;
    }
    
    /**
     * Gets this method's kind
     *
     * @return  string kind one of ENTITY | COLLECTION
     */
    public function getKind() {
      return current($this->method->getAnnotation('finder'));
    }
 
    /**
     * Returns this method's name
     *
     * @return  string method name
     */
    public function getName() {
      return $this->method->getName();
    }
   
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(%s %s::%s())',
        $this->getClassName(),
        $this->getKind(),
        $this->finder->getClassName(),
        $this->method->getName()
      );
    }
  
    /**
     * Invokes this method
     *
     * @param   mixed[] args default array()
     * @return  mixed
     */
    public function invoke($args= array()) {
      try {
        return $this->method->invoke($this->finder, $args);
      } catch (lang::Throwable $e) {
        throw new FinderException($this->method->getName().' invocation failed', $e);
      }
    }
  }
?>