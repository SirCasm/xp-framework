<?php

  abstract class EascFeature extends Object {
    public 
      $mandatory = TRUE;    

    /**
     * States whether the the feature is mandatory
     *
     */
    public function isMandatory() {
      return is_bool($this->mandatory) ? $this->mandatory : $this->mandatory->value;
    }

    abstract public function clientCheck(EascFeature $feature);

    abstract public function serverCheck(EascFeature $feature);
  }
?>

