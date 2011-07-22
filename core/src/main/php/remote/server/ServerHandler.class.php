<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.protocol.XpProtocolConstants',
    'remote.server.message.EascMessageFactory',
    'lang.reflect.Proxy'
  );

  /**
   * Server handler
   *
   * @purpose  handler
   */
  class ServerHandler extends Object {
      
    /**
     * Set serializer
     *
     * @param   remote.protocol.Serializer serializer
     */
    public function setSerializer($serializer) {
      $this->serializer= $serializer;
    }  


    /**
     * Handle incoming data
     *
     * @param   peer.Socket socket
     * @param   peer.server.ServerProtocol protocol
     * @param   int type
     * @param   string data
     */
    public function handle($socket, $protocol, $type, $data) {
      try {
        $handler= EascMessageFactory::forType($type);
        $handler->handle($protocol, $data);

        $answerType = NULL;
        switch ($type) {
          case REMOTE_MSG_FEAT_USED:
            $answerType = REMOTE_MSG_INIT;
          break;
          default:
            $answerType = REMOTE_MSG_VALUE;
          break;
        }
        $response= EascMessageFactory::forType($answerType);
        $response->setValue($handler->getValue());

      } catch (Throwable $e) {
        $response= EascMessageFactory::forType(REMOTE_MSG_EXCEPTION);
        $response->setValue($e);
      }
      $protocol->answerWithMessage($socket, $response);
    }
  }
?>
