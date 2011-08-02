<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'remote.server.features.EascFeature',
    'peer.AuthenticationException'
  );
  
  /**
   * AuthenticationFeature
   */
  class AuthenticationFeature extends EascFeature {
    public
      $user= '',
      $password='';

    private
      $_user,
      $_password;

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
     * Write the server's credentials to the private members _user
     * and _password. This is necessary so they won't get sent over
     * the wire.
     */
    public function setServerCredentials($user = '', $password='') {
      $this->_user = $user;
      $this->_password = $password;
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
      Console::writeLine(
        sprintf('user: %s pass: %s user: %s pass: %s', $this->_user, $this->_password, $clientFeature->user, $clientFeature->password)
      );
      if (
        $this->_user == $clientFeature->user && 
        $this->_password == $clientFeature->password) {

        return TRUE;

      } else {
        throw new AuthenticationException('Wrong user or password given');
      }
    }
  }
?>

