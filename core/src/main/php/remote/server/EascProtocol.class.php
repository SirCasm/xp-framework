<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.sys.ShmSegment',
    'peer.server.ServerProtocol',
    'remote.protocol.ByteCountedString',
    'remote.protocol.RemoteInterfaceMapping',
    'remote.protocol.Serializer',
    'remote.server.deploy.Deployer',
    'remote.server.features.FeatureContainer',
    'remote.server.features.SupportedTokens',
    'remote.server.features.AuthenticationFeature',
    'remote.server.message.EascFeaturesAvailableMessage',
    'remote.server.RemoteObjectMap',
    'remote.server.ServerHandler',
    'util.PropertyManager',
    'util.collections.HashTable',
    'util.log.FileAppender',
    'util.log.Logger'
  );
  
  /**
   * EASC protocol handler
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class EascProtocol extends Object implements ServerProtocol {
    public
      $serializer  = NULL,
      $context     = NULL,
      $features    = NULL,
      $scanner     = NULL;

    /**
     * Constructor
     *
     * @param   remote.server.deploy.scan.FileSystemScanner scanner
     */
    public function __construct($scanner) {
      $this->serializer= new Serializer();
      $this->serializer->mapping('I', new RemoteInterfaceMapping());
      $this->context[RemoteObjectMap::CTX_KEY]= new RemoteObjectMap();
      $this->scanner= $scanner;
      $this->cat = Logger::getInstance()->getCategory();
      $this->cat->withAppender(new FileAppender('/home/rene/devel/easc.log'));
      $this->deployer= new Deployer();

      $this->initializeFeatures();
    }

    /**
     * Initialize protocol
     *
     * @return  bool
     */
    public function initialize() {
      if ($this->scanner->scanDeployments()) {
        foreach ($this->scanner->getDeployments() as $deployment) {
          try {
            $this->deployer->deployBean($deployment);
          } catch (DeployException $e) {
            // Fall through
          }
        }
      }
      return TRUE; 
    }

    /**
     * Initializes the Features on the server.
     * TODO: Move this to its own class if possible
     */
    public function initializeFeatures() {
      $this->features = new FeatureContainer();
      $this->features->addFeature(new SupportedTokens($this->serializer->typeMapping));
      $props = PropertyManager::getInstance();
      
      if ($props->hasProperties('features')) {
        $featureProps = $props->getProperties('features');      
      }

      if ($featureProps->hasSection('Authentication')) {
        $credentials= $featureProps->readSection('Authentication');
        if (!$credentials['user'] || !$credentials['password'])
        {
          throw new EascFeatureNotSupportedException('There was no user or password for the AuthenticationFeature supplied.');
        }
        // The Password is not written to the class to avoid sending
        // it over the wire
        $auth = new AuthenticationFeature();
        $auth->setServerCredentials($credentials['user'], $credentials['password']);
        $this->features->addFeature($auth);
      }
    }

    /**
     * Write answer
     *
     * @param   io.Stream stream
     * @param   int type
     * @param   var data
     */
    protected function answer($stream, $type, $data) {
      $length= strlen($data);
      $packet= pack(
        'Nc4Na*', 
        0x3c872747, 
        2,
        0,
        $type,
        FALSE,
        $length,
        $data
      );
      $stream->write($packet);
    }

    /**
     * Write answer
     *
     * @param   io.Stream stream
     * @param   int type
     * @param   remote.protocol.ByteCountedString[] bcs
     */
    protected function answerWithBytes($stream, $type, $bcs) {
      $header= pack(
        'Nc4Na*', 
        0x3c872747, 
        2,
        0,
        $type,
        FALSE,
        $bcs->length(),
        ''
      );

      $stream->write($header);
      $bcs->writeTo($stream);
    }
    
    /**
     * Write answer
     *
     * @param   io.Stream stream
     * @param   remote.server.message.EascMessage message
     */
    public function answerWithMessage($stream, $m) {
      $this->answerWithBytes(
        $stream,
        $m->getType(),
        new ByteCountedString($this->serializer->representationOf($m->getValue(), $this->context))
      );
    }

    /**
     * Read bytes from socket
     *
     * @param   peer.Socket sock
     * @param   int num
     * @return  string
     */
    protected function readBytes($sock, $num) {
      $return= '';
      while (strlen($return) < $num) {
        if (0 == strlen($buf= $sock->readBinary($num - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }
    
    /**
     * Extract a string out of packed data
     *
     * @param   string data
     * @param   int offset
     * @return  string
     */
    public function readString($data, &$offset) {
      $string= '';
      do {
        $ctl= unpack('nlength/cnext', substr($data, $offset, 4));
        $string.= substr($data, $offset+ 3, $ctl['length']);
        $offset+= $ctl['length']+ 1;
      } while ($ctl['next']);

      return utf8_decode($string);
    }    
    
    /**
     * Handle client connect
     *
     * @param   peer.Socket socket
     */
    public function handleConnect($socket) {
      $this->initializeFeatures();
      $message = new EascFeaturesAvailableMessage();
      $message->setValue($this->features->getFeatures());
      $this->answerWithMessage($socket, $message); 
    }

    /**
     * Handle client disconnect
     *
     * @param   peer.Socket socket
     */
    public function handleDisconnect($socket) { }
  
    /**
     * Handle client data
     *
     * @param   peer.Socket socket
     * @return  var
     */
    public function handleData($socket) {
      $this->cat->info('Getting info');
      // Check if socket on eof
      if (NULL === ($bytes= $this->readBytes($socket, 12))) return;

      $header= unpack(
        'Nmagic/cvmajor/cvminor/ctype/ctran/Nlength', 
        $bytes
      );

      if (0x3c872747 != $header['magic']) {
        $this->answer($socket, 0x0007 /* REMOTE_MSG_ERROR */, 'Magic number mismatch');
        return NULL;
      }
      
      $impl= new ServerHandler();
      $impl->setSerializer($this->serializer);
      
      return $impl->handle($socket, $this, $header['type'], $this->readBytes($socket, $header['length']));
    }
    
    /**
     * Handle I/O error
     *
     * @param   peer.Socket socket
     * @param   lang.Exception e
     */
    public function handleError($socket, $e) { }

  } 
?>
