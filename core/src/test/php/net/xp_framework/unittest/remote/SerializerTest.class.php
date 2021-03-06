<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.remote.Enum',
    'net.xp_framework.unittest.remote.Person',
    'remote.protocol.RemoteInterfaceMapping',
    'remote.protocol.Serializer',
    'unittest.TestCase',
    'util.collections.HashSet',
    'util.collections.HashTable',
    'util.collections.Vector',
    'util.Hashmap'
  );

  /**
   * Unit test for Serializer class
   *
   * @see      xp://remote.Serializer
   * @purpose  TestCase
   */
  class SerializerTest extends TestCase {
    protected $serializer= NULL;

    /**
     * Setup testcase
     *
     */
    public function setUp() {
      $this->serializer= new Serializer();
    }

    /**
     * Test serialization of NULL
     *
     */
    #[@test]
    public function representationOfNull() {
      $this->assertEquals('N:', $this->serializer->representationOf(NULL));
    }

    /**
     * Test serialization of Shorts
     *
     */
    #[@test]
    public function representationOfShorts() {
      $this->assertEquals('S:300;', $this->serializer->representationOf(new Short(300)));
      $this->assertEquals('S:-300;', $this->serializer->representationOf(new Short(-300)));
    }

    /**
     * Test serialization of longs
     *
     */
    #[@test]
    public function representationOfBytes() {
      $this->assertEquals('B:127;', $this->serializer->representationOf(new Byte(127)));
      $this->assertEquals('B:-128;', $this->serializer->representationOf(new Byte(-128)));
    }

    /**
     * Test serialization of booleans
     *
     */
    #[@test]
    public function representationOfBooleans() {
      $this->assertEquals('b:1;', $this->serializer->representationOf($var= TRUE));
      $this->assertEquals('b:0;', $this->serializer->representationOf($var= FALSE));
    }

    /**
     * Test serialization of integers
     *
     */
    #[@test]
    public function representationOfIntegers() {
      $this->assertEquals('i:6100;', $this->serializer->representationOf($var= 6100));
      $this->assertEquals('i:-6100;', $this->serializer->representationOf($var= -6100));
    }

    /**
     * Test serialization of longs
     *
     */
    #[@test]
    public function representationOfLongs() {
      $this->assertEquals('l:6100;', $this->serializer->representationOf(new Long(6100)));
      $this->assertEquals('l:-6100;', $this->serializer->representationOf(new Long(-6100)));
    }

    /**
     * Test serialization of floats
     *
     */
    #[@test]
    public function representationOfFloats() {
      $this->assertEquals('d:0.1;', $this->serializer->representationOf($var= 0.1));
      $this->assertEquals('d:-0.1;', $this->serializer->representationOf($var= -0.1));
    }

    /**
     * Test serialization of doubles
     *
     */
    #[@test]
    public function representationOfDoubles() {
      $this->assertEquals('d:0.1;', $this->serializer->representationOf(new Double(0.1)));
      $this->assertEquals('d:-0.1;', $this->serializer->representationOf(new Double(-0.1)));
    }

    /**
     * Test serialization of the string "Hello World"
     *
     */
    #[@test]
    public function representationOfString() {
      $this->assertEquals('s:11:"Hello World";', $this->serializer->representationOf($var= 'Hello World'));
    }

    /**
     * Test serialization of an array containing three integers
     * (1, 2 and 5)
     *
     */
    #[@test]
    public function representationOfIntegerArray() {
      $this->assertEquals(
        'M:[O:11:"lang.Object";O:11:"lang.Object"]:3:{i:0;i:1;i:1;i:2;i:2;i:5;}',
        $this->serializer->representationOf($var= array(1, 2, 5))
      );
    }

    /**
     * Test serialization of an array containing two strings
     * ("More" and "Power")
     *
     */
    #[@test]
    public function representationOfStringArray() {
      $this->assertEquals(
        'M:[O:11:"lang.Object";O:11:"lang.Object"]:2:{i:0;s:4:"More";i:1;s:5:"Power";}',
        $this->serializer->representationOf($var= array('More', 'Power'))
      );
    }
    
    /**
     * Test Serialization of a generic HashTable
     *
     */
    #[@test]
    public function representationOfGenericHashTable() {
      $hashmap = create('new HashTable<string, string>');
      $hashmap->put('keyone', 'valueone');
      $hashmap->put('keytwo', 'valuetwo');
      $hashmap->put('keythree', 'valuethree');

      $this->assertEquals(
        'M:[s;s]:3:{s:6:"keyone";s:8:"valueone";s:6:"keytwo";s:8:"valuetwo";s:8:"keythree";s:10:"valuethree";}',
        $this->serializer->representationOf($hashmap)
      );

      $hashmap = create('new HashTable<HashTable<string, HashTable<string,string>>, HashTable<integer,string>>');
      $this->assertEquals(
        'M:[M:[s;M:[s;s]];M:[i;s]]:0:{}',
        $this->serializer->representationOf($hashmap)
      );

      $hashmap= create('new HashTable<string, HashTable<string, integer>>');
      $innerHashMap = create('new HashTable<string, integer>');
      $innerHashMap->put('test', 1);
      $innerHashMap->put('test2', 7);
      $innerHashMap->put('test3', 6);
      $innerHashMap->put('test4', 5);
      $innerHashMap->put('test5', 4);
      $innerHashMap->put('test6', 3);
      $hashmap->put('test', $innerHashMap);

      $this->assertEquals(
        'M:[s;M:[s;i]]:1:{s:4:"test";M:[s;i]:6:{s:4:"test";i:1;s:5:"test2";i:7;s:5:"test3";i:6;s:5:"test4";i:5;s:5:"test5";i:4;s:5:"test6";i:3;}}',
        $this->serializer->representationOf($hashmap)
      );
    }

    /**
     * Test Serialization of a generic HashTable
     *
     */
    #[@test]
    public function representationOfNestedGenerics() {
      $hashmap = create('new HashSet<HashTable<Vector<string>,HashSet<int>>>');

      $this->assertEquals(
        'ST:[M:[V:[s];ST:[i]]]:0:{}',
        $this->serializer->representationOf($hashmap)
      );
    }

    /**
     * Test Serialization of a generic HashTable
     *
     */
    #[@test]
    public function representationOfGenericHashSet() {
      $hashset = create('new HashSet<string>');
      $hashset->add('This');
      $hashset->add('test');
      $hashset->add('data');
      $hashset->add('is');
      $hashset->add('hopefully');
      $hashset->add('sufficient');
      $hashset->add('test');
      $hashset->add('data');

      $this->assertEquals(
        'ST:[s]:6:{s:4:"This";s:4:"test";s:4:"data";s:2:"is";s:9:"hopefully";s:10:"sufficient";}',
        $this->serializer->representationOf($hashset)
      );
    }


    /**
     * Test Serialization of a generic HashTable
     *
     */
    #[@test]
    public function representationOfGenericList() {
      $vector = create('new Vector<string>');
      $vector->add('This');
      $vector->add('is');
      $vector->add('hopefully');
      $vector->add('sufficient');
      $vector->add('test');
      $vector->add('data');

      $this->assertEquals(
        'V:[s]:6:{s:4:"This";s:2:"is";s:9:"hopefully";s:10:"sufficient";s:4:"test";s:4:"data";}',
        $this->serializer->representationOf($vector)
      );
    }

    /**
     * Test serialization of a date object
     *
     */
    #[@test]
    public function representationOfDate() {
      $this->assertEquals('T:20050729T153745+0200;', $this->serializer->representationOf(new Date(1122644265)));
    }

    /**
     * Test serialization of a hashmap
     *
     */
    #[@test]
    public function representationOfHashmap() {
      $h= new Hashmap();
      $h->put('key', 'value');
      $h->put('number', '6100');

      $this->assertEquals(
        'M:[O:11:"lang.Object";O:11:"lang.Object"]:2:{s:3:"key";s:5:"value";s:6:"number";s:4:"6100";}',
        $this->serializer->representationOf($h)
      );
    }

    /**
     * Test serialization of a hashmap with mixed values
     *
     */
    #[@test]
    public function representationOfMixedHashmap() {
      $h= new Hashmap();
      $h->put('key', 'value');
      $h->put('number', 6100);

      $this->assertEquals(
        'M:[O:11:"lang.Object";O:11:"lang.Object"]:2:{s:3:"key";s:5:"value";s:6:"number";i:6100;}',
        $this->serializer->representationOf($h)
      );
    }

    /**
     * Test serialization of a generic value object
     *
     * @see     xp://Person
     */
    #[@test]
    public function representationOfValueObject() {
      $this->assertEquals(
        'O:39:"net.xp_framework.unittest.remote.Person":2:{2:id;i:1549;4:name;s:11:"Timm Friebe";}',
        $this->serializer->representationOf(new net�xp_framework�unittest�remote�Person())
      );
    }

    /**
     * Test serialization of a generic value object
     *
     * @see     xp://Person
     */
    #[@test]
    public function representationOfGenericException() {
      $this->assertEquals(
        'E:16:"lang.XPException":3:{7:message;s:17:"Generic Exception";5:trace;V:[t]:9:{t:4:{4:file;N:5:class;s:14:"SerializerTest";6:method;s:32:"representationOfGenericException";4:line;i:0;}t:4:{4:file;s:16:"Method.class.php";5:class;s:20:"php.ReflectionMethod";6:method;s:10:"invokeArgs";4:line;i:102;}t:4:{4:file;s:19:"TestSuite.class.php";5:class;s:19:"lang.reflect.Method";6:method;s:6:"invoke";4:line;i:266;}t:4:{4:file;s:19:"TestSuite.class.php";5:class;s:18:"unittest.TestSuite";6:method;s:11:"runInternal";4:line;i:473;}t:4:{4:file;s:16:"Runner.class.php";5:class;s:18:"unittest.TestSuite";6:method;s:3:"run";4:line;i:236;}t:4:{4:file;s:16:"Runner.class.php";5:class;s:18:"xp.unittest.Runner";6:method;s:3:"run";4:line;i:247;}t:4:{4:file;N:5:class;s:18:"xp.unittest.Runner";6:method;s:4:"main";4:line;i:0;}t:4:{4:file;s:16:"Method.class.php";5:class;s:20:"php.ReflectionMethod";6:method;s:10:"invokeArgs";4:line;i:102;}t:4:{4:file;s:9:"class.php";5:class;s:19:"lang.reflect.Method";6:method;s:6:"invoke";4:line;i:90;}}5:cause;N:}',
        $this->serializer->representationOf(new XPException('Generic Exception'))
      );
    }

    /**
     * Test serialization of a mapped value object
     *
     * @see     xp://Person
     */
    #[@test]
    public function representationOfMappedValueObject() {
      $this->serializer->mapPackage('remote', Package::forName('net.xp_framework.unittest.remote'));
      $this->assertEquals(
        'O:13:"remote.Person":2:{2:id;i:1549;4:name;s:11:"Timm Friebe";}',
        $this->serializer->representationOf(new net�xp_framework�unittest�remote�Person())
      );
    }

    /**
     * Test serialization of a enum object
     *
     */
    #[@test]
    public function representationOfEnum() {
      $this->assertEquals(
        'O:37:"net.xp_framework.unittest.remote.Enum":1:{4:name;s:6:"Value1";}',
        $this->serializer->representationOf(net�xp_framework�unittest�remote�Enum::$Value1)
      );
    }

    /**
     * Test serialization of a Bytes object
     *
     */
    #[@test]
    public function representationOfByes() {
      $this->assertEquals(
        "Y:4:\"\0abc\";",
        $this->serializer->representationOf(new Bytes(array(0, 'a', 'b', 'c')))
      );
    }

    /**
     * Test deserialization of an integer
     *
     */
    #[@test]
    public function valueOfInt() {
      $this->assertEquals(
        new Integer(1),
        $this->serializer->valueOf(new SerializedData('i:1;'))
      );
    }

    /**
     * Test deserialization of a byte
     *
     */
    #[@test]
    public function valueOfByte() {
      $this->assertEquals(
        new Byte(1),
        $this->serializer->valueOf(new SerializedData('B:1;'))
      );
    }

    /**
     * Test deserialization of a long
     *
     */
    #[@test]
    public function valueOfLong() {
      $this->assertEquals(
        new Long(12345),
        $this->serializer->valueOf(new SerializedData('l:12345;'))
      );
    }

    /**
     * Test deserialization of a float
     *
     */
    #[@test]
    public function valueOfFloat() {
      $this->assertEquals(
        new Float(1.5),
        $this->serializer->valueOf(new SerializedData('f:1.5;'))
      );
    }

    /**
     * Test deserialization of a double
     *
     */
    #[@test]
    public function valueOfDouble() {
      $this->assertEquals(
        new Double(1.5),
        $this->serializer->valueOf(new SerializedData('d:1.5;'))
      );
    }

    /**
     * Test deserialization of an integer
     *
     * @see     xp://Person
     */
    #[@test]
    public function valueOfShorts() {
      $this->assertEquals(
        new Short(1),
        $this->serializer->valueOf(new SerializedData('S:1;'))
      );
    }

    /**
     * Test deserialization of a date
     *
     */
    #[@test]
    public function valueOfDates() {
      $this->assertEquals(
        new Date(328312800),
        $this->serializer->valueOf(new SerializedData('T:19800528T000000+0200;'))
      );
    }

    /**
     * Test deserialization of enum
     *
     */
    #[@test]
    public function valueOfEnum() {
      $obj= $this->serializer->valueOf(new SerializedData('O:37:"net.xp_framework.unittest.remote.Enum":1:{s:4:"name";s:6:"Value1";};'));
      $this->assertEquals(net�xp_framework�unittest�remote�Enum::$Value1, $obj);
      $this->assertEquals(net�xp_framework�unittest�remote�Enum::$Value1->ordinal(), $obj->ordinal());
      $this->assertEquals(net�xp_framework�unittest�remote�Enum::$Value1->name(), $obj->name());
    }

    /**
     * Test deserialization of a class that does not exist will yield an UnknownRemoteObject
     *
     * @see   xp://net.xp_framework.unittest.remote.UnknownRemoteObjectTest
     */
    #[@test]
    public function valueOfUnknownObject() {
      $obj= $this->serializer->valueOf(new SerializedData('O:40:"net.xp_framework.unittest.remote.Unknown":2:{2:id;i:1549;4:name;s:11:"Timm Friebe";};'));
      $this->assertClass($obj, 'remote.UnknownRemoteObject');
      $this->assertEquals('net.xp_framework.unittest.remote.Unknown', $obj->__name);
      $this->assertEquals(new Integer(1549), $obj->__members['id']);
      $this->assertEquals(new String('Timm Friebe'), $obj->__members['name']);
    }

    /**
     * Test deserialization of an integer
     *
     */
    #[@test]
    public function valueOfException() {
      $exception= $this->serializer->valueOf(new SerializedData(
        'E:46:"java.lang.reflect.UndeclaredThrowableException":3:{'.
        '7:message;s:12:"*** BLAM ***";'.
        '5:trace;V:[t]:1:{t:4:{4:file;s:9:"Test.java";5:class;s:4:"Test";6:method;s:4:"main";4:line;i:10;}}'.
        '5:cause;N:'.
        '}'
      ));
      $this->assertClass($exception, 'remote.ExceptionReference');
      $this->assertEquals('java.lang.reflect.UndeclaredThrowableException', $exception->referencedClassname);
      $this->assertEquals('*** BLAM ***', $exception->getMessage());
      with ($trace= $exception->getStackTrace()); {
        $this->assertEquals(1, sizeof($trace));
        $this->assertClass($trace[0], 'remote.RemoteStackTraceElement');
        $this->assertEquals('Test.java', $trace[0]->file);
        $this->assertEquals('Test', $trace[0]->class);
        $this->assertEquals('main', $trace[0]->method);
        $this->assertEquals(10, $trace[0]->line);
      }
      $this->assertNull($exception->getCause());
    }

    /**
     * Test serialization of a Bytes object
     *
     */
    #[@test]
    public function valueOfBytes() {
      $this->assertEquals(
        new Bytes(array(0, 'a', 'b', 'c')),
        $this->serializer->valueOf(new SerializedData("Y:4:\"\0abc\";"))
      );
    }

    /**
     * Test serialization of a HashTable object
     *
     */
    #[@test]
    public function valueOfGenericHashtable() {

       $return = $this->serializer->valueOf(new SerializedData('M:[O:17:"lang.types.String";O:17:"lang.types.Double"]:0:{}'));

       $hashmap = create('new HashTable<lang.types.String, lang.types.Double>');
       $this->assertEquals($return, $hashmap);
    }

    /**
     * Test deserialization of a classreference
     *
     */
    #[@test]
    public function genericClass() {
      $class= $this->serializer->valueOf(new SerializedData('C:47:"net.xp_framework.easc.reflect.MethodDescription"'));
      $this->assertTrue(is('remote.ClassReference', $class));
      $this->assertEquals("net.xp_framework.easc.reflect.MethodDescription", $class->referencedName());
    }

    /**
     * Test deserialization of a package-mapped classreference
     *
     */
    #[@test]
    public function genericPackageMappedClass() {
      $this->serializer->mapPackage('net.xp_framework.easc.reflect', Package::forName('remote.reflect'));

      $class= $this->serializer->valueOf(new SerializedData('C:47:"net.xp_framework.easc.reflect.MethodDescription"'));
      $this->assertTrue(is('remote.ClassReference', $class));
      $this->assertEquals("remote.reflect.MethodDescription", $class->referencedName());
    }

    /**
     * Test deserialization of a package-mapped classreference
     *
     * @deprecated
     */
    #[@test]
    public function oneWayPackageMappedClass() {
      $this->serializer->packageMapping('net.xp_framework.easc.reflect', 'remote.reflect');

      $class= $this->serializer->valueOf(new SerializedData('C:47:"net.xp_framework.easc.reflect.MethodDescription"'));
      $this->assertTrue(is('remote.ClassReference', $class));
      $this->assertEquals("remote.reflect.MethodDescription", $class->referencedName());
    }

    /**
     * Test deserialization of a package-mapped classreference
     *
     * @deprecated
     */
    #[@test]
    public function oneWayPackageMappedInterface() {
      $this->serializer->packageMapping('net.xp_framework.easc.beans', 'remote.beans');
      $this->serializer->mapping('I', new RemoteInterfaceMapping());

      $class= $this->serializer->valueOf(
        new SerializedData('I:12036987:{s:41:"net.xp_framework.easc.beans.BeanInterface";}'),
        array('handler' => 'remote.protocol.XPProtocolHandler')
      );

      $this->assertSubclass($class, 'lang.reflect.Proxy');
      $this->assertSubclass($class, 'remote.beans.BeanInterface');
    }

    /**
     * Test deserialization of a package-mapped classreference
     *
     */
    #[@test]
    public function remoteInterfaceMapping() {
      $this->serializer->mapPackage('net.xp_framework.easc.beans', Package::forName('remote.beans'));
      $this->serializer->mapping('I', new RemoteInterfaceMapping());

      $class= $this->serializer->valueOf(
        new SerializedData('I:12036987:{s:41:"net.xp_framework.easc.beans.BeanInterface";}'),
        array('handler' => 'remote.protocol.XPProtocolHandler')
      );

      $this->assertSubclass($class, 'lang.reflect.Proxy');
      $this->assertSubclass($class, 'remote.beans.BeanInterface');
    }

    /**
     * Check serialization through custom class mappings. Check that the serialization
     * is always carried through by the best matching serializer mapping.
     *
     */
    #[@test]
    public function bestMapping() {
      $fooClass= ClassLoader::defineClass('net.xp_framework.unittest.remote.FooClass', 'lang.Object', NULL);
      $barClass= ClassLoader::defineClass('net.xp_framework.unittest.remote.BarClass', 'FooClass', NULL);
      $bazClass= ClassLoader::defineClass('net.xp_framework.unittest.remote.BazClass', 'BarClass', NULL);
      $bazookaClass= ClassLoader::defineClass('net.xp_framework.unittest.remote.BazookaClass', 'BazClass', NULL);

      // Both must be serialized with the FOO mapping, because both are Foo or Foo-derived objects.
      $this->serializer->mapping('FOO', newinstance('remote.protocol.SerializerMapping', array(), '{
        function handledClass() { return XPClass::forName("net.xp_framework.unittest.remote.FooClass"); }
        function representationOf($serializer, $value, $context= array()) { return "FOO:"; }
        public function valueOf($serializer, $serialized, $context= array()) { return NULL; }
      }'));
      $this->assertEquals('FOO:', $this->serializer->representationOf(new FooClass()));
      $this->assertEquals('FOO:', $this->serializer->representationOf(new BarClass()));
      $this->assertEquals('FOO:', $this->serializer->representationOf(new BazClass()));

      // Add more concrete mapping for BAR. Foo must still be serialized with FOO, but the BarClass-object
      // has a better matching mapping.
      $this->serializer->mapping('BAR', newinstance('remote.protocol.SerializerMapping', array(), '{
        function handledClass() { return XPClass::forName("net.xp_framework.unittest.remote.BarClass"); }
        function representationOf($serializer, $value, $context= array()) { return "BAR:"; }
        function valueOf($serializer, $serialized, $context= array()) { return NULL; }
      }'));
      $this->assertEquals('FOO:', $this->serializer->representationOf(new FooClass()));
      $this->assertEquals('BAR:', $this->serializer->representationOf(new BarClass()));
      $this->assertEquals('BAR:', $this->serializer->representationOf(new BazClass()));
      $this->assertEquals('BAR:', $this->serializer->representationOf(new BazookaClass()));
    }
  }
?>
