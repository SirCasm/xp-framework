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
      'string' => 's',
      'int' => 'i',
      'double' => 'd',
      'boolean' => 'b',
      'NULL' => 'N',
      '<null>' => 'N',
      'util.collections.HashTable' => 'M',
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
//      var_dump($serialized); 

//      var_dump($serialized->consumeWord());
//      var_dump($serialized->consumeWord());

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
        $serializedTypes .= $this->handleGenericArgs($value->getClass());

        return 'M['.$serializedTypes.']:'.$value->size().':{'.$this->serializeContent($serializer, $value).'}';
    }

    public function serializeContent($serializer, $hashmap) {
      $serialized = '';
      foreach ($hashmap->keys() as $key) {
        $serialized .= $serializer->representationOf($key).$serializer->representationOf($hashmap->get($key));
      }
      rtrim($serialized, ';');
      return $serialized;
    }
    
    /**
     *
     *
     *
     */
    public function handleGenericArgs(XPClass $definition) {
      $serializedTypes = '';
      $name = $definition->getName();
      $genericArguments = $definition->genericArguments();
      foreach ($genericArguments as $comp)
      {
        if ($comp instanceof Primitive) {
          $serializedTypes .= $this->typeMapping[$comp->name];
        } else if ($comp->isGeneric()) {
          $name = $comp->genericDefinition()->getName();

          // Lookup Type in the map and recursive call to get the generics' types
          $serializedTypes .= $this->typeMapping[$name].'['.$this->handleGenericArgs($comp).']';

        } else if ($comp instanceof Generic) {
          $serializedTypes .= 'O:'.strlen($comp->getName()).':"'.$comp->getName().'"';
        }
        // Add separator
        $serializedTypes .= ';';
      }
      // Remove last separator
      $serializedTypes = rtrim($serializedTypes, ';');
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
