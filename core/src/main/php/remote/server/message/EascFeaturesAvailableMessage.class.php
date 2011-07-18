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
  class EascFeaturesAvailableMessage extends EascMessage {

    /**
     * Get type of message
     *
     * @return  int
     */
    public function getType() {
      return REMOTE_MSG_FEAT_AVAIL;
    }
  }
?>
