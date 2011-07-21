<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.collections.HashSet',
    'remote.server.features.EascFeature',
    'remote.server.features.EascFeatureNotSupportedException'
  );

  /**
   * SupportedTokens
   */
  class SupportedTokens extends EascFeature {
    public 
      $tokens = NULL;

    public function __construct($tokenArray= NULL) {
      $this->tokens = create('new HashSet<lang.types.String>');

      foreach ($tokenArray as $key => $value) {
        $this->tokens->add(new String($value));
      }
    }

    /**
     * Returns the HashSet<String> containing the
     * tokens
     */ 
    public function getTokens() {
      return $this->tokens;
    }

    /**
     * Client-side check for the authentication
     *
     * @return Boolean
     */
    public function clientCheck(EascFeature $serverFeature) {
      if (!($serverFeature instanceof self)) {
        // TODO: Find better Exception type
        throw new EascFeatureNotSupportedException('Given EascFeature is not of type '.$this->getClass()->getClassName());
      }

      if (!$serverFeature->tokens) {
        throw new FormatException('SupportedToken must contain a HashSet<String> of tokens');
      }
      
      $iter = $this->tokens->getIterator();

      while ($iter->valid()) {
        $token = $iter->current();
        if (!$serverFeature->tokens->contains($token)) {
          // TODO: Find better Exception type
          throw new Exception('Unsupported Token found.');
        }
        $token = $iter->next();
      }
      return TRUE;
    }

    /**
     * Server-side check for the authentication
     *
     * @return Boolean
     */
    public function serverCheck(EascFeature $clientFeature) {
      return $clientFeature->clientCheck($this); 
    }
  }
?>

