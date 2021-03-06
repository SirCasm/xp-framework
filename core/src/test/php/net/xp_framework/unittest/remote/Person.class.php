<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.remote';

  /**
   * Person object
   *
   * @see      xp://net.xp_framework.unittest.remote.SerializerTest
   * @purpose  Helper class for SerializerTest
   */
  class net�xp_framework�unittest�remote�Person extends Object {
    public
      $id     = 1549,
      $name   = 'Timm Friebe';

    public function __construct() {
      $this->id     = new Integer(1549);
      $this->name   = new String('Timm Friebe');
    }

    /**
     * Set Id
     *
     * @param   mixed id
     */
    public function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @return  mixed
     */
    public function getId() {
      return $this->id;
    }

    /**
     * Set Name
     *
     * @param   mixed name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  mixed
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Checks whether a given object is equal to this person.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->name == $this->name && $cmp->id == $this->id;
    }
  }
?>
