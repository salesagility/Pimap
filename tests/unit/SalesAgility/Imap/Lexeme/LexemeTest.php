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

use SalesAgility\Imap\Lexeme\Lexeme;
use SalesAgility\Imap\Lexeme\LexemeType;
use SalesAgility\Imap\Token\Token;
use SalesAgility\Imap\Token\TokenType;
use SalesAgility\Iteration\StringIterator;

class LexemeTest extends \Codeception\Test\Unit
{
    /** @var \ReflectionClass $reflection */
    private static $reflection;
    /**@var \ReflectionProperty $propertyFirst */
    private static $propertyFirst;
    /**@var \ReflectionProperty $propertyCurrent */
    private static $propertyCurrent;
    /**@var \ReflectionProperty $propertyLast */
    private static $propertyLast;
    /**@var \ReflectionProperty $propertyLength */
    private static $propertyLength;
    /**@var \ReflectionProperty $propertyTokenList */
    private static $propertyTokenList;
    /**@var \ReflectionProperty $propertyTypes */
    private static $propertyTypes;

    protected function _before()
    {
        if(self::$reflection === null) {
            self::$reflection = new ReflectionClass(Lexeme::class);
            self::$propertyFirst = self::$reflection->getProperty('first');
            self::$propertyFirst->setAccessible(true);
            self::$propertyCurrent = self::$reflection->getProperty('current');
            self::$propertyCurrent->setAccessible(true);
            self::$propertyLast = self::$reflection->getProperty('last');
            self::$propertyLast->setAccessible(true);
            self::$propertyLength = self::$reflection->getProperty('length');
            self::$propertyLength->setAccessible(true);
            self::$propertyTokenList = self::$reflection->getProperty('tokenList');
            self::$propertyTokenList->setAccessible(true);
            self::$propertyTypes = self::$reflection->getProperty('types');
            self::$propertyTypes->setAccessible(true);
        }
    }


    public function test__construct()
    {
        $object = new Lexeme();
        $this->assertEquals(null, self::$propertyFirst->getValue($object));
        $this->assertEquals(null, self::$propertyCurrent->getValue($object));
        $this->assertEquals(null, self::$propertyLast->getValue($object));
        $this->assertEquals(null, self::$propertyLength->getValue($object));
        $this->assertEquals(array(), self::$propertyTokenList->getValue($object));
        $this->assertEquals(array(), self::$propertyTypes->getValue($object));
    }

    public function testAddType()
    {
        $object = new Lexeme();
        $object->addType(LexemeType::whitespace());
        $expect = array(LexemeType::whitespace());
        $actual = self::$propertyTypes->getValue($object);
        $this->assertEquals($expect, $actual);
    }

    public function testAddToken()
    {
        $object = new Lexeme();
        $token = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token);
        $this->assertEquals('a', $object->offsetGet(0)->toString());
    }

    public function testSeek()
    {
        $object = new Lexeme();
        $token1  = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token1);
        $token2  = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token2);
        $token3  = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token3);

        $object->seek(2);
        $this->assertEquals($token3, $object->current());

        $this->assertFalse($object->seek(3));
    }

    public function testKey()
    {
        $object = new Lexeme();
        $token1  = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token1);
        $token2  = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token2);
        $token3  = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token3);

        $this->assertEquals(0, $object->key());
        $object->next();
        $this->assertEquals(1, $object->key());
        $object->next();
        $this->assertEquals(2, $object->key());
    }

    public function testHasType()
    {
        $object = new Lexeme();
        $object->addType(LexemeType::whitespace());
        $expect = array(LexemeType::whitespace());
        $actual = self::$propertyTypes->getValue($object);
        $this->assertTrue($object->hasType(LexemeType::whitespace()));
        $this->assertFalse($object->hasType(LexemeType::utext()));
    }

    public function testToString()
    {
        $object = new Lexeme();
        $token = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token);
        $this->assertEquals('a', $object->toString());
    }

    public function testFastForward()
    {
        $object = new Lexeme();
        $token1  = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token1);
        $token2  = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token2);
        $token3  = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token3);
        $object->fastForward();
        $this->assertEquals(2, $object->key());
    }

    public function testOffsetGet()
    {
        $object = new Lexeme();
        $token1  = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token1);
        $this->assertSame($token1, $object->offsetGet(0));
    }



    public function testNext()
    {
        $object = new Lexeme();
        $token1  = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token1);
        $token2  = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token2);
        $token3  = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token3);
        $object->rewind();
        $object->next();
        $object->next();
        $this->assertSame($token3, $object->current());
    }

    public function testValid()
    {
        $object = new Lexeme();
        $token1  = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token1);
        $token2  = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token2);
        $token3  = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token3);
        $object->rewind();
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertFalse($object->valid());
    }



    public function testRewind()
    {
        $object = new Lexeme();
        $token1  = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token1);
        $token2  = new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token2);
        $token3  = new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token3);

        $object->next();
        $object->next();
        $object->next();

        $object->rewind();
        $this->assertEquals(0, $object->key());
    }

    public function testCurrent()
    {
        $object = new Lexeme();
        $token1  = new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl());
        $object->addToken($token1);
        $this->assertSame($token1, $object->current());
    }
}
