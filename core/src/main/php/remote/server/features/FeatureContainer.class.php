
<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.collections.HashTable',
    'remote.server.features.EascFeature',
    'lang.types.String'
  );
  
  /**
   * FeatureContainer
   */
  class FeatureContainer extends Object {
    public
      $features = NULL;


    /**
     *
     */
    public function __construct() { 
      $this->features = create('new HashTable<String, EascFeature>');
    }

    /**
     *
     */
    public function addFeature($name, $feature) {
      if (!$feature instanceof EascFeature) {
        return FALSE;
      }

      if ($feature->mandatory !== FALSE && $feature->mandatory !== TRUE)
      {
        throw new Exception('Each feature needs a boolean specifing whether the Feature is mandatory or optional');
      }
      
      if (!$name instanceof String) {
        $name = new String($name);
      }

      $this->features->put($name, $feature);

      return TRUE;
    }

    /**
     *
     */
    public function getFeatures() {
      return $this->features;
    }

    /**
     *
     */
    public function getFeature($name) {
      if (!$name instanceof String) {
        $name = new String($name);
      }

      return $this->features[$name];
    }
  }
?>

