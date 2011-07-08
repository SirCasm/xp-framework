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
      $offset= 0,
      $length;

    /**
     * Constructor
     * 
     * @param   string buffer
     */
    public function __construct($buffer) {
      $this->buffer= $buffer;
      $this->offset= 0;
      $this->length= strlen($buffer);
    }

    /**
     * Consume a identifier ([length]:[string])
     * 
     * @return  string
     */
    public function consumeIdentifier() {
      $l= substr(
        $this->buffer, 
        $this->offset, 
        strpos($this->buffer, ':', $this->offset)- $this->offset
      );
      $b= strlen($l)+ 1;              // 1 for ':'
      $v= substr($this->buffer, $this->offset + $b, $l);
      $this->offset+= $b + $l + 1;    // 1 for '"'
      return $v;
    }
    
    /**
     * Consume a string ([length]:"[string]";)
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

    /**
     * Consume a string ([length]:"[string]")
     * 
     * @return  string
     */
    public function consumeTypeString() {
      $l= substr(
        $this->buffer, 
        $this->offset, 
        strpos($this->buffer, ':', $this->offset)- $this->offset
      );
      $b= strlen($l)+ 2;              // 1 for ':', 1 for '"'
      $v= substr($this->buffer, $this->offset + $b, $l);
      $this->offset+= $b + $l + 1;    // 1 for '"', +1 to set the marker behind
      return $v;
    }

    public function getPositionOfNextToken() {
      $found = FALSE;
      $pos = 1;
      while ($found == FALSE && $this->length > ($this->offset + $pos)) {
        switch ($this->buffer{$this->offset+$pos}) {
          case ':':
          case ';':
          case ']':
            $found = TRUE;
          break;
          default:
            $pos++;
          break;
        }
      }
      
      return $found == TRUE ? $pos : FALSE;
    }

    /**
     *
     *
     *
     *
     */
    public function consumeNextToken() {
      $i = $this->getPositionOfNextToken();

      $t = substr($this->buffer, $this->offset, $i);
      $this->offset += $i;

      return $i === FALSE ? FALSE : $t;
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

      throw new IllegalStateException(sprintf('Expected "%s" character, found "%s" instead. Offset %d. Object: %s', $chr, $this->buffer{$this->offset}, $this->offset, $this->toString()));
      
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
