<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Hashmap', 'remote.protocol.SerializerMapping');

  /**
   * Mapping for Hashmaps
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class HashTableMapping extends Object implements SerializerMapping {
    protected $typeMapping = array(
      'string'                      => 's',
      'int'                         => 'i',
      'double'                      => 'd',
      'boolean'                     => 'b',
      'NULL'                        => 'N',
      '<null>'                      => 'N',
      'util.collections.HashTable'  => 'M',
      'util.collections.Vector'     => 'V',
      'util.collections.HashSet'    => 'ST',
      'lang.types.Integer'          => 'i',
      'lang.types.Double'           => 'd',
      'lang.types.Short'            => 'S',
      'lang.types.Long'             => 'l',
      'lang.types.String'           => 's',
      'lang.types.Integer'          => 'i',
      'lang.types.Integer'          => 'i'
    );

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
        $value = $serializer->valueOf($serialized);
        $newInstance->put($key, $value);
      }
      $serialized->consumeCharacter('}');

      return $newInstance; 
    }

    public function getClassName() {
      return 'util.collections.HashTable';
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
        $serializedTypes .= $this->serializeTypes($value->getClass());
        return $this->typeMapping[$value->getClass()->genericDefinition()->getName()].':['.$serializedTypes.']:'.$value->size().':{'.$this->serializeContent($serializer, $value).'}';
    }


    /**
     * Serialize the content of the HashTable
     *
     *
     */
    protected function serializeContent($serializer, $hashmap) {
      $serialized = '';
      $keys = $hashmap->keys();
      foreach ($keys as $key) {
        $serialized .= $serializer->representationOf($key);
        $serialized .= $serializer->representationOf($hashmap->get($key));
      }
      return $serialized;
    }
    
    /**
     * Serialize the types of the generic arguments
     *
     */
    protected function serializeTypes(XPClass $definition) {
      $serializedTypes = '';
      $name = $definition->getName();
      $genericArguments = $definition->genericArguments();
      foreach ($genericArguments as $comp)
      {
        if ($comp instanceof Primitive) {
          $serializedTypes .= $this->typeMapping[$comp->getName()];
        } else if ($comp->isGeneric()) {
          $name = $comp->genericDefinition()->getName();
          $serializedTypes .= $this->typeMapping[$name].':['.$this->serializeTypes($comp).']';
        } else if (!isset($this->typeMapping[$comp->getName()])) {
          $serializedTypes .= 'O:'.strlen($comp->getName()).':"'.$comp->getName().'"';
        } else { 
          $serializedTypes .= $this->typeMapping[$comp->getName()];
        }
        // Add separator
        $serializedTypes .= ';';
      } 

      return $serializedTypes;
   }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @return  lang.XPClass
     */
    public function handledClass() {
      return Type::forName('util.collections.HashTable');
    }
  } 
?>
