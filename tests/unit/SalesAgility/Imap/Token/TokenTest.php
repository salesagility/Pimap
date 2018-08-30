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

use SalesAgility\Imap\Token\Token;
use SalesAgility\Imap\Token\TokenType;
use SalesAgility\Iteration\StringIterator;

class TokenTest extends \Codeception\Test\Unit
{
    public function test__construct()
    {
        $object  = new Token(StringIterator::withLiteral(' '), TokenType::whiteSpace());
        $this->assertTrue($object->valid());
    }

    public function testLastKey()
    {
        $object  = new Token(StringIterator::withLiteral('foobarbaz'), TokenType::notWhiteSpaceOrControl());
        $this->assertEquals(8, $object->lastKey());

        $iterator = StringIterator::withStringIterator(StringIterator::withLiteral('foobarbaz'), 1, 3);
        $object  = new Token($iterator, TokenType::notWhiteSpaceOrControl());
        $this->assertEquals(3, $object->lastKey());
    }

    public function testType()
    {
        $object  = new Token(StringIterator::withLiteral('foobarbaz'), TokenType::notWhiteSpaceOrControl());
        $this->assertEquals(TokenType::notWhiteSpaceOrControl(), $object->type());
    }

    public function testFirstKey()
    {
        $object  = new Token(StringIterator::withLiteral('foobarbaz'), TokenType::notWhiteSpaceOrControl());
        $this->assertEquals(0, $object->firstKey());

        $iterator = StringIterator::withStringIterator(StringIterator::withLiteral('foobarbaz'), 1, 3);
        $object  = new Token($iterator, TokenType::notWhiteSpaceOrControl());
        $this->assertEquals(1, $object->firstKey());
    }

    public function testToString()
    {
        $object  = new Token(StringIterator::withLiteral('foobarbaz'), TokenType::notWhiteSpaceOrControl());
        $this->assertEquals('foobarbaz', $object->toString());
    }
}
