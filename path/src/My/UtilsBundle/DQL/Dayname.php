<?php

namespace My\UtilsBundle\DQL; 

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * Date  ::= "DAYNAME" "(" ArithmeticPrimary ")"
 */

class Dayname extends FunctionNode {

    public $date = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser){

         $parser->match(Lexer::T_IDENTIFIER);
         $parser->match(Lexer::T_OPEN_PARENTHESIS);
         $this->date = $parser->ArithmeticPrimary();
         $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker){

        return "DAYNAME(" . $sqlWalker->walkArithmeticPrimary($this->date) . ")";
    }
}