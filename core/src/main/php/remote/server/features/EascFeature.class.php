<?php

  /**
   * Abstract class to be extended by all Classes used
   * in the feature negotiation of EASC 2.0
   */
  abstract class EascFeature extends Object {
    public 
      $mandatory = TRUE; // This member MUST be public.

    /**
     * States whether the the feature is mandatory
     *
     * @return Boolean
     */
    public function isMandatory() {
      return is_bool($this->mandatory) ? $this->mandatory : $this->mandatory->value;
    }

    abstract public function clientCheck(EascFeature $serverFeature);

    abstract public function serverCheck(EascFeature $clientFeature);
  }
?>

