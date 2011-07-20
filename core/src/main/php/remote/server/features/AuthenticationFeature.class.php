<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'remote.server.features.EascFeature'
  );
  
  /**
   * AuthenticationFeature
   *
   *
   */
  class AuthenticationFeature extends Object implements EascFeature {
    public
      $mandatory= FALSE,
      $user= '',
      $password='';

    public function __construct($user= '', $password= '') {
      $this->user= $user;
      $this->password= $password;
      if ($user&& $password) {
        $this->mandatory= TRUE;
      }
    }

    public function isMandatory() {
      return is_bool($this->mandatory) ? $this->mandatory :  $this->mandatory->value;
    }

    public function handle(EascFeature $feature) {
      if (!($feature instanceof self)) {
        // TODO: Find better Exception type
        throw new Exception('Given EascFeature is not of type '.$this->getClass()->getClassName());
      }

      return TRUE;
    }
  }
?>

