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

use SalesAgility\Imap\Token\Tokenizer;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Imap\Token\Token;
use SalesAgility\Imap\Token\TokenType;
use SalesAgility\Imap\Token\TokenException;

/**
 * Class TokenizerTest
 * @see https://www.ietf.org/rfc/rfc2822.txt
 */
class TokenizerTest extends \Codeception\Test\Unit
{

    /** @var UnitTester  $tester*/
    protected $tester;
    public function testIsNotWhiteSpaceOrControl() {
        $object = new Tokenizer();
        $methodName = 'isNotWhiteSpaceOrControl';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        $actual = $method->invokeArgs($object, array('a'));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array('1'));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array('['));
        $this->assertTrue($actual);

        // negative test
        $actual = $method->invokeArgs($object, array("\x20"));
        $this->assertFalse($actual);

        $actual = $method->invokeArgs($object, array("\x09"));
        $this->assertFalse($actual);

        $actual = $method->invokeArgs($object, array("\x0D"));
        $this->assertFalse($actual);

        $actual = $method->invokeArgs($object, array("\x0A"));
        $this->assertFalse($actual);
    }
    public function testIsWhiteSpace() {
        $object = new Tokenizer();
        $methodName = 'isWhiteSpace';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        $actual = $method->invokeArgs($object, array("\x20"));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array("\x09"));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('a'));
        $this->assertFalse($actual);

        $actual = $method->invokeArgs($object, array('1'));
        $this->assertFalse($actual);

        $actual = $method->invokeArgs($object, array('['));
        $this->assertFalse($actual);
    }

    public function testisCarriageReturn() {
        $object = new Tokenizer();
        $methodName = 'isCarriageReturn';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        $actual = $method->invokeArgs($object, array("\x0D"));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array("\x09"));
        $this->assertFalse($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('a'));
        $this->assertFalse($actual);
    }

    public function testIsLineFeed() {
        $object = new Tokenizer();
        $methodName = 'isLineFeed';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        $actual = $method->invokeArgs($object, array("\x0A"));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array("\x09"));
        $this->assertFalse($actual);

        $actual = $method->invokeArgs($object, array('a'));
        $this->assertFalse($actual);
    }

    public function testIsSpecial() {
        $object = new Tokenizer();
        $methodName = 'isSpecial';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        $actual = $method->invokeArgs($object, array('('));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array(')'));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array('['));
        $this->assertTrue($actual);


        $actual = $method->invokeArgs($object, array(']'));
        $this->assertTrue($actual);


        $actual = $method->invokeArgs($object, array(';'));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array(':'));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array('<'));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array('>'));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array('\\'));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array('@'));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array('.'));
        $this->assertTrue($actual);

        $actual = $method->invokeArgs($object, array(','));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('\''));
        $this->assertFalse($actual);


        $actual = $method->invokeArgs($object, array('"'));
        $this->assertFalse($actual);
    }

    public function testIsQuotedPair() {
        $object = new Tokenizer();
        $methodName = 'isQuotedPair';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array('"'));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('\''));
        $this->assertFalse($actual);
    }

    public function testIsSpecialGroup()
    {
        $object = new Tokenizer();
        $methodName = 'isSpecialGroup';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array('('));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array(')'));
        $this->assertFalse($actual);
    }

    public function testIsSpecialClosingGroup()
    {
        $object = new Tokenizer();
        $methodName = 'isSpecialClosingGroup';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array(')'));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('('));
        $this->assertFalse($actual);
    }

    public function testIsSpecialOption() {
        $object = new Tokenizer();
        $methodName = 'isSpecialOption';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array('['));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array(']'));
        $this->assertFalse($actual);
    }

    public function testIsSpecialClosingOption()
    {
        $object = new Tokenizer();
        $methodName = 'isSpecialClosingOption';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array(']'));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('['));
        $this->assertFalse($actual);
    }

    public function testIsSpecialAngledAddress()
    {
        $object = new Tokenizer();
        $methodName = 'isSpecialAngledAddress';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array('<'));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('>'));
        $this->assertFalse($actual);
    }

    public function testIsSpecialClosingAngledAddress()
    {
        $object = new Tokenizer();
        $methodName = 'isSpecialClosingAngledAddress';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array('>'));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('<'));
        $this->assertFalse($actual);
    }

    public function testIsSpecialEscape() {
        $object = new Tokenizer();
        $methodName = 'isSpecialEscape';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array('\\'));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('a'));
        $this->assertFalse($actual);
    }

    public function testIsSpecialAt() {
        $object = new Tokenizer();
        $methodName = 'isSpecialAt';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array('@'));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('a'));
        $this->assertFalse($actual);
    }

    public function testIsSpecialDot() {
        $object = new Tokenizer();
        $methodName = 'isSpecialDot';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array('.'));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('a'));
        $this->assertFalse($actual);
    }

    public function testIsSpecialListSeperator() {
        $object = new Tokenizer();
        $methodName = 'isSpecialListSeparator';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array(','));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('a'));
        $this->assertFalse($actual);
    }


    public function testIsDoubleQuote() {
        $object = new Tokenizer();
        $methodName = 'isDoubleQuote';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $actual = $method->invokeArgs($object, array('"'));
        $this->assertTrue($actual);

        // negative tests
        $actual = $method->invokeArgs($object, array('a'));
        $this->assertFalse($actual);
    }

    public function testSeekNotWhiteSpaceOrControl()
    {
        $object = new Tokenizer();
        $methodName = 'seekNotWhiteSpaceOrControl';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        // negative tests
        $this->tester->expectException(
            new \InvalidArgumentException('Iterator::current() must not start with whitespace or control character'),
            function () use ($object, $method) {
                $iterator = StringIterator::withLiteral(' ');
                $method->invokeArgs($object, array(&$iterator));
            }
        );
    }

    public function testSeekEndOfLine () {
        $object = new Tokenizer();
        $methodName = 'seekEndOfLine';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        $string = "\x0D\x0A";
        $iterator = new StringIterator($string);
        // Move current position +1 after the opening character
        /** @var Token $actual */
        $actual = $method->invokeArgs($object, array(&$iterator));
        $actualString = $actual->toString();
        $this->assertEquals($string, $actualString);
        $this->assertInstanceOf(Token::class, $actual);
        $this->assertEquals($iterator->current(), "\x0A");
        $actual->rewind();
        $this->assertEquals($actual->current(), "\x0D");
        $actual->fastForward();
        $this->assertEquals($actual->current(), "\x0A");

        // negative tests
        $string = 'f' . "\x0D\x0A";
        $iterator = new StringIterator($string, 0);
        $this->tester->expectException(
            new InvalidArgumentException('Iterator::current() must start with a carriage return'),
            function () use ($object, $method, $iterator) {
                $method->invokeArgs($object, array(&$iterator));
            }
        );

        $string = "\x0D";
        $iterator = new StringIterator($string);
        // Move current position +1 after the opening character
        /** @var Token $actual */
        $actual = $method->invokeArgs($object, array(&$iterator));
        $this->assertEquals(false, $actual);

        $string = "\x0Da";
        $iterator = new StringIterator($string);
        // Move current position +1 after the opening character
        /** @var Token $actual */
        $actual = $method->invokeArgs($object, array(&$iterator));
        $this->assertEquals(false, $actual);
    }

    public function testSeekClosingPair()
    {
        $object = new Tokenizer();
        $methodName = 'seekClosingPair';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        $openCharacter = "\x28";
        $closeCharacter = "\x29";
        $string = '()';
        $iterator = new StringIterator($string);
        // Move current position +1 after the opening character
        /** @var Token $actual */
        $actual = $method->invokeArgs($object, array(&$iterator, $openCharacter, $closeCharacter));
        $this->assertEquals(')', $iterator->current(), 'Offset character must be the close character');
        $this->assertInstanceOf(Token::class, $actual);
        $this->assertTrue($actual->type()->isGroup());
        $this->assertEquals($actual->current(), '(', 'Offset character must be the open character');
        $actual->fastForward();
        $this->assertEquals($actual->current(), ')', 'Offset character must be the close character');

        $openCharacter = "\x28";
        $closeCharacter = "\x29";
        $string = '(())';
        $iterator = new StringIterator($string);
        // Move current position +1 after the opening character
        /** @var Token $actual */
        $actual = $method->invokeArgs($object, array(&$iterator, $openCharacter, $closeCharacter));
        $this->assertEquals(')', $iterator->current(), 'Offset character must be the close character');
        $this->assertInstanceOf(Token::class, $actual);
        $this->assertTrue($actual->type()->isGroup());
        $this->assertEquals($actual->current(), '(', 'Offset character must be the open character');
        $actual->fastForward();
        $this->assertEquals($actual->current(), ')', 'Offset character must be the close character');

        $openCharacter = "\x28";
        $closeCharacter = "\x29";
        $string = '(("Daniel" NIL "user" "localhost.localdomain"))';
        $iterator = new StringIterator($string);
        // Move current position +1 after the opening character
        /** @var Token $actual */
        $actual = $method->invokeArgs($object, array(&$iterator, $openCharacter, $closeCharacter));
        $this->assertEquals(')', $iterator->current(), 'Offset character must be the close character');
        $this->assertInstanceOf(Token::class, $actual);
        $this->assertEquals($actual->current(), '(', 'Offset character must be the open character');
        $this->assertTrue($actual->type()->isGroup());
        $this->assertEquals(')', $iterator->current(), 'Offset character must be the close character');
        $actual->fastForward();
        $this->assertEquals($actual->current(), ')', 'Offset character must be the close character');
        $this->assertEquals($actual->key(), strlen($string) - 1);

        $openCharacter = "\x28";
        $closeCharacter = "\x29";
        $string = '('."\x0D\x0A".'("Daniel" NIL "user" "localhost.localdomain")'."\x0D\x0A".')';
        $iterator = new StringIterator($string);
        // Move current position +1 after the opening character
        /** @var Token $actual */
        $actual = $method->invokeArgs($object, array(&$iterator, $openCharacter, $closeCharacter));
        $this->assertEquals(')', $iterator->current(), 'Offset character must be the close character');
        $this->assertInstanceOf(Token::class, $actual);
        $this->assertTrue($actual->type()->isGroup());
        $this->assertEquals($actual->current(), '(', 'Offset character must be the open character');
        $actual->fastForward();
        $this->assertEquals($actual->current(), ')', 'Offset character must be the close character');
        $this->assertEquals($actual->key(), strlen($string) - 1);

        $openCharacter = ":";
        $closeCharacter = ";";
        $string = ':value;comment';
        $iterator = new StringIterator($string);
        // Move current position +1 after the opening character
        /** @var Token $actual */
        $actual = $method->invokeArgs($object, array(&$iterator, $openCharacter, $closeCharacter));
        $this->assertEquals(';', $iterator->current(), 'Offset character must be the close character');
        $this->assertInstanceOf(Token::class, $actual);
        $this->assertTrue($actual->type()->isPaired());
        $this->assertEquals($actual->current(), ':', 'Offset character must be the open character');
        $actual->fastForward();
        $this->assertEquals($actual->current(), ';', 'Offset character must be the close character');

        // negative cases
        $openCharacter = "\x28";
        $closeCharacter = "\x29";
        $string = '(("Daniel" NIL "user" "localhost.localdomain")';
        $iterator = new StringIterator($string);
        // Move current position +1 after the opening character
        /** @var Token $actual */
        $actual = $method->invokeArgs($object, array(&$iterator, $openCharacter, $closeCharacter));
        $this->assertEquals(false, $actual);

        $string = '(("Daniel" NIL "user" "localhost.localdomain")';
        $iterator = new StringIterator($string, 2);
        $this->tester->expectException(
            new InvalidArgumentException('Iterator->current() position must be at the open character'),
            function () use ($object, $method, $iterator) {
                $method->invokeArgs($object, array(&$iterator, '(', ')'));
            }
        );
    }

    public function testSeekLineFolding()
    {
        // Test for non folded lines
        // Test for folded lines
        // as a message body may not use CRLF so therefor it is not line folding

        $object = new Tokenizer();
        $methodName = 'seekLineFolding';
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        // Positive Tests
        $string = 'a b '. "\x0D\x0A" . "\x20\x20" . 'd e f g' . "\x0D\x0A";
        $iterator = new StringIterator($string, 4);
        $actual = $method->invokeArgs($object, array(&$iterator));
        $this->assertNotEquals(true, $actual, 'seekLineFolding is not detecting the standard positive case');
        $this->assertInstanceOf(Token::class, $actual, 'seekLineFolding must return the token when a folded line has been detected');
        // TODO: Verify that the start and positions are correct

        // Negative Tests
        $string = 'a b '. "\x0D\x0A" . "\x20\x20" . 'd e f g' . "\x0D\x0A";
        $iterator = new StringIterator($string);
        $this->tester->expectException(
            new InvalidArgumentException('Iterator::current() must start with a carriage return'),
            function () use ($object, $method, $iterator) {
                $method->invokeArgs($object, array(&$iterator));
            }
        );

        $string = 'a b '. "\x0D\x0A" . "a:" . 'd e f g' . "\x0D\x0A";
        $iterator = new StringIterator($string, 4);
        $actual = $method->invokeArgs($object, array(&$iterator));
        $this->assertEquals(false, $actual, "seekLineFolding() failed to detect when the line folding does not exist");

        $string = 'a b '. "\x0D\x0A" . "\x0D" . 'd e f g' . "\x0D\x0A";
        $iterator = new StringIterator($string, 4);
        $actual = $method->invokeArgs($object, array(&$iterator));
        $this->assertEquals(false, $actual, 'seekLineFolding() failed to detect control characters');

        $string = "\x0D";
        $iterator = new StringIterator($string);
        $actual = $method->invokeArgs($object, array(&$iterator));
        $this->assertEquals(false, $actual, "seekLineFolding() failed to detect corrupted line ending");

        $string = "\x0D\x0A";
        $iterator = new StringIterator($string);
        $actual = $method->invokeArgs($object, array(&$iterator));
        $this->assertEquals(false, $actual, "seekLineFolding() failed to detect end of file");

        $string = "\x0D\x0A\x0D\x0A";
        $iterator = new StringIterator($string);
        $actual = $method->invokeArgs($object, array(&$iterator));
        $this->assertEquals(false, $actual, "seekLineFolding() failed to detect 2 x EOL");
    }

    // Please keep the parser until last
    // as it will like help work our any issues faster
    public function testParse()
    {
        $object = new Tokenizer();
        // test with group
        $iterator = StringIterator::withLiteral('20 FETCH 1:20 (FLAGS UID BODY[HEADER] BODYSTRUCTURE)'."\x0D\x0A");
        /** @var \SalesAgility\Imap\Token\TokenList $tokenlist */
        $tokenlist = $object->parse($iterator);
        $this->assertCount(10, $tokenlist);
        $this->assertEquals('20', $tokenlist->offsetGet(0)->toString());
        $this->assertEquals("\x20", $tokenlist->offsetGet(1)->toString());
        $this->assertEquals('FETCH', $tokenlist->offsetGet(2)->toString());
        $this->assertEquals("\x20", $tokenlist->offsetGet(3)->toString());
        $this->assertEquals('1', $tokenlist->offsetGet(4)->toString());
        $this->assertEquals(':', $tokenlist->offsetGet(5)->toString());
        $this->assertEquals('20', $tokenlist->offsetGet(6)->toString());
        $this->assertEquals("\x20", $tokenlist->offsetGet(7)->toString());
        $this->assertEquals('(FLAGS UID BODY[HEADER] BODYSTRUCTURE)', $tokenlist->offsetGet(8)->toString());
        $this->assertEquals("\x0D\x0A", $tokenlist->offsetGet(9)->toString());

        // test with optional
        $iterator = StringIterator::withLiteral('* OK [PERMANENTFLAGS (\Answered \Flagged \Deleted \Seen \Draft \*)] Flags permitted.'."\x0D\x0A");
        /** @var \SalesAgility\Imap\Token\TokenList $tokenlist */
        $tokenlist = $object->parse($iterator);
        $this->assertCount(11, $tokenlist);
        // test with DQUOTE group
        $iterator = StringIterator::withLiteral('INTERNALDATE "05-Jun-2018 08:59:50 +0100"');
        /** @var \SalesAgility\Imap\Token\TokenList $tokenlist */
        $tokenlist = $object->parse($iterator);
        $this->assertCount(3, $tokenlist);

        // test with angle brackets
        $iterator =  StringIterator::withLiteral('<address>');
        $tokenlist = $object->parse($iterator);
        $this->assertCount(1, $tokenlist);

        // test list separator
        // test @ sign
        $iterator =  StringIterator::withLiteral('A <a@localhost>, B <b@localhost>');
        $tokenlist = $object->parse($iterator);
        $this->assertCount(8, $tokenlist);


        // test escaping
        $iterator = StringIterator::withLiteral('\Answered');
        $tokenlist = $object->parse($iterator);
        $this->assertCount(2, $tokenlist);

        // test .
        $iterator = StringIterator::withLiteral('domain.tld');
        $tokenlist = $object->parse($iterator);
        $this->assertCount(3, $tokenlist);

        // CTL
        $iterator = StringIterator::withLiteral("\x0D");
        $tokenlist = $object->parse($iterator);
        $this->assertCount(1, $tokenlist);

        $iterator = StringIterator::withLiteral("\x0A");
        $tokenlist = $object->parse($iterator);
        $this->assertCount(1, $tokenlist);


        // test line folding
        $string = 'a b '. "\x0D\x0A" . "\x20\x20" . 'd e' . "\x0D\x0A";
        $iterator = StringIterator::withLiteral($string);
        $tokenlist = $object->parse($iterator);
        $this->assertCount(9, $tokenlist);

        // closing groups
        $iterator = StringIterator::withLiteral(" ) ");
        $tokenlist = $object->parse($iterator);
        $this->assertCount(3, $tokenlist);

        // closing option
        $iterator = StringIterator::withLiteral(" ] ");
        $tokenlist = $object->parse($iterator);
        $this->assertCount(3, $tokenlist);

        // closing DQUOTE
        $iterator = StringIterator::withLiteral(" \" ");
        $tokenlist = $object->parse($iterator);
        $this->assertCount(3, $tokenlist);

        // closing angle bracket
        $iterator = StringIterator::withLiteral(" > ");
        $tokenlist = $object->parse($iterator);
        $this->assertCount(3, $tokenlist);

        $iterator = StringIterator::withLiteral('@');
        $tokenlist = $object->parse($iterator);
        $this->assertCount(1, $tokenlist);


        // test possible errors
        $iterator = StringIterator::withLiteral('(()');
        $tokenlist = $object->parse($iterator);
        $this->assertCount(2, $tokenlist);

        $iterator = StringIterator::withLiteral('<<>');
        $tokenlist = $object->parse($iterator);
        $this->assertCount(2, $tokenlist);

        $iterator = StringIterator::withLiteral('[[]');
        $tokenlist = $object->parse($iterator);
        $this->assertCount(2, $tokenlist);


        $iterator = StringIterator::withLiteral('<<>');
        $tokenlist = $object->parse($iterator);
        $this->assertCount(2, $tokenlist);

        // line length
        try {
            $justRight = str_repeat("\x20", 998) . "\x0D\x0A";
            $iterator = StringIterator::withLiteral($justRight);
            $tokenlist = $object->parse($iterator);
            // Keep in mind that the tokenizer
            // creates 998 for the spaces + a token to mark the limit
            // + a token for the CRLF
            $this->assertCount(1001, $tokenlist);
        } catch (TokenException $e) {
            $this->tester->fail('tokenizer required line length exceeded too early');
        }

        // negative tests
        // line length
        $this->tester->expectException(
            TokenException::requiredLineLengthExceeded(),
            function () {
                $object = new Tokenizer();
                $tooLong = str_repeat("\x20", 999) . "\x0D\x0A";
                $iterator = StringIterator::withLiteral($tooLong);
                $object->parse($iterator);
            }
        );


        $this->tester->expectException(
            TokenException::requiredLineLengthExceeded(),
            function () {
                $object = new Tokenizer();
                $tooLong = str_repeat("\x20", 998) . "\x0D\x20";
                $iterator = StringIterator::withLiteral($tooLong);
                $object->parse($iterator);
            }
        );

        $this->tester->expectException(
            TokenException::requiredLineLengthExceeded(),
            function () {
                $object = new Tokenizer();
                $tooLong = str_repeat("\x20", 1000);
                $iterator = StringIterator::withLiteral($tooLong);
                $object->parse($iterator);
            }
        );

        $this->tester->expectException(
            TokenException::requiredLineLengthExceeded(),
            function () {
                $object = new Tokenizer();
                $tooLong = str_repeat("\x20", 998) . "x0A";
                $iterator = StringIterator::withLiteral($tooLong);
                $object->parse($iterator);
            }
        );

        $this->tester->expectException(
            TokenException::requiredLineLengthExceeded(),
            function () {
                $object = new Tokenizer();
                $tooLong = str_repeat("\x20", 998) . "\x29";
                $iterator = StringIterator::withLiteral($tooLong);
                $object->parse($iterator);
            }
        );


        $this->tester->expectException(
            TokenException::requiredLineLengthExceeded(),
            function () {
                $object = new Tokenizer();
                $tooLong = str_repeat("\x20", 998) . "\x0A\x20";
                $iterator = StringIterator::withLiteral($tooLong);
                $object->parse($iterator);
            }
        );

    }

    public function testParseWithoutLineRestrictions()
    {
        $object = new Tokenizer();
        // test with group
        $iterator = StringIterator::withLiteral('20 FETCH 1:20 (FLAGS UID BODY[HEADER] BODYSTRUCTURE)'."\x0D\x0A");
        /** @var \SalesAgility\Imap\Token\TokenList $tokenlist */
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(10, $tokenlist);
        $this->assertEquals('20', $tokenlist->offsetGet(0)->toString());
        $this->assertEquals("\x20", $tokenlist->offsetGet(1)->toString());
        $this->assertEquals('FETCH', $tokenlist->offsetGet(2)->toString());
        $this->assertEquals("\x20", $tokenlist->offsetGet(3)->toString());
        $this->assertEquals('1', $tokenlist->offsetGet(4)->toString());
        $this->assertEquals(':', $tokenlist->offsetGet(5)->toString());
        $this->assertEquals('20', $tokenlist->offsetGet(6)->toString());
        $this->assertEquals("\x20", $tokenlist->offsetGet(7)->toString());
        $this->assertEquals('(FLAGS UID BODY[HEADER] BODYSTRUCTURE)', $tokenlist->offsetGet(8)->toString());
        $this->assertEquals("\x0D\x0A", $tokenlist->offsetGet(9)->toString());

        // test with optional
        $iterator = StringIterator::withLiteral('* OK [PERMANENTFLAGS (\Answered \Flagged \Deleted \Seen \Draft \*)] Flags permitted.'."\x0D\x0A");
        /** @var \SalesAgility\Imap\Token\TokenList $tokenlist */
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(11, $tokenlist);
        // test with DQUOTE group
        $iterator = StringIterator::withLiteral('INTERNALDATE "05-Jun-2018 08:59:50 +0100"');
        /** @var \SalesAgility\Imap\Token\TokenList $tokenlist */
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(3, $tokenlist);

        // test with angle brackets
        $iterator =  StringIterator::withLiteral('<address>');
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(1, $tokenlist);

        // test list separator
        // test @ sign
        $iterator =  StringIterator::withLiteral('A <a@localhost>, B <b@localhost>');
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(8, $tokenlist);


        // test escaping
        $iterator = StringIterator::withLiteral('\Answered');
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(2, $tokenlist);

        // test .
        $iterator = StringIterator::withLiteral('domain.tld');
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(3, $tokenlist);

        // CTL
        $iterator = StringIterator::withLiteral("\x0D");
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(1, $tokenlist);

        $iterator = StringIterator::withLiteral("\x0A");
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(1, $tokenlist);


        // test line folding
        $string = 'a b '. "\x0D\x0A" . "\x20\x20" . 'd e' . "\x0D\x0A";
        $iterator = StringIterator::withLiteral($string);
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(9, $tokenlist);

        // closing groups
        $iterator = StringIterator::withLiteral(" ) ");
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(3, $tokenlist);

        // closing option
        $iterator = StringIterator::withLiteral(" ] ");
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(3, $tokenlist);

        // closing DQUOTE
        $iterator = StringIterator::withLiteral(" \" ");
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(3, $tokenlist);

        // closing angle bracket
        $iterator = StringIterator::withLiteral(" > ");
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(3, $tokenlist);

        $iterator = StringIterator::withLiteral('@');
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(1, $tokenlist);


        // test possible errors
        $iterator = StringIterator::withLiteral('(()');
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(2, $tokenlist);

        $iterator = StringIterator::withLiteral('<<>');
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(2, $tokenlist);

        $iterator = StringIterator::withLiteral('[[]');
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(2, $tokenlist);


        $iterator = StringIterator::withLiteral('<<>');
        $tokenlist = $object->parseWithoutLineRestrictions($iterator);
        $this->assertCount(2, $tokenlist);

    }
}
