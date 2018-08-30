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

use SalesAgility\Imap\Lexeme\LexemeList;
use SalesAgility\Imap\Lexeme\Lexeme;
use SalesAgility\Imap\Lexeme\LexemeType;
use SalesAgility\Imap\Token\Token;
use SalesAgility\Imap\Token\TokenType;
use SalesAgility\Iteration\StringIterator;

class LexemeListTest extends \Codeception\Test\Unit
{
    /** @var UnitTester $tester */
    protected $tester;

    public function testKey()
    {
        $object = new LexemeList();

        $l1 = new Lexeme();
        $l1->addToken(new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l1;

        $l2 = new Lexeme();
        $l2->addToken(new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l2;

        $l3 = new Lexeme();
        $l3->addToken(new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l3;

        $object->next();
        $object->next();
        $this->assertEquals(2, $object->key());
    }

    public function testCurrent()
    {
        $object = new LexemeList();

        $l1 = new Lexeme();
        $l1->addToken(new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l1;

        $l2 = new Lexeme();
        $l2->addToken(new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l2;

        $l3 = new Lexeme();
        $l3->addToken(new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l3;

        $this->assertEquals('a', $object->current()->offsetGet(0)->toString());
        $object->next();
        $this->assertEquals('b', $object->current()->offsetGet(0)->toString());
        $object->next();
        $this->assertEquals('c', $object->current()->offsetGet(0)->toString());
    }

    public function testRewind()
    {
        $object = new LexemeList();

        $l1 = new Lexeme();
        $l1->addToken(new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l1;

        $l2 = new Lexeme();
        $l2->addToken(new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l2;

        $l3 = new Lexeme();
        $l3->addToken(new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l3;

        $object->next();
        $object->next();
        $object->rewind();
        $this->assertEquals('a', $object->current()->offsetGet(0)->toString());
    }

    public function testOffsetUnset()
    {
        $object = new LexemeList();

        $l1 = new Lexeme();
        $l1->addToken(new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l1;

        $l2 = new Lexeme();
        $l2->addToken(new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l2;

        $l3 = new Lexeme();
        $l3->addToken(new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l3;

        $object->offsetUnset(2);
        $this->assertEquals('b', $object->offsetGet(1)->offsetGet(0)->toString());
    }

    public function testValid()
    {
        $object = new LexemeList();

        $l1 = new Lexeme();
        $l1->addToken(new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l1;

        $l2 = new Lexeme();
        $l2->addToken(new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l2;

        $l3 = new Lexeme();
        $l3->addToken(new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l3;

        $this->assertTrue($object->valid());
        $object->next();
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertFalse($object->valid());
    }

    public function testOffsetExists()
    {
        $object = new LexemeList();

        $l1 = new Lexeme();
        $l1->addToken(new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l1;

        $l2 = new Lexeme();
        $l2->addToken(new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l2;

        $l3 = new Lexeme();
        $l3->addToken(new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l3;

        $this->assertTrue($object->offsetExists(0));
        $this->assertTrue($object->offsetExists(1));
        $this->assertTrue($object->offsetExists(2));
        $this->assertFalse($object->offsetExists(3));
    }

    public function testOffsetGet()
    {
        $object = new LexemeList();

        $l1 = new Lexeme();
        $l1->addToken(new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l1;

        $l2 = new Lexeme();
        $l2->addToken(new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l2;

        $l3 = new Lexeme();
        $l3->addToken(new Token(StringIterator::withLiteral('c'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l3;

        $this->assertEquals('b', $object->offsetGet(1)->offsetGet(0)->toString());
    }

    public function testOffsetSet()
    {
        $object = new LexemeList();

        $l1 = new Lexeme();
        $l1->addToken(new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l1;

        $l2 = new Lexeme();
        $l2->addToken(new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl()));
        $object[0] = $l2;

        $this->assertEquals('b', $object->offsetGet(0)->offsetGet(0)->toString());


        $this->tester->expectException(
            new \InvalidArgumentException('Lexeme List can only store integer key values'),
            function () {
                $object = new LexemeList();
                $l2 = new Lexeme();
                $l2->addToken(new Token(StringIterator::withLiteral('b'), TokenType::notWhiteSpaceOrControl()));
                $object['string'] = $l2;
            }
        );

        $this->tester->expectException(
            new \InvalidArgumentException('Lexeme List can only store values which derive from a Lexeme'),
            function () {
                $object = new LexemeList();
                $object[] = 'hello lexeme';
            }
        );
    }

    public function testNext()
    {
        $object = new LexemeList();

        $l1 = new Lexeme();
        $l1->addToken(new Token(StringIterator::withLiteral('a'), TokenType::notWhiteSpaceOrControl()));
        $object[] = $l1;
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertFalse($object->valid());
    }
}
