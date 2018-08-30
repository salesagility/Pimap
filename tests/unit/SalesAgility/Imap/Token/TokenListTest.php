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

use SalesAgility\Imap\Token\TokenList;
use \SalesAgility\Imap\Token\Token;
use SalesAgility\Imap\Token\TokenType;
use SalesAgility\Iteration\StringIterator;

class TokenListTest extends \Codeception\Test\Unit
{

    /** @var UnitTester  $tester*/
    protected $tester;

    public function testOffsetSet()
    {
        $object = new TokenList();
        $object[] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $this->assertEquals(0, $object->key());


        $object = new TokenList();
        $object[0] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $this->assertEquals(0, $object->key());

        // negative cases
        $this->tester->expectException(
            new \Exception('Token List can only store integer key values'),
            function () {
                $object = new TokenList();
                $object['string'] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
                $this->assertEquals(0, $object->key());
            }
        );


        $this->tester->expectException(
            new \Exception('Token List can only store values which derive from a Token'),
            function () {
                $object = new TokenList();
                $object[0] = 'string';
                $this->assertEquals(0, $object->key());
            }
        );

    }

    public function testValid()
    {
        $object = new TokenList();
        $object[] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $this->assertEquals(0, $object->key());
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertFalse($object->valid());
    }

    public function testKey()
    {
        $object = new TokenList();
        $object[] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->next();
        $object->next();
        $this->assertEquals(2, $object->key());
    }

    public function testOffsetGet()
    {
        $object = new TokenList();
        $object[] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->next();
        $object->next();
        $this->assertEquals('b', $object->offsetGet(1)->toString());
    }

    public function testOffsetUnset()
    {
        $object = new TokenList();
        $object[] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        unset($object[1]);
        $object->offsetGet(1);
        $this->assertEquals('c', $object->offsetGet(1)->toString());
    }

    public function testCurrent()
    {
        $object = new TokenList();
        $object[] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $this->assertEquals('a', $object->current()->toString());
        $object->next();
        $this->assertEquals('b', $object->current()->toString());
        $object->next();
        $this->assertEquals('c', $object->current()->toString());
    }

    public function testNext()
    {
        $object = new TokenList();
        $object[] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->next();
        $this->assertEquals(1, $object->key());
    }

    public function testOffsetExists()
    {
        $object = new TokenList();
        $object[] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $this->assertTrue($object->offsetExists(0));
        $this->assertFalse($object->offsetExists(1));
    }

    public function testSeek()
    {
        $object = new TokenList();
        $object[] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->seek(2);
        $this->assertEquals(2, $object->key());
    }


    public function testRewind()
    {
        $object = new TokenList();
        $object[] = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object[] = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->next();
        $object->next();
        $object->next();
        $object->rewind();
        $this->assertEquals(0, $object->key());
    }
}
