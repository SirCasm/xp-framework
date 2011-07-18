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
      if ($user != '' && $password != '') {
        $this->mandatory= TRUE;
      }
    }

    public function isMandatory() {
      return $mandatory;
    }
  }
?>

