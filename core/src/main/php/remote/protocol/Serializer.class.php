<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.Enum',
    'remote.ClassReference',
    'remote.ExceptionReference',
    'remote.protocol.ArrayListMapping',
    'remote.protocol.ByteArrayMapping',
    'remote.protocol.ByteMapping',
    'remote.protocol.DateMapping',
    'remote.protocol.DoubleMapping',
    'remote.protocol.EnumMapping',
    'remote.protocol.ExceptionMapping',
    'remote.protocol.FloatMapping',
    'remote.protocol.HashmapMapping',
    'remote.protocol.HashTableMapping',
    'remote.protocol.IntegerMapping',
    'remote.protocol.LongMapping',
    'remote.protocol.SerializedData',
    'remote.protocol.ShortMapping',
    'remote.protocol.StackTraceElementMapping',
    'remote.protocol.VectorMapping',
    'remote.UnknownRemoteObject',
    'util.collections.HashTable'
  );

  /**
   * Class that reimplements PHP's builtin serialization format.
   *
   * @see      php://serialize
   * @test     xp://net.xp_framework.unittest.remote.SerializerTest
   * @purpose  Serializer
   */
  class Serializer extends Object {
    public
      $mappings   = array(),
      $packages   = array(0 => array(), 1 => array()),
      $exceptions = array();
    
    public
      $_classMapping  = array();

    /**
     * Constructor. Initializes the default mappings
     *
     */
    public function __construct() {
      $this->mappings['T']= new DateMapping();
      $this->mappings['l']= new LongMapping();
      $this->mappings['B']= new ByteMapping();
      $this->mappings['S']= new ShortMapping();
      $this->mappings['f']= new FloatMapping();
      $this->mappings['d']= new DoubleMapping();
      $this->mappings['i']= new IntegerMapping();
      $this->mappings['A']= new ArrayListMapping();
      $this->mappings['e']= new ExceptionMapping();
      $this->mappings['t']= new StackTraceElementMapping();
      $this->mappings['Y']= new ByteArrayMapping();
      $this->mappings['M']= new HashTableMapping();
      $this->mappings['V']= new VectorMapping();
      
      // A hashmap doesn't have its own token, because it'll be serialized
      // as an array. We use HASHMAP as the token, so it will never match
      // another one (can only be one char). This is a little bit hackish.
      $this->mappings['HASHMAP']= new HashmapMapping();
      
      // Setup default exceptions
      $this->exceptions['IllegalArgument']= 'lang.IllegalArgumentException';
      $this->exceptions['IllegalAccess']= 'lang.IllegalAccessException';
      $this->exceptions['ClassNotFound']= 'lang.ClassNotFoundException';
      $this->exceptions['NullPointer']= 'lang.NullPointerException';
    }

    /**
     * Retrieve serialized representation of a variable
     *
     * @param   var var
     * @return  string
     * @throws  lang.FormatException if an error is encountered in the format 
     */  
    public function representationOf($var, $ctx= array()) {
      switch ($type= xp::typeOf($var)) {
        case '<null>': case 'NULL': 
          return 'N;';

        case 'boolean': 
          return 'b:'.($var ? 1 : 0).';';

        case 'integer': 
          return 'i:'.$var.';';

        case 'double': 
          return 'd:'.$var.';';

        case 'lang.types.String':
          $var = $var->toString();
        case 'string':
          $encoded = utf8_encode($var);
          return 's:'.strlen($encoded).':"'.$encoded.'";';

        case 'array':
          $s= 'a:'.sizeof($var).':{';
          foreach (array_keys($var) as $key) {
            $s.= serialize($key).$this->representationOf($var[$key], $ctx);
          }
          return $s.'}';

        case 'resource': 
          return ''; // Ignore (resources can't be serialized)

        case $var instanceof Generic: {
          if (FALSE !== ($m= $this->mappingFor($var))) {
            return $m->representationOf($this, $var, $ctx);
          }
          
          // Default object serializing
          $props= get_object_vars($var);
          $type= strtr($type, $this->packages[1]);
          
          unset($props['__id']);
          $s= 'O:'.strlen($type).':"'.$type.'":'.sizeof($props).':{';
          foreach (array_keys($props) as $name) {
            $s.= serialize($name).$this->representationOf($var->{$name}, $ctx);
          }
          return $s.'}';
        }



        default: 
          throw new FormatException('Cannot serialize unknown type '.$type);
      }
    }

    /**
     * Resolves types for generic Collections when 
     * deserializing. 
     *
     * @param remote.protocol.SerializedData serialized
     *
     */
    public function typeFor($serialized) {
      $classString = '';
      $token = $serialized->consumeNextToken();
      switch ($token) {
        case 'M':
          $baseType = $this->mappings[$token]->handledClass()->getName();
          $serialized->consumeCharacter('[');
          $typeOne = $this->typeFor($serialized);
          $typeTwo .= $this->typeFor($serialized); 
          if ($serialized->getCharacter() == ';') {
            $serialized->consumeCharacter(';');
          }
          $serialized->consumeCharacter(']');
          return sprintf('%s<%s,%s>', $baseType, $typeOne, $typeTwo);
        break;
        case 'V': 
          $baseType = $this->mappings[$token]->handledClass()->getName();
          $serialized->consumeCharacter('[');
          $argType .= $this->typeFor($serialized);
          if ($serialized->getCharacter() == ';') {
            $serialized->consumeCharacter(';');
          }
          $serialized->consumeCharacter(']');
          return sprintf('%s<%s>', $baseType, $argType);
        break;
        case 'O':
          $classString = $serialized->consumeString();
          return $classString;
        break;
        case 's':
          return 'lang.types.String';
        case 'i':
        case 'd':
        case 'b':
          $classString = $this->mappings[$token]->handledClass()->getName();
        return $classString;
        break;
        default:
          Console::writeLine('Error found character: '.$token);
        break;
      }

    }

    /**
     * Fetch best fitted mapper for the given object
     *
     * @param   lang.Object var
     * @return  var FALSE in case no mapper could be found, &remote.protocol.SerializerMapping otherwise
     */
    public function mappingFor($var) {
      if (!($var instanceof Generic)) return FALSE;  // Safeguard

      // Check the mapping-cache for an entry for this object's class
      if (isset($this->_classMapping[$var->getClassName()])) {
        return $this->_classMapping[$var->getClassName()];
      }
      
      // Find most suitable mapping by calculating the distance in the inheritance
      // tree of the object's class to the class being handled by the mapping.
      $cinfo= array();
      foreach (array_keys($this->mappings) as $token) {
        $class= $this->mappings[$token]->handledClass();

        if (!is($class->getName(), $var) && !$var->getClass()->isGeneric()) continue;
        
        $distance= 0; $objectClass= $var->getClass();

        if ($objectClass->isGeneric()) {
          $objectClass= $objectClass->genericDefinition();
        }
        do {
          // Check for direct match
          if ($class->getName() != $objectClass->getName()) $distance++;
        } while (0 < $distance && NULL !== ($objectClass= $objectClass->getParentClass()));

        // Register distance to object's class in cinfo
        $cinfo[$distance]= $this->mappings[$token];

        if (isset($cinfo[0])) break;
      }
      
      // No handlers found...
      if (0 == sizeof($cinfo)) return FALSE;

      ksort($cinfo, SORT_NUMERIC);
      
      // First class is best class
      // Remember this, so we can take shortcut next time
      $this->_classMapping[$var->getClassName()]= $cinfo[key($cinfo)];
      return $this->_classMapping[$var->getClassName()];
    }

    /**
     * Register or retrieve a mapping for a token
     *
     * @param   string token
     * @param   remote.protocol.SerializerMapping mapping
     * @return  remote.protocol.SerializerMapping mapping
     * @throws  lang.IllegalArgumentException if the given argument is not a SerializerMapping
     */
    public function mapping($token, $mapping) {
      if (NULL !== $mapping) {
        if (!$mapping instanceof SerializerMapping) throw new IllegalArgumentException(
          'Given argument is not a SerializerMapping ('.xp::typeOf($mapping).')'
        );

        $this->mappings[$token]= $mapping;
        $this->_classMapping= array();
      }
      
      return $this->mappings[$token];
    }
    
    /**
     * Register or retrieve a mapping for a token
     *
     * @param   string token
     * @param   string exception fully qualified class name
     * @return  string 
     */
    public function exceptionName($name, $exception= NULL) {
      if (NULL !== $exception) $this->exceptions[$name]= $exception;
      return $this->exceptions[$name];
    }
  
    /**
     * Register or retrieve a mapping for a package.
     *
     * This method should only be used to retrieve package
     * mappings. Registering functionality will be removed
     * in future versions.
     *
     * @deprecated
     * @param   string name
     * @param   string replace
     * @return  string replaced
     */
    public function packageMapping($name, $replace= NULL) {
      if (NULL !== $replace) {
        $this->packages[$name]= $replace;     // BC
        $this->packages[0][$name]= $replace;
      }

      return $name == strtr($name, $this->packages)
        ? strtr($name, $this->packages[0])
        : strtr($name, $this->packages)      // BC
      ;
    }
        
    /**
     * Map a remote package name to a local package
     *
     * @param   string remote
     * @param   lang.reflect.Package mapped
     */
    public function mapPackage($remote, Package $mapped) {
      $this->packages[0][$remote]= $mapped->getName();
      $this->packages[1][$mapped->getName()]= $remote;
    }
    
    /**
     * Retrieve serialized representation of a variable
     *
     * @param   remote.protocol.SerializedData serialized
     * @param   array context default array()
     * @return  var
     * @throws  lang.ClassNotFoundException if a class cannot be found
     * @throws  lang.FormatException if an error is encountered in the format 
     */  
    public function valueOf($serialized, $context= array()) {
      static $types= NULL;
      
      if (!$types) $types= array(
        'N'   => 'void',
        'b'   => 'boolean',
        'i'   => 'integer',
        'd'   => 'double',
        's'   => 'string',
        'B'   => new ClassReference('lang.types.Byte'),
        'S'   => new ClassReference('lang.types.Short'),
        'f'   => new ClassReference('lang.types.Float'),
        'l'   => new ClassReference('lang.types.Long'),
        'a'   => 'array',
        'A'   => new ClassReference('lang.types.ArrayList'),
        'T'   => new ClassReference('util.Date')
      );
      $token= $serialized->consumeSize();
      switch ($token) {
        case 'N': {     // null
          $value= NULL;
          return $value;
        }

        case 'b': {     // booleans
          $value= (bool)$serialized->consumeWord();
          return $value;
        }

        case 'i': {     // integers
          $value= (int)$serialized->consumeWord();
          return $value;
        }

        case 'd': {     // decimals
          $value= (float)$serialized->consumeWord();
          return $value;
        }

        case 's': {     // strings
          $value= new String($serialized->consumeString());
          return $value;
        }

        case 'a': {     // arrays
          $a= array();
          $size= $serialized->consumeSize();
          $serialized->consumeCharacter('{');
          for ($i= 0; $i < $size; $i++) {
            $key= $this->valueOf($serialized, $context);
            $a[$key]= $this->valueOf($serialized, $context);
          }
          $serialized->consumeCharacter('}');
          return $a;
        }

        case 'E': {     // generic exceptions
          $instance= new ExceptionReference($serialized->consumeString());
          $size= $serialized->consumeSize();
          $serialized->consumeCharacter('{');
          for ($i= 0; $i < $size; $i++) {
            $member= $this->valueOf($serialized, $context);
            $instance->{$member}= $this->valueOf($serialized, $context);
          }
          $serialized->consumeCharacter('}');
          return $instance;
        }
        
        case 'O': {     // generic objects
          $name= $serialized->consumeString();
          $members= array();
          try {
            $class= XPClass::forName(strtr($name, $this->packages[0]));
          } catch (ClassNotFoundException $e) {
            $instance= new UnknownRemoteObject($name);
            $size= $serialized->consumeSize();
            $serialized->consumeCharacter('{');
            for ($i= 0; $i < $size; $i++) {
              $member= $this->valueOf($serialized, $context);
              $members[$member]= $this->valueOf($serialized, $context);
            }
            $serialized->consumeCharacter('}');
            $instance->__members= $members;
            return $instance;
          }
          
          $size= $serialized->consumeSize();
          $serialized->consumeCharacter('{');

          if ($class->isEnum()) {
            if ($size != 1 || 'name' != $this->valueOf($serialized, $context)) {
              throw new FormatException(sprintf(
                'Local class %s is an enum but remote class is not serialized as one (%s)',
                $name,
                $serialized->toString()
              ));
            }
            $instance= Enum::valueOf($class, $this->valueOf($serialized, $context));
          } else {
            $instance= $class->newInstance();
            for ($i= 0; $i < $size; $i++) {
              $member= $this->valueOf($serialized, $context);
              $instance->{$member}= $this->valueOf($serialized, $context);
            }
          }
          
          $serialized->consumeCharacter('}');
          return $instance;
        }

        case 'c': {     // builtin classes
          $type= $serialized->consumeWord();
          if (!isset($types[$type])) {
            throw new FormatException('Unknown type token "'.$type.'"');
          }
          return $types[$type];
        }
        
        case 'C': {     // generic classes
          $value= new ClassReference(strtr($serialized->consumeString(), $this->packages[0]));
          return $value;
        }

        default: {      // default, check if we have a mapping
          if (!($mapping= $this->mapping($token, $m= NULL))) {
            throw new FormatException(
              'Cannot deserialize unknown type "'.$token.'" ('.$serialized->toString().')'
            );
          }

          return $mapping->valueOf($this, $serialized, $context);
        }
      }
    }
  }
?>
