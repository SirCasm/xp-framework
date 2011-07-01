<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Class that represents a chunk of serialized data
   *
   * @test     xp://remote.protocol.Serializer
   * @purpose  Value object
   */
  class SerializedData extends Object {
    public
      $buffer= '',
      $offset= 0;

    /**
     * Constructor
     * 
     * @param   string buffer
     */
    public function __construct($buffer) {
      $this->buffer= $buffer;
      $this->offset= 0;
    }
    
    /**
     * Consume a string ([length]:"[string]")
     * 
     * @return  string
     */
    public function consumeString() {
      $l= substr(
        $this->buffer, 
        $this->offset, 
        strpos($this->buffer, ':', $this->offset)- $this->offset
      );
      $b= strlen($l)+ 2;              // 1 for ':', 1 for '"'
      $v= substr($this->buffer, $this->offset + $b, $l);
      $this->offset+= $b + $l + 2;    // 1 for '"', +1 to set the marker behind
      return $v;
    }

    public function consumeNextToken() {
      $colonpos = strpos($this->buffer, ':', $this->offset);
      $semipos  = strpos($this->buffer, ';', $this->offset);
      $colonpos = $colonpos === FALSE ? $semipos+1 : $colonpos;
      $v= substr(
        $this->buffer, 
        $this->offset, 
        ($colonpos < $semipos ? $colonpos : $semipos) - $this->offset
      );
     
      $this->offset+= strlen($v)+ 1;  // +1 to set the marker behind
      return $v;

    }

    /**
     * Consume everything up to the next ";" and return it
     * 
     * @param   string stop
     * @return  string
     */     
    public function consumeWord() {
      $pos = strpos($this->buffer, ';', $this->offset);
      $v= substr(
        $this->buffer, 
        $this->offset, 
        $pos - $this->offset
      );
      $this->offset+= strlen($v)+ 1;  // +1 to set the marker behind
      return $v;
    }

    public function consumeCharacter($chr) {
      if ($this->buffer{$this->offset} == $chr) {
        $this->offset++;
        return;
      }

      throw new IllegalStateException(sprintf('Expected "%s" character, found "%s" instead', $chr, $this->buffer{$this->offset}));
      
    }

    /**
     * Consume everything up to the next "[" and return it
     * 
     * @param   string stop
     * @return  string
     */     
    public function consumeType() {
       $v= substr(
        $this->buffer, 
        $this->offset, 
        strpos($this->buffer, '[', $this->offset)- $this->offset
      ); 
     
      $this->offset+= strlen($v)+ 1;  // +1 to set the marker behind
      return $v;
    }

    public function consumeTypeEnd() {
       $v= substr(
        $this->buffer, 
        $this->offset, 
        strpos($this->buffer, ']', $this->offset)- $this->offset
      ); 
     
      $this->offset+= strlen($v)+ 1;  // +1 to set the marker behind
      return $v;
    }

    public function getCharacter($i = 0) {
      return $this->buffer{$this->offset + $i};
    }

    /**
     * Consume everything up to the next ":" character and return it
     * 
     * @param   string stop
     * @return  string
     */     
    public function consumeSize() {
      $v= substr(
        $this->buffer, 
        $this->offset, 
        strpos($this->buffer, ':', $this->offset)- $this->offset
      ); 
      $this->offset+= strlen($v)+ 1;  // +1 to set the marker behind
      return $v;
    }
  }
?>
