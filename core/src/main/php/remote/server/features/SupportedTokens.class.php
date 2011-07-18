
<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.collections.HashTable',
    'remote.server.features.EascFeature'
  );

  /**
   * SupportedTokens
   *
   *
   */
  class SupportedTokens extends Object implements EascFeature {
    public 
      $mandatory = TRUE,
      $tokens = NULL;

    public function __construct($tokenArray) {
      $this->tokens = create('new HashTable<lang.types.String, lang.types.String>');

      foreach ($tokenArray as $key => $value) {
        $this->tokens->put(new String($key),new String($value));
      }
    }

    public function isMandatory() {
      return $this->mandatory;
    }

    public function getTokens() {
      return $this->tokens;
    }
  }
?>

