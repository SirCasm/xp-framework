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
   */
  class AuthenticationFeature extends EascFeature {
    public
      $user= '',
      $password='';


    /**
     * Constructor
     */
    public function __construct($user= '', $password= '') {
      $this->user= $user;
      $this->password= $password;
      $this->mandatory= TRUE; // Authentication should never be optional
    }
    
    /**
     * Client-side check for the authentication
     *
     * @return Boolean
     */
    public function clientCheck(EascFeature $serverFeature) {
      if (!($serverFeature instanceof self)) {
        // TODO: Find better Exception type
        throw new Exception('Given EascFeature is not of type '.$this->getClass()->getClassName());
      }

      return TRUE;
    }

    /**
     * Server-side check for the authentication
     *
     * @return Boolean
     */
    public function serverCheck(EascFeature $clientFeature) {
      if (!($clientFeature instanceof self)) {
        // TODO: Find better Exception type
        throw new Exception('Given EascFeature is not of type '.$this->getClass()->getClassName());
      }
    }
  }
?>

