<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.server.message.EascMessage');

  /**
   * EASC Init message
   *
   * @purpose  Init message
   */
  class EascFeaturesUsedMessage extends EascMessage {

    /**
     * Get type of message
     *
     * @return  int
     */
    public function getType() {
      return REMOTE_MSG_FEAT_USED;
    }

    /**
     * Checks the features again. 
     *
     * @return Boolean
     */
    public function handle($protocol, $data) {
      if (FALSE === ($bcsh= unpack('nlength/cnext', substr($data, 0, 3)))) return; 
      $data = new SerializedData(substr($data, 3, $bcsh['length'])); 

      $serverFeatures = $protocol->features->getFeatures();
      $clientFeatures = $protocol->serializer->valueOf($data);
      $this->checkFeatures($serverFeatures, $clientFeatures); 
      Console::writeLine('Check was successful');
    }

    /**
     * This method checks the Client's features against the 
     * server's features.
     *
     * @param HashTable<String,EascFeature> serverFeatures
     * @param HashTable<String,EascFeature> clientFeatures
     *
     */
    public function checkFeatures($serverFeatures, $clientFeatures) {
      // Keys from the Server and the client are necessary
      $keys = array_unique(
        array_merge(
          $serverFeatures->keys(), 
          $clientFeatures->keys()
      ));

      // Iterate through all features the server offers
      foreach($keys as $key) {
        $clientFeature = $clientFeatures[$key];
        $serverFeature = $serverFeatures[$key];
        // Both exist means the client will activate the feature
        if($clientFeature && $serverFeature) {
          $serverFeature->serverCheck($clientFeature);

        // Client does not support the feature and it's optional server side
        } elseif (!$clientFeature && $serverFeature && !$serverFeature->isMandatory()) {
          $this->cat && $this->cat->infof('Optional feature "%s" not supported by Client. Deactivating it.', $key->toString());
        // Server does not support the feature and it's optional client side
        $serverFeatures->remove($key);
        } elseif (!$serverFeature && $clientFeature && !$clientFeature->isMandatory()) {
          $this->cat && $this->cat->infof('Got unsupported feature "%s" from client.', $key->toString());
          throw new EascFeatureNotSupportedException('Client sent unsupported optional feature. This shouldn\'t happen and indicates a faulty client-implementation');
        } else {
          throw new EascFeatureNotSupportedException('Server cannot support the mandatory feature: '.$key->toString());
        }
      }
    }
  }
?>
