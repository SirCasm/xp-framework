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
      'lang.types.Integer'          => 'i',
      'lang.types.Double'           => 'd',
      'lang.types.Short'            => 'S',
      'lang.types.Long'             => 'l',
      'lang.types.String'           => 's',
      'lang.types.Integer'          => 'i',
      'lang.types.Integer'          => 'i',
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
      $serialized->offset -= 2;
      $classString = $this->typeFor($serialized);
      $newInstance = Type::forName($classString)->newInstance();
      $serialized->consumeCharacter(':');
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
      $token = $serialized->consumeNextToken();
      switch ($token) {
        case 'M':
          $classString = 'util.collections.HashTable<';
          $serialized->consumeCharacter('[');
          $classString .= $this->typeFor($serialized);
          $classString .= ',';
          $classString .= $this->typeFor($serialized); 
          if ($serialized->getCharacter() == ';') {
            $serialized->consumeCharacter(';');
          }
          $serialized->consumeCharacter(']');
          $classString .= '>';
          return $classString;
        break;
        case 'O':
          $classString = $serialized->consumeString();
          return $classString;
        break;
        case 's':
        case 'i':
        case 'd':
        case 'b':
          $classString = $this->tokenMapping[$token];
        return $classString;
        break;
        default:
          Console::writeLine('Error found character: '.$token);
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
/*        if ($comp->isSubClassOf(XPClass::forName('lang.types.Number'))) {
          $serializedTypes .= $this->typeMapping[$comp->getName()];
        } else if ($comp->isSubClassOf(XPClass::forName('lang.types.String'))) {
          $serializedTypes .= $this->typeMapping[$comp->getName()];
        } else if ($comp->isGeneric()) {
          $name = $comp->genericDefinition()->getName();

          // Lookup Type in the map and recursive call to get the generics' types
          $serializedTypes .= $this->typeMapping[$name].':['.$this->serializeTypes($comp).']';

        } else if ($comp instanceof Generic) {
          $serializedTypes .= 'O:'.strlen($comp->getName()).':"'.$comp->getName().'"';
        }
        */
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
