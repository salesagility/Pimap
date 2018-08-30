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

use SalesAgility\Imap\Interpreter\MessageInterpreter;
use SalesAgility\Iteration\StringIterator;

class MessageInterpreterTest extends \Codeception\Test\Unit
{
    public function testParse()
    {
        // FETCH 1 (UID)\r\n
        $object = new MessageInterpreter();
        $response = StringIterator::withLiteral("\x0D\x0A* 1 FETCH (UID 1000)\x0D\x0A");
        $actual = $object->parse($response);
        $this->assertEquals('1000', $actual->offsetGet(0)->uid());
        $this->assertEquals('1', $actual->offsetGet(0)->number());
        //
        $response = StringIterator::withLiteral("* 1 FETCH (UID 1000)\x0D\x0A");
        $actual = $object->parse($response);
        $this->assertEquals('1000', $actual->offsetGet(0)->uid());
        $this->assertEquals('1', $actual->offsetGet(0)->number());

        // Multiple Messages
        $response = StringIterator::withLiteral("* 1 FETCH (UID 1000)\x0D\x0A* 2 FETCH (UID 2000)\x0D\x0A");
        $actual = $object->parse($response);
        $this->assertEquals('1000', $actual->offsetGet(0)->uid());
        $this->assertEquals('1', $actual->offsetGet(0)->number());
        $this->assertEquals('2000', $actual->offsetGet(1)->uid());
        $this->assertEquals('2', $actual->offsetGet(1)->number());

        // FETCH 1 (UID FLAGS)
        $response = StringIterator::withLiteral("* 1 FETCH (UID 1000 FLAGS (\Seen \Answered))\x0D\x0A");
        $actual = $object->parse($response);
        $this->assertEquals('1000', $actual->offsetGet(0)->uid());
        $this->assertEquals('1', $actual->offsetGet(0)->number());
        $this->assertTrue($actual->offsetGet(0)->flags()->isAnswered());
        $this->assertTrue($actual->offsetGet(0)->flags()->isSeen());
        $this->assertFalse($actual->offsetGet(0)->flags()->isDeleted());

        // test mixing the order
        $response = StringIterator::withLiteral("* 1 FETCH (FLAGS (\Seen \Answered) UID 1000)\x0D\x0A");
        $actual = $object->parse($response);
        $this->assertEquals('1000', $actual->offsetGet(0)->uid());
        $this->assertEquals('1', $actual->offsetGet(0)->number());
        $this->assertTrue($actual->offsetGet(0)->flags()->isAnswered());
        $this->assertTrue($actual->offsetGet(0)->flags()->isSeen());
        $this->assertFalse($actual->offsetGet(0)->flags()->isDeleted());


        // FETCH 1 (BODYSTRUCTURE)
        $response = StringIterator::withLiteral("* 1 FETCH (BODYSTRUCTURE (\"TEXT\" \"PLAIN\" (\"CHARSET\" \"UTF-8\") NIL NIL \"7BIT\" 38 1 NIL NIL NIL))\x0D\x0A");
        $actual = $object->parse($response);
        $this->assertEquals('1', $actual->offsetGet(0)->number());
        $this->assertTrue($actual->offsetGet(0)->body()->structure()->plainTextBodyExists());
        $this->assertFalse($actual->offsetGet(0)->body()->structure()->htmlBodyExists());
        $this->assertFalse($actual->offsetGet(0)->body()->structure()->attachmentsExists());

        $response = StringIterator::withLiteral('* 1 FETCH (BODYSTRUCTURE (("text" "plain" ("charset" "utf-8" "Imap" "flowed") NIL NIL "7bit" 1399 47 NIL NIL NIL NIL)("text" "html" ("charset" "utf-8") NIL NIL "7bit" 4570 116 NIL NIL NIL NIL)"alternative" ("boundary" "------------A89A745D2D07E229D39052C8") NIL NIL NIL))'."\x0D\x0A");
        $actual = $object->parse($response);
        $this->assertEquals('1', $actual->offsetGet(0)->number());
        $this->assertTrue($actual->offsetGet(0)->body()->structure()->plainTextBodyExists());
        $this->assertTrue($actual->offsetGet(0)->body()->structure()->htmlBodyExists());
        $this->assertFalse($actual->offsetGet(0)->body()->structure()->attachmentsExists());

        $response = StringIterator::withLiteral('* 1 FETCH (BODYSTRUCTURE ((("text" "plain" ("charset" "us-ascii") NIL NIL "7bit" 58 1 NIL NIL NIL NIL)("text" "html" ("charset" "us-ascii") NIL NIL "7bit" 49 2 NIL NIL NIL NIL) "alternative" ("boundary" "b2_C5VDszZ1ehFtRLkuszAURx9g9bPNLBC7V6l7ellTA") NIL NIL NIL)("text" "plain" ("name" "Plaintext" "charset" "us-ascii") "<Plaintext>" NIL "base64" 18 1 NIL ("attachment" ("filename" "Plaintext")) NIL NIL) "mixed" ("boundary" "b1_C5VDszZ1ehFtRLkuszAURx9g9bPNLBC7V6l7ellTA") NIL NIL NIL)))'."\x0D\x0A");
        $actual = $object->parse($response);
        $this->assertEquals('1', $actual->offsetGet(0)->number());
        $this->assertTrue($actual->offsetGet(0)->body()->structure()->plainTextBodyExists());
        $this->assertTrue($actual->offsetGet(0)->body()->structure()->htmlBodyExists());
        $this->assertTrue($actual->offsetGet(0)->body()->structure()->attachmentsExists());

        // Plain text with attachments
        $response = StringIterator::withLiteral('* 1 FETCH (BODYSTRUCTURE (("TEXT" "PLAIN" ("CHARSET" "UTF-8") NIL NIL "7BIT" 86 2 NIL NIL NIL)("IMAGE" "PNG" ("NAME" "zoltan-api-errors.png") NIL NIL "BASE64" 327336 NIL ("ATTACHMENT" ("FILENAME" "zoltan-api-errors.png")) NIL) "MIXED" ("BOUNDARY" "00000000000088a9bc05712e52c2") NIL NIL))'."\x0D\x0A");
        $actual = $object->parse($response);
        $this->assertEquals('1', $actual->offsetGet(0)->number());
        $this->assertTrue($actual->offsetGet(0)->body()->structure()->plainTextBodyExists());
        $this->assertFalse($actual->offsetGet(0)->body()->structure()->htmlBodyExists());
        $this->assertTrue($actual->offsetGet(0)->body()->structure()->attachmentsExists());

        // Plain text email with html attachment
        // The interpreter must not confuse the html attachment with the html body
        $response = StringIterator::withLiteral('* 1 FETCH (BODYSTRUCTURE (("TEXT" "PLAIN" ("CHARSET" "UTF-8") NIL NIL "7BIT" 66 2 NIL NIL NIL)("TEXT" "HTML" ("CHARSET" "UTF-8" "NAME" "Trick.html") NIL NIL "BASE64" 47952 960 NIL ("ATTACHMENT" ("FILENAME" "Trick.html")) NIL) "MIXED" ("BOUNDARY" "0000000000001b837c05712e6685") NIL NIL))'."\x0D\x0A");
        $actual = $object->parse($response);
        $this->assertEquals('1', $actual->offsetGet(0)->number());
        $this->assertTrue($actual->offsetGet(0)->body()->structure()->plainTextBodyExists());
        $this->assertFalse($actual->offsetGet(0)->body()->structure()->htmlBodyExists());
        $this->assertTrue($actual->offsetGet(0)->body()->structure()->attachmentsExists());

        // FETCH 1 (BODY[HEADER])
        $response = StringIterator::withLiteral(file_get_contents(codecept_data_dir().'FETCH_BODY_HEADER.txt'));
        $actual = $object->parse($response);
        $this->assertTrue($actual->offsetGet(0)->hasHeader());
        $this->assertNotEmpty($actual->offsetGet(0)->header()->date());
        $this->assertEquals(1528185590, $actual->offsetGet(0)->header()->date()->getTimestamp());
        $this->assertEquals(array('Daniel <user@localhost.localdomain>'), $actual->offsetGet(0)->header()->to());
        $this->assertEquals(array('Mailer <root@localhost.localdomain>'), $actual->offsetGet(0)->header()->from());
        $this->assertEquals(array('Information <user@localhost.localdomain>'), $actual->offsetGet(0)->header()->replyTo());
        $this->assertEquals('Test Email: Baron Hartmann', $actual->offsetGet(0)->header()->subject());
        $this->assertEmpty($actual->offsetGet(0)->header()->cc());
        $this->assertEmpty($actual->offsetGet(0)->header()->bcc());

        $response = StringIterator::withLiteral(file_get_contents(codecept_data_dir().'FETCH_BODY_HEADER.txt'));
        $actual = $object->parse($response);
        $this->assertTrue($actual->offsetGet(0)->hasHeader());
        $this->assertNotEmpty($actual->offsetGet(0)->header()->date());
        $this->assertEquals(1528185590, $actual->offsetGet(0)->header()->date()->getTimestamp());
        $this->assertEquals(array('Daniel <user@localhost.localdomain>'), $actual->offsetGet(0)->header()->to());
        $this->assertEquals(array('Mailer <root@localhost.localdomain>'), $actual->offsetGet(0)->header()->from());
        $this->assertEquals(array('Information <user@localhost.localdomain>'), $actual->offsetGet(0)->header()->replyTo());
        $this->assertEquals('Test Email: Baron Hartmann', $actual->offsetGet(0)->header()->subject());
        $this->assertEmpty($actual->offsetGet(0)->header()->cc());
        $this->assertEmpty($actual->offsetGet(0)->header()->bcc());

        $response = StringIterator::withLiteral(file_get_contents(codecept_data_dir().'FETCH_UID_FLAGS_BODY_HEADER_BODYSTRUCTURE.txt'));
        $actual = $object->parse($response);
        $this->assertTrue($actual->offsetGet(0)->hasHeader());
        $this->assertNotEmpty($actual->offsetGet(0)->header()->date());
        $this->assertEquals(1528185590, $actual->offsetGet(0)->header()->date()->getTimestamp());
        $this->assertEquals(array('Daniel <user@localhost.localdomain>'), $actual->offsetGet(0)->header()->to());
        $this->assertEquals(array('Mailer <root@localhost.localdomain>'), $actual->offsetGet(0)->header()->from());
        $this->assertEquals(array('Information <user@localhost.localdomain>'), $actual->offsetGet(0)->header()->replyTo());
        $this->assertEquals('Test Email: Baron Hartmann', $actual->offsetGet(0)->header()->subject());
        $this->assertEmpty($actual->offsetGet(0)->header()->cc());
        $this->assertEmpty($actual->offsetGet(0)->header()->bcc());

        // Test charset encoding like UTF-8
        // Test multiple email addresses
        // Test when keywords do not appear the correct order
        $response = StringIterator::withLiteral(file_get_contents(codecept_data_dir().'FETCH_FLAGS_BODY_HEADER_BODYSTRUCTURE_UTF8.txt'));
        $actual = $object->parse($response);
        $this->assertTrue($actual->offsetGet(0)->hasHeader());
        $this->assertNotEmpty($actual->offsetGet(0)->header()->date());
        $this->assertEquals(1531235225, $actual->offsetGet(0)->header()->date()->getTimestamp());
        $to = array(
            'Daniel <daniel@example.com>',
            'Ashley <ashley@example.com>',
        );
        $this->assertEquals($to, $actual->offsetGet(0)->header()->to());
        $this->assertEquals(array('Joe <joe@example.com>'), $actual->offsetGet(0)->header()->from());
        $this->assertEquals(array('Joe <joe@example.com>'), $actual->offsetGet(0)->header()->replyTo());
        $this->assertEquals('RE: Test Email: Please Ignore', $actual->offsetGet(0)->header()->subject());
        $this->assertEquals('<kcim.5b44cb99.2f81.6604c5126d8c32d7@hoza15.fra2.bytemine.net>', $actual->offsetGet(0)->header()->messageId());
        $this->assertEmpty($actual->offsetGet(0)->header()->cc());
        $this->assertEmpty($actual->offsetGet(0)->header()->bcc());

        // TODO: test cc and bcc
        // TODO: 100% code coverage
        
        // Test body plain text email
        $response = StringIterator::withLiteral(file_get_contents(codecept_data_dir().'PLAIN_FETCH_UID_BODY_HEADER_BODYSTUCTURE_BODY.txt'));
        $actual = $object->parse($response);
        $this->assertTrue($actual->offsetGet(0)->hasHeader());
        $this->assertNotEmpty($actual->offsetGet(0)->header()->date());
        $this->assertEquals(1529494340, $actual->offsetGet(0)->header()->date()->getTimestamp());
        $to = array(
            'qweqwe <xxxxxxxxx@gmail.com>',
        );
        $this->assertEquals($to, $actual->offsetGet(0)->header()->to());
        $this->assertEquals(array('Daniel xxxxxx <xxxxxxxxx@gmail.com>'), $actual->offsetGet(0)->header()->from());
        $this->assertEquals(array('Daniel xxxxxx <xxxxxxxxx@gmail.com>'), $actual->offsetGet(0)->header()->replyTo());
        $this->assertEquals('Plain text email', $actual->offsetGet(0)->header()->subject());
        $this->assertEmpty($actual->offsetGet(0)->header()->cc());
        $this->assertEmpty($actual->offsetGet(0)->header()->bcc());
        $expectedBody = 'Hi this should only be in plain text';
        $actualBody = $actual->offsetGet(0)->body()->text();
        $this->assertEquals($expectedBody, $actualBody);
        // Test body plain text email with attachments
        // Test body html/plain text email
        // Test body html/plain text email with attachments
        
        
        $response = StringIterator::withLiteral(file_get_contents(codecept_data_dir().'FLAGS_UID_BODY_HEADER_BODYSTRUCTURE_BODY_TEXT.txt'));
        $actual = $object->parse($response);

        $this->assertTrue($actual->offsetGet(0)->hasHeader());
        $this->assertNotEmpty($actual->offsetGet(0)->header()->date());
        $this->assertEquals(1516806498, $actual->offsetGet(0)->header()->date()->getTimestamp());
        $to = array(
            'Administrator <xxxxxxxxx@gmail.com>',
        );
        $this->assertEquals($to, $actual->offsetGet(0)->header()->to());
        $this->assertEquals(array('SuiteCRM <xxxxxxxxx@gmail.com>'), $actual->offsetGet(0)->header()->from());
        $this->assertEquals(array('SuiteCRM <xxxxxxxxx@gmail.com>'), $actual->offsetGet(0)->header()->replyTo());
        $this->assertEquals('', $actual->offsetGet(0)->header()->subject());
        $this->assertEmpty($actual->offsetGet(0)->header()->cc());
        $this->assertEmpty($actual->offsetGet(0)->header()->bcc());
        $this->assertNotEmpty($actual->offsetGet(0)->body()->text());
        $this->assertNotEmpty($actual->offsetGet(0)->body()->html());


        $response = StringIterator::withLiteral(file_get_contents(codecept_data_dir().'FETCH_RANGE.txt'));
        $actual = $object->parse($response);

        $this->assertTrue($actual->offsetGet(0)->hasHeader());
        $this->assertNotEmpty($actual->offsetGet(0)->header()->date());
        $this->assertEquals(1515494001, $actual->offsetGet(0)->header()->date()->getTimestamp());
        $to = array(
            'UuUuUu UuUuUu <UuUuUuUuU@gmail.com>',
        );
        $this->assertEquals($to, $actual->offsetGet(0)->header()->to());
        $this->assertEquals(array('Gmail Team <mail-noreply@google.com>'), $actual->offsetGet(0)->header()->from());
        $this->assertEquals(array('Gmail Team <mail-noreply@google.com>'), $actual->offsetGet(0)->header()->replyTo());
        $this->assertEquals('Three tips to get the most out of Gmail', $actual->offsetGet(0)->header()->subject());
        $this->assertEmpty($actual->offsetGet(0)->header()->cc());
        $this->assertEmpty($actual->offsetGet(0)->header()->bcc());
        $this->assertNotEmpty($actual->offsetGet(0)->body()->text());
        $this->assertTrue(\SalesAgility\Utility\StringValue::startsWith($actual->offsetGet(0)->body()->text(), 'Three tips to get the most out of Gmail'));
        $this->assertNotEmpty($actual->offsetGet(0)->body()->html());
        $this->assertTrue(\SalesAgility\Utility\StringValue::startsWith($actual->offsetGet(0)->body()->html(), "\r\n".'<!DOCTYPE html>'));
    }
}
