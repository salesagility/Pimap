<?php
/*********************************************************************************
 * Pimap is a PHP IMAP library developed by SalesAgility Ltd.
 * Copyright (C) 2018 SalesAgility Ltd.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *********************************************************************************/

use SalesAgility\Imap\Interpreter\LexemeInterpreter;

class LexemeInterpreterTest extends \Codeception\Test\Unit
{
    public function testLastInGroup()
    {
        // test cases which are not covered by other tests
        $response = \SalesAgility\Iteration\StringIterator::withLiteral('("test" ("test))');
        $tokenizer = new \SalesAgility\Imap\Token\Tokenizer();
        $leximizer = new \SalesAgility\Imap\Lexeme\Lexemizer();
        $interpreter = new LexemeInterpreter();
        $tokens = $tokenizer->parse($response);
        $lexemes = $leximizer->parse($tokens);
        $parsed = $interpreter->lastInGroup($lexemes);
        $this->assertEquals(5, $parsed);
    }
}
