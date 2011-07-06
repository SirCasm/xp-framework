<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.protocol.SerializerMapping',
    'lang.ChainedException'
  );

  /**
   * Mapping for lang.Throwable
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping for 
   */
  class ExceptionMapping extends Object implements SerializerMapping {

    /**
     * Returns a value for the given serialized string
     *
     * @param   server.protocol.Serializer serializer
     * @param   remote.protocol.SerializedData serialized
     * @param   [:var] context default array()
     * @return  var
     */
    public function valueOf($serializer, $serialized, $context= array()) {
      $class= XPClass::forName($serializer->exceptionName($serialized->consumeString()));

      $size= $serialized->consumeSize();
      $serialized->offset++;  // Opening "{"
      $data= array();
      for ($i= 0; $i < $size; $i++) {
        $member= $serializer->valueOf($serialized, $context);
        $element = $serializer->valueOf($serialized, $context);
        if ($member == 'trace') {
          $element= $element->elements();
        } else if ($member == 'message') {
          $element= $element->toString();
        }
        $data[$member]= $element;
      }
      $serialized->offset++; // Closing "}"
      
      $instance= $class->newInstance($data['message']);
      unset($data['message']);
      foreach (array_keys($data) as $name) {
        $instance->{$name}= $data[$name];
      }

      return $instance;
    }

    /**
     * Returns an on-the-wire representation of the given value
     *
     * @param   server.protocol.Serializer serializer
     * @param   lang.Object value
     * @param   [:var] context default array()
     * @return  string
     */
    public function representationOf($serializer, $value, $context= array()) {
      $trace= $value->getStackTrace();

      $traceTable = create('new Vector<lang.StackTraceElement>');
      $traceTable->addAll($trace);
      
      if (FALSE !== ($token= array_search($value->getClassName(), $serializer->exceptions, TRUE))) {
      
        // It's a known exception
        $s= 'e:'.strlen($token).':"'.$token.'":3:{';
      } else {
      
        // Generic exceptions
        $s= 'E:'.strlen($value->getClassName()).':"'.$value->getClassName().'":3:{';
      }
      $s.= '7:message;';
      $s.= $serializer->representationOf($value->getMessage());
      
      $s.= '5:trace;'.$serializer->representationOf($traceTable, $context);
      
      // Transfer cause
      $s.= '5:cause;'.(($value instanceof ChainedException) 
        ? $serializer->representationOf($value->getCause(), $context)
        : 'N:' //TODO: Remove all the hardcoded stuff
      );
      
      return $s.'}';
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @return  lang.XPClass
     */
    public function handledClass() {
      return XPClass::forName('lang.Throwable');
    }
  } 
?>
