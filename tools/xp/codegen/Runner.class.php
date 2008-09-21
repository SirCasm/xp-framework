<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.codegen';
  
  uses(
    'lang.System',
    'util.cmd.Console', 
    'util.cmd.ParamString',
    'util.collections.HashTable',
    'util.collections.Vector',
    'xp.codegen.FileSystemStorage',
    'xp.codegen.ConsoleOutput',
    'xp.codegen.FileSystemOutput'
  );

  /**
   * Code generation
   *
   * @purpose  Tool
   */
  class xp�codegen�Runner extends Object {

    /**
     * Displays usage and exists
     *
     */
    protected static function usage() {
      Console::$err->writeLine('*** Usage: cgen [generator-class] args');
      exit(1);
    }
    
    /**
     * Invoke a target
     *
     * @param   xp.codegen.AbstractGenerator generator
     * @param   lang.reflect.Method method
     * @param   util.collections.HashTable targets
     * @return  mixed result
     */
    protected static function invoke(AbstractGenerator $generator, Method $method, HashTable $targets) {
      $target= $targets->get($method);
      if ($target->containsKey('result')) return $target['result'][0];

      Console::write('---> Target ', $method->getName(), ': ');
      
      // Execute dependencies
      if ($target->containsKey('depends')) {
        foreach ($target->get('depends') as $depends) {
          self::invoke($generator, $depends, $targets); 
        }
      }
      
      // Retrieve arguments
      $arguments= array();
      if ($target->containsKey('arguments')) {
        foreach ($target->get('arguments') as $argument) {
          $arguments[]= self::invoke($generator, $argument, $targets);
        }
      }
      
      // Execute target itself
      $result= $method->invoke($generator, $arguments);
      $target['result']= new ArrayList($result);
      
      Console::writeLine(NULL === $result ? '<ok>' : xp::typeOf($result));
      return $result;
    }
    
    /**
     * Main runner method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      if (!$args) self::usage();
    
      // Load generator class
      try {
        $class= Package::forName('xp.codegen')->getPackage($args[0])->loadClass('Generator');
      } catch (ElementNotFoundException $e) {
        Console::$err->writeLine('*** No generator named "'.$args[0].'"');
        exit(2);
      }

      // Instantiate generator
      $params= new ParamString(array_slice($args, 1));
      $generator= $class->newInstance($params);
      $generator->storage= new FileSystemStorage(System::tempDir());
      
      // Output
      $output= $params->value('output', 'O', '-');
      if ('-' === $output) {
        $generator->output= new ConsoleOutput(Console::$err);
      } else {
        $generator->output= new FileSystemOutput($output);
      }
      $generator->output->addObserver(newinstance('util.Observer', array(), '{
        public function update($obs, $arg= NULL) { Console::writeLine("     >> ", $arg); }
      }'));
      Console::writeLine('===> Starting ', $generator);
      
      // Compile target chain
      $empty= new ArrayList();
      $targets= create('new util.collections.HashTable<lang.reflect.Method, util.collections.HashTable>()');
      foreach ($class->getMethods() as $method) {
        if (!$method->hasAnnotation('target')) continue;
        
        $target= create('new util.collections.HashTable<lang.types.String, lang.Generic>()');

        // Fetch dependencies
        if ($method->hasAnnotation('target', 'depends')) {
          $depends= create('new util.collections.Vector<lang.reflect.Method>()');
          foreach ((array)$method->getAnnotation('target', 'depends') as $dependency) {
            $depends[]= $class->getMethod($dependency);
          }
          $target['depends']= $depends;
        }
        
        // Fetch input
        if ($method->hasAnnotation('target', 'input')) {
          $arguments= create('new util.collections.Vector<lang.reflect.Method>()');
          foreach ((array)$method->getAnnotation('target', 'input') as $input) {
            $arguments[]= $class->getMethod($input);
          }
          $target['arguments']= $arguments;
        }

        $targets->put($method, $target);
      }
      
      // Invoke
      foreach ($targets->keys() as $method) {
        self::invoke($generator, $method, $targets);
      }
      
      Console::writeLine('===> Done');
    }
  }
?>