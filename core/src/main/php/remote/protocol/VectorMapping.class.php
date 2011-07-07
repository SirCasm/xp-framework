<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.collections.Vector', 
    'remote.protocol.SerializerMapping'
  );

  /**
   * Mapping for Hashmaps
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class VectorMapping extends Object implements SerializerMapping {

    /**
     * Returns a value for the given serialized string
     *
     * @param   server.protocol.Serializer serializer
     * @param   remote.protocol.SerializedData serialized
     * @param   [:var] context default array()
     * @return  var
     */
    public function valueOf($serializer, $serialized, $context= array()) {
      // No implementation
      $serialized->offset -= 2;
      $classString = $serializer->typeFor($serialized);
      $newInstance = Type::forName($classString)->newInstance();
      $serialized->consumeCharacter(':');
      $size = $serialized->consumeSize();
      $serialized->consumeCharacter('{'); 
      for ($i = 0; $i < $size; $i++) {
        $key = $serializer->valueOf($serialized);
        $newInstance->add($key);
      }
      $serialized->consumeCharacter('}');

      return $newInstance; 
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
        $serializedTypes = '';
        $genericArguments = $value->getClass()->genericArguments();
        $serializedTypes .= $serializer->serializeTypes($value->getClass());
        return $serializer->typeMapping[$value->getClass()->genericDefinition()->getName()].':['.$serializedTypes.']:'.$value->size().':{'.$this->serializeContent($serializer, $value).'}';
    }
  

    /**
     * Serialize the content of the HashTable
     *
     *
     */
    protected function serializeContent($serializer, $vector) {
      $serialized = '';
      $elements = $vector->elements();
      foreach ($elements as $element) {
        $serialized .= $serializer->representationOf($element);
      }
      return $serialized;
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @return  lang.XPClass
     */
    public function handledClass() {
      return Type::forName('util.collections.Vector');
    }
  } 
?>
