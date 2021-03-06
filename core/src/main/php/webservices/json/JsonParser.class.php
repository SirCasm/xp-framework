<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  

  uses('text.parser.generic.AbstractParser');

  /**
   * Generated parser class
   *
   * @purpose  Parser implementation
   */
  class JsonParser extends AbstractParser {
    const T_TRUE= 261;
    const T_FALSE= 262;
    const T_NULL= 263;
    const T_INT= 264;
    const T_FLOAT= 265;
    const T_STRING= 270;
    const T_ESCAPE_QUOTATION= 271;
    const T_ESCAPE_REVERSESOLIDUS= 272;
    const T_ESCAPE_SOLIDUS= 273;
    const T_ESCAPE_BACKSPACE= 274;
    const T_ESCAPE_FORMFEED= 275;
    const T_ESCAPE_NEWLINE= 276;
    const T_ESCAPE_CARRIAGERETURN= 277;
    const T_ESCAPE_HORIZONTALTAB= 278;
    const T_ESCAPE_UNICODE= 279;
    const YY_ERRORCODE= 256;

    protected static $yyLhs= array(-1,
          0,     0,     0,     0,     1,     5,     5,     6,     6,     7, 
          2,     2,     8,     8,     3,     3,     9,     9,    10,    10, 
         10,    10,    10,    10,    10,    10,    10,    10,     4,     4, 
          4,     4,     4, 
    );
    protected static $yyLen= array(2,
          1,     1,     1,     1,     1,     3,     2,     1,     3,     3, 
          3,     2,     1,     3,     3,     2,     1,     2,     1,     1, 
          1,     1,     1,     1,     1,     1,     1,     1,     1,     1, 
          1,     1,     1, 
    );
    protected static $yyDefRed= array(0,
         29,    30,    31,    33,    32,     0,     0,     0,     0,     1, 
          2,     3,     4,     5,     7,     0,     0,     8,    12,    13, 
          0,    19,    20,    21,    22,    23,    24,    25,    26,    27, 
         28,    16,     0,    17,     0,     6,     0,     0,    11,    15, 
         18,    10,     9,    14, 
    );
    protected static $yyDgoto= array(9,
         10,    11,    12,    13,    14,    17,    18,    21,    33,    34, 
    );
    protected static $yySindex = array(           -5,
          0,     0,     0,     0,     0,   -32,   -33,   -34,     0,     0, 
          0,     0,     0,     0,     0,   -52,   -41,     0,     0,     0, 
        -40,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,   -24,     0,    -5,     0,   -26,    -5,     0,     0, 
          0,     0,     0,     0, 
    );
    protected static $yyRindex= array(            0,
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0, 
    );
    protected static $yyGindex= array(-2,
          0,     0,     1,     0,     0,     0,   -28,     0,     0,   -22, 
    );
    protected static $yyTable = array(32,
          8,     8,    37,    38,    20,    35,    16,     8,    43,    40, 
         41,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     8,     0, 
          0,     0,    42,     0,     0,    44,     0,    16,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,    39,     0,     0,     0,     0,     7,     0,    19, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,    36,     0,     7,     0,     0,     0,     6, 
          0,     0,    15,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     6,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     1,     2,     3, 
          4,     5,     0,     0,     0,    22,    23,    24,    25,    26, 
         27,    28,    29,    30,    31,    22,    23,    24,    25,    26, 
         27,    28,    29,    30,    31,     1,     2,     3,     4,     5, 
    );
    protected static $yyCheck = array(34,
         34,    34,    44,    44,     7,    58,     6,    34,    37,    34, 
         33,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    34,    -1, 
         -1,    -1,    35,    -1,    -1,    38,    -1,    37,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    93,    -1,    -1,    -1,    -1,    91,    -1,    93, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,   125,    -1,    91,    -1,    -1,    -1,   123, 
         -1,    -1,   125,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,   123,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,   261,   262,   263, 
        264,   265,    -1,    -1,    -1,   270,   271,   272,   273,   274, 
        275,   276,   277,   278,   279,   270,   271,   272,   273,   274, 
        275,   276,   277,   278,   279,   261,   262,   263,   264,   265, 
    );
    protected static $yyFinal= 9;
    protected static $yyName= array(    
      'end-of-file', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "'\"'", NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, "','", NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, "':'", NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "'['", 
      NULL, "']'", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, "'{'", NULL, "'}'", NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, 'T_TRUE', 'T_FALSE', 'T_NULL', 'T_INT', 'T_FLOAT', NULL, 
      NULL, NULL, NULL, 'T_STRING', 'T_ESCAPE_QUOTATION', 
      'T_ESCAPE_REVERSESOLIDUS', 'T_ESCAPE_SOLIDUS', 'T_ESCAPE_BACKSPACE', 
      'T_ESCAPE_FORMFEED', 'T_ESCAPE_NEWLINE', 'T_ESCAPE_CARRIAGERETURN', 
      'T_ESCAPE_HORIZONTALTAB', 'T_ESCAPE_UNICODE', 
    );

    protected static $yyTableCount= 0, $yyNameCount= 0;

    static function __static() {
      self::$yyTableCount= sizeof(self::$yyTable);
      self::$yyNameCount= sizeof(self::$yyName);
    }

    /**
     * Retrieves name of a given token
     *
     * @param   int token
     * @return  string name
     */
    protected function yyname($token) {
      return isset(self::$yyName[$token]) ? self::$yyName[$token] : '<unknown>';
    }

    /**
     * Helper method for yyexpecting
     *
     * @param   int n
     * @return  string[] list of token names.
     */
    protected function yysearchtab($n) {
      if (0 == $n) return array();

      for (
        $result= array(), $token= $n < 0 ? -$n : 0; 
        $token < self::$yyNameCount && $n+ $token < self::$yyTableCount; 
        $token++
      ) {
        if (@self::$yyCheck[$n+ $token] == $token && !isset($result[$token])) {
          $result[$token]= self::$yyName[$token];
        }
      }
      return array_filter(array_values($result));
    }

    /**
     * Computes list of expected tokens on error by tracing the tables.
     *
     * @param   int state for which to compute the list.
     * @return  string[] list of token names.
     */
    protected function yyexpecting($state) {
      return array_merge($this->yysearchtab(self::$yySindex[$state], self::$yyRindex[$state]));
    }

    /**
     * Parser main method. Maintains a state and a value stack, 
     * currently with fixed maximum size.
     *
     * @param   text.parser.generic.AbstractLexer lexer
.    * @return  mixed result of the last reduction, if any.
     */
    public function yyparse($yyLex) {
      $yyVal= NULL;
      $yyStates= $yyVals= array();
      $yyToken= -1;
      $yyState= $yyErrorFlag= 0;

      while (1) {
        for ($yyTop= 0; ; $yyTop++) {
          $yyStates[$yyTop]= $yyState;
          $yyVals[$yyTop]= $yyVal;

          for (;;) {
            if (($yyN= self::$yyDefRed[$yyState]) == 0) {

              // Check whether it's necessary to fetch the next token
              $yyToken < 0 && $yyToken= $yyLex->advance() ? $yyLex->token : 0;

              if (
                ($yyN= self::$yySindex[$yyState]) != 0 && 
                ($yyN+= $yyToken) >= 0 && 
                $yyN < self::$yyTableCount && 
                self::$yyCheck[$yyN] == $yyToken
              ) {
                $yyState= self::$yyTable[$yyN];       // shift to yyN
                $yyVal= $yyLex->value;
                $yyToken= -1;
                $yyErrorFlag > 0 && $yyErrorFlag--;
                continue 2;
              }
        
              if (
                ($yyN= self::$yyRindex[$yyState]) != 0 && 
                ($yyN+= $yyToken) >= 0 && 
                $yyN < self::$yyTableCount && 
                self::$yyCheck[$yyN] == $yyToken
              ) {
                $yyN= self::$yyTable[$yyN];           // reduce (yyN)
              } else {
                switch ($yyErrorFlag) {
                  case 0: return $this->error(
                    E_PARSE, 
                    sprintf(
                      'Syntax error at %s, line %d (offset %d): Unexpected %s',
                      $yyLex->fileName,
                      $yyLex->position[0],
                      $yyLex->position[1],
                      $this->yyName($yyToken)
                    ), 
                    $this->yyExpecting($yyState)
                  );
                  
                  case 1: case 2: {
                    $yyErrorFlag= 3;
                    do { 
                      if (
                        ($yyN= @self::$yySindex[$yyStates[$yyTop]]) != 0 && 
                        ($yyN+= TOKEN_YY_ERRORCODE) >= 0 && 
                        $yyN < self::$yyTableCount && 
                        self::$yyCheck[$yyN] == TOKEN_YY_ERRORCODE
                      ) {
                        $yyState= self::$yyTable[$yyN];
                        $yyVal= $yyLex->value;
                        break 3;
                      }
                    } while ($yyTop-- >= 0);

                    throw new ParseError(E_ERROR, sprintf(
                      'Irrecoverable syntax error at %s, line %d (offset %d)',
                      $yyLex->fileName,
                      $yyLex->position[0],
                      $yyLex->position[1]
                    ));
                  }

                  case 3: {
                    if (0 == $yyToken) {
                      throw new ParseError(E_ERROR, sprintf(
                        'Irrecoverable syntax error at end-of-file at %s, line %d (offset %d)',
                        $yyLex->fileName,
                        $yyLex->position[0],
                        $yyLex->position[1]
                      ));
                    }

                    $yyToken = -1;
                    break 1;
                  }
                }
              }
            }

            $yyV= $yyTop+ 1 - self::$yyLen[$yyN];
            $yyVal= $yyV > $yyTop ? NULL : $yyVals[$yyV];

            // Actions
            switch ($yyN) {

    case 5:  #line 28 "grammar/json.jay"
    {
                  /* Introspect array to check if this is actually an object*/
                  if (!empty($yyVals[0+$yyTop]['__jsonclass__']) && !empty($yyVals[0+$yyTop]['__xpclass__'])) {
                    $yyVal= Type::forName($yyVals[0+$yyTop]['__xpclass__'])->newInstance();
                    
                    foreach ($yyVals[0+$yyTop] as $key => $value) {
                      /* TBD: A member like "constructor" should probably not be serialized*/
                      /* at all. It should be ignored at this point...*/
                      if (in_array($key, array('__jsonclass__', '__xpclass__', 'constructor'))) continue;
                      $yyVal->{$key}= $value;
                    }
                    
                    if (method_exists($yyVal, '__wakeup')) $yyVal->__wakeup();
                  } else {
                    $yyVal= $yyVals[0+$yyTop];
                  }
                } break;

    case 6:  #line 48 "grammar/json.jay"
    { $yyVal= $yyVals[-1+$yyTop]; } break;

    case 7:  #line 49 "grammar/json.jay"
    { $yyVal= array(); } break;

    case 9:  #line 54 "grammar/json.jay"
    { $yyVal= $yyVals[-2+$yyTop] + $yyVals[0+$yyTop]; } break;

    case 10:  #line 58 "grammar/json.jay"
    { $yyVal= array($yyVals[-2+$yyTop] => $yyVals[0+$yyTop]); } break;

    case 11:  #line 62 "grammar/json.jay"
    { $yyVal= $yyVals[-1+$yyTop]; } break;

    case 12:  #line 63 "grammar/json.jay"
    { $yyVal= array(); } break;

    case 13:  #line 67 "grammar/json.jay"
    { $yyVal= array($yyVals[0+$yyTop]); } break;

    case 14:  #line 68 "grammar/json.jay"
    { $yyVal= array_merge($yyVals[-2+$yyTop], array($yyVals[0+$yyTop])); } break;

    case 15:  #line 72 "grammar/json.jay"
    { $yyVal= $yyVals[-1+$yyTop]; } break;

    case 16:  #line 73 "grammar/json.jay"
    { $yyVal= ''; } break;

    case 18:  #line 78 "grammar/json.jay"
    { $yyVal= $yyVals[-1+$yyTop].$yyVals[0+$yyTop]; } break;

    case 19:  #line 82 "grammar/json.jay"
    { $yyVal= iconv('UTF-8', 'ISO-8859-15//IGNORE//TRANSLIT', $yyVals[0+$yyTop]); } break;

    case 20:  #line 83 "grammar/json.jay"
    { $yyVal= '"'; } break;

    case 21:  #line 84 "grammar/json.jay"
    { $yyVal= "\\"; } break;

    case 22:  #line 85 "grammar/json.jay"
    { $yyVal= "/"; } break;

    case 23:  #line 86 "grammar/json.jay"
    { $yyVal= "\b"; } break;

    case 24:  #line 87 "grammar/json.jay"
    { $yyVal= "\f"; } break;

    case 25:  #line 88 "grammar/json.jay"
    { $yyVal= "\n"; } break;

    case 26:  #line 89 "grammar/json.jay"
    { $yyVal= "\r"; } break;

    case 27:  #line 90 "grammar/json.jay"
    { $yyVal= "\t"; } break;

    case 28:  #line 91 "grammar/json.jay"
    {
                                $yyVal= iconv(
                                  'UCS-4BE',
                                  'ISO-8859-15//IGNORE//TRANSLIT',
                                  pack('N', hexdec(substr($yyVals[0+$yyTop], 2)))
                                );
                              } break;

    case 29:  #line 101 "grammar/json.jay"
    { $yyVal= TRUE; } break;

    case 30:  #line 102 "grammar/json.jay"
    { $yyVal= FALSE; } break;

    case 31:  #line 103 "grammar/json.jay"
    { $yyVal= NULL; } break;

    case 32:  #line 104 "grammar/json.jay"
    { $yyVal= doubleval($yyVals[0+$yyTop]); } break;

    case 33:  #line 105 "grammar/json.jay"
    { $yyVal= intval($yyVals[0+$yyTop]); } break;
#line 412 "-"
            }
                   
            $yyTop-= self::$yyLen[$yyN];
            $yyState= $yyStates[$yyTop];
            $yyM= self::$yyLhs[$yyN];

            if (0 == $yyState && 0 == $yyM) {
              $yyState= self::$yyFinal;

              // Check whether it's necessary to fetch the next token
              $yyToken < 0 && $yyToken= $yyLex->advance() ? $yyLex->token : 0;

              // We've reached the final token!
              if (0 == $yyToken) return $yyVal;
              continue 2;
            }

            $yyState= (
              ($yyN= self::$yyGindex[$yyM]) != 0 && 
              ($yyN+= $yyState) >= 0 && 
              $yyN < self::$yyTableCount && 
              self::$yyCheck[$yyN] == $yyState
            ) ? self::$yyTable[$yyN] : self::$yyDgoto[$yyM];
            continue 2;
          }
        }
      }
    }

  }
?>
