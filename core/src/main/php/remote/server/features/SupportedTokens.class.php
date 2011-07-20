
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
   *
   *
   */
  class SupportedTokens extends Object implements EascFeature {
    public 
      $mandatory = TRUE,
      $tokens = NULL;

    public function __construct($tokenArray= NULL) {
      $this->tokens = create('new HashSet<lang.types.String>');

      foreach ($tokenArray as $key => $value) {
        $this->tokens->add(new String($value));
      }
    }

    public function isMandatory() {
      return is_bool($this->mandatory) ? $this->mandatory :  $this->mandatory->value;
    }

    public function getTokens() {
      return $this->tokens;
    }

    public function handle(EascFeature $feature) {
      if (!($feature instanceof self)) {
        // TODO: Find better Exception type
        throw new EascFeatureNotSupportedException('Given EascFeature is not of type '.$this->getClass()->getClassName());
      }

      if (!$feature->tokens) {
        throw new FormatException('SupportedToken must contain a HashSet with Tokens');
      }
      
      $iter = $this->tokens->getIterator();
      while ($iter->valid()) {
        $token = $iter->current();
        if (!$feature->tokens->contains($token)) {
          // TODO: Find better Exception type
          throw new Exception('Unsupported Token found.');
        }
        $token = $iter->next();
      }
      return TRUE;
    }
  }
?>

