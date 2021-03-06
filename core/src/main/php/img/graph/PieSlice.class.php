<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Shape class representing a 3D Pie chart's clice
   *
   * @deprecated
   * @see     xp://img.graph.PieChart
   * @purpose Value object
   */
  class PieSlice extends Object {
    public 
      $value    = 0.0,
      $caption  = '',
      $detached = FALSE,
      $colors   = array();
      
    /**
     * Constructor
     *
     * @param   float value
     * @param   var colors either an array of two colors, the second
     *          representing the shadow, or one color, for both lid and shadow
     */
    public function __construct($value, $colors) {
      $this->value= $value;
      if (!is_array($colors)) {
        $this->colors= array($colors, $colors);
      } else {
        $this->colors= $colors;
      }
    }

    /**
     * Set Value
     *
     * @param   int value
     */
    public function setValue($value) {
      $this->value= $value;
    }

    /**
     * Get Value
     *
     * @return  int
     */
    public function getValue() {
      return $this->value;
    }

    /**
     * Set Caption
     *
     * @param   string caption
     */
    public function setCaption($caption) {
      $this->caption= $caption;
    }

    /**
     * Get Caption
     *
     * @return  string
     */
    public function getCaption() {
      return $this->caption;
    }
  }
?>
