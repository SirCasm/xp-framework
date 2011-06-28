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

    protected $tokenMapping = array(
      's' => 'lang.types.String',
      'i' => 'lang.types.Integer',
      'd' => 'lang.types.Double', 
      'b' => 'lang.types.Boolean',
      'M' => 'util.collections.HashTable'
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
      $serialized->offset -= 3;
      $classString = $this->typeFor($serialized);
      $newInstance = Type::forName($classString)->newInstance();
      $size = $serialized->consumeSize();
      $serialized->consumeCharacter('{'); 
      for ($i = 0; $i < $size; $i++) {
        $key = $serializer->valueOf($serialized);
        $value = $serializer->valueOf($serialized);
        $newInstance->put($key, $value);
      }

      return $newInstance; 
    }

    /**
     *
     *
     *
     *
     *
     */
    public function typeFor($serialized) {
      $classString = '';
      switch ($serialized->consumeSize()) {
        case 'M':
          $classString = 'util.collections.HashTable<';
          $type = $serialized->consumeType();
          $classString .= $this->typeFor($serialized);
          $classString .= ',';
          $classString .= $this->typeFor($serialized); 
          $classString .= '>';
          return $classString;
        break;
        case 'O':
          $size = $serialized->consumeSize();
          $classString = $serialized->consumeString();
          if ($serialized->getCharacter() == ']') {
            $serialized->consumeCharacter(']');
          }
          if ($serialized->getCharacter() == ':') {
            $serialized->consumeCharacter(':');
          }
          return $classString;
        break;
        case 's':
        case 'i':
        case 'd':
        case 'b':
          switch ($serialized->getCharacter(1)) {
            case ';':
              $classString = $this->tokenMapping[$serialized->consumeWord()];
            break;
            case ']':
              $classString = $this->tokenMapping[$serialized->consumeTypeEnd()];
              if ($serialized->getCharacter() == ':') {
                $serialized->consumeCharacter(':');
              }
            break;
          }
          return $classString;
        break;
        default:
          Console::writeLine('Error found character: '.$serialized->getCharacter());
        break;
      }

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
          $serializedTypes .= $this->typeMapping[$comp->name];
        } else if ($comp->isGeneric()) {
          $name = $comp->genericDefinition()->getName();

          // Lookup Type in the map and recursive call to get the generics' types
          $serializedTypes .= $this->typeMapping[$name].':['.$this->serializeTypes($comp).']';

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
