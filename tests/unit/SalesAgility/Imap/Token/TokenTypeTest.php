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

use SalesAgility\Imap\Token\TokenType;

class TokenTypeTest extends \Codeception\Test\Unit
{
    /** @var UnitTester  $tester*/
    protected $tester;

    public function testIsControlCharacter()
    {
        $object = TokenType::controlCharacter();
        $this->assertTrue($object->isControlCharacter());

        $object = TokenType::whiteSpace();
        $this->assertFalse($object->isControlCharacter());
    }

    public function testIsPaired()
    {
        $object = TokenType::paired();
        $this->assertTrue($object->isPaired());

        $object = TokenType::whiteSpace();
        $this->assertFalse($object->isPaired());
    }

    public function testFoldingWhiteSpace()
    {
        $object = TokenType::foldingWhiteSpace();
        $this->assertTrue($object->isFoldingWhiteSpace());
    }

    public function testIsNonFoldedLiteral()
    {
        $object = TokenType::nonFoldedLiteral();
        $this->assertTrue($object->isNonFoldedLiteral());
    }

    public function testWhiteSpace()
    {
        $object = TokenType::whiteSpace();
        $this->assertTrue($object->isWhiteSpace());
    }

    public function testDot()
    {
        $object = TokenType::dot();
        $this->assertTrue($object->isDot());
    }

    public function testIsAngledAddress()
    {
        $object = TokenType::angledAddress();
        $this->assertTrue($object->isAngledAddress());

        $object = TokenType::nonFoldedLiteral();
        $this->assertFalse($object->isAngledAddress());
    }

    public function testNotWhiteSpaceOrControl()
    {
        $object = TokenType::notWhiteSpaceOrControl();
        $this->assertTrue($object->isNotWhiteSpaceOrControl());
    }

    public function testIsRequiredLineLength()
    {
        $object = TokenType::requiredLineLength();
        $this->assertTrue($object->isRequiredLineLength());
    }

    public function testIsAtSign()
    {
        $object = TokenType::atSign();
        $this->assertTrue($object->isAtSign());

        $object = TokenType::nonFoldedLiteral();
        $this->assertFalse($object->isAtSign());
    }

    public function testGroup()
    {
        $object = TokenType::group();
        $this->assertTrue($object->isGroup());
    }

    public function testEndOfLine()
    {
        $object = TokenType::endOfLine();
        $this->assertTrue($object->isEndOfLine());
    }

    public function testRecommendedLineLength()
    {
        $object = TokenType::recommendedLineLength();
        $this->assertTrue($object->isRecommendedLineLength());
    }

    public function testIsRecommendedLineLength()
    {
        $object = TokenType::recommendedLineLength();
        $this->assertTrue($object->isRecommendedLineLength());

        $object = TokenType::nonFoldedLiteral();
        $this->assertFalse($object->isRecommendedLineLength());
    }

    public function testIsDot()
    {
        $object = TokenType::dot();
        $this->assertTrue($object->isDot());
    }

    public function testIsSpecial()
    {
        $object = TokenType::special();
        $this->assertTrue($object->isSpecial());
    }

    public function testRequiredLineLength()
    {
        $object = TokenType::requiredLineLength();
        $this->assertTrue($object->isRequiredLineLength());

    }

    public function testNonFoldedLiteral()
    {
        $object = TokenType::nonFoldedLiteral();
        $this->assertTrue($object->isNonFoldedLiteral());
    }

    public function testQuoted()
    {
        $object = TokenType::quoted();
        $this->assertTrue($object->isQuoted());
    }

    public function testControlCharacter()
    {
        $object = TokenType::controlCharacter();
        $this->assertTrue($object->isControlCharacter());
    }

    public function testAtSign()
    {
        $object = TokenType::atSign();
        $this->assertTrue($object->isAtSign());
    }

    public function testIsEndOfLine()
    {
        $object = TokenType::endOfLine();
        $this->assertTrue($object->isEndOfLine());

        $object = TokenType::nonFoldedLiteral();
        $this->assertFalse($object->isEndOfLine());
    }

    public function testIsWhiteSpace()
    {
        $object = TokenType::whiteSpace();
        $this->assertTrue($object->isWhiteSpace());

        $object = TokenType::nonFoldedLiteral();
        $this->assertFalse($object->isWhiteSpace());
    }

    public function testIsFoldingWhiteSpace()
    {
        $object = TokenType::foldingWhiteSpace();
        $this->assertTrue($object->isFoldingWhiteSpace());

        $object = TokenType::nonFoldedLiteral();
        $this->assertFalse($object->isFoldingWhiteSpace());
    }

    public function testSpecial()
    {
        $object = TokenType::special();
        $this->assertTrue($object->isSpecial());
    }

    public function testListSeparator()
    {
        $object = TokenType::listSeparator();
        $this->assertTrue($object->isListSeparator());
    }

    public function testIsQuoted()
    {
        $object = TokenType::quoted();
        $this->assertTrue($object->isQuoted());

        $object = TokenType::nonFoldedLiteral();
        $this->assertFalse($object->isQuoted());
    }

    public function testIsNotWhiteSpaceOrControl()
    {
        $object = TokenType::notWhiteSpaceOrControl();
        $this->assertTrue($object->isNotWhiteSpaceOrControl());

        $object = TokenType::nonFoldedLiteral();
        $this->assertFalse($object->isNotWhiteSpaceOrControl());
    }

    public function testIsGroup()
    {
        $object = TokenType::group();
        $this->assertTrue($object->isGroup());

        $object = TokenType::nonFoldedLiteral();
        $this->assertFalse($object->isGroup());
    }

    public function testIsListSeparator()
    {
        $object = TokenType::listSeparator();
        $this->assertTrue($object->isListSeparator());

        $object = TokenType::nonFoldedLiteral();
        $this->assertFalse($object->isListSeparator());
    }

    public function testPaired()
    {
        $object = TokenType::paired();
        $this->assertTrue($object->isPaired());

    }

    public function testAngledAddress()
    {
        $object = TokenType::angledAddress();
        $this->assertTrue($object->isAngledAddress());
    }
}
