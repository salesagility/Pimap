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

use SalesAgility\Imap\Lexeme\Lexemizer;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Imap\Token\Tokenizer;
use \SalesAgility\Imap\Lexeme\LexemeList;
use \SalesAgility\Imap\Lexeme\LexemeType;

class LexemizerTest extends \Codeception\Test\Unit
{
    private function octetCount(LexemeList $lexemeList) {
        $octetCount = 0;
        foreach ($lexemeList as $lexeme){
            $octetCount += $lexeme->octetCount();
        };
        return $octetCount;
    }

    public function testParse()
    {
        $object = new Lexemizer();
        $tokenizer = new Tokenizer();

        $keywordTest = StringIterator::withLiteral('*');
        $tokenlist = $tokenizer->parse($keywordTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertEquals(1, $this->octetCount($lexemeList));

        $keywordTest = StringIterator::withLiteral('1234');
        $tokenlist = $tokenizer->parse($keywordTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::allNumbers()));
        $this->assertEquals(4, $this->octetCount($lexemeList));

        $keywordTest = StringIterator::withLiteral('FETCH');
        $tokenlist = $tokenizer->parse($keywordTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::allCapitals()));
        $this->assertEquals(5, $this->octetCount($lexemeList));

        $keywordTest = StringIterator::withLiteral('RFC822');
        $tokenlist = $tokenizer->parse($keywordTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::capitalsNumbers()));
        $this->assertEquals(6, $this->octetCount($lexemeList));

        $keywordTest = StringIterator::withLiteral('"a"');
        $tokenlist = $tokenizer->parse($keywordTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::quotedString()));
        $this->assertEquals(3, $this->octetCount($lexemeList));

        $keywordTest = StringIterator::withLiteral('b');
        $tokenlist = $tokenizer->parse($keywordTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertFalse($lexemeList[0]->hasType(LexemeType::allCapitals()));
        $this->assertFalse($lexemeList[0]->hasType(LexemeType::allNumbers()));
        $this->assertFalse($lexemeList[0]->hasType(LexemeType::capitalsNumbers()));
        $this->assertEquals(1, $this->octetCount($lexemeList));


        $keywordTest = StringIterator::withLiteral('b15');
        $tokenlist = $tokenizer->parse($keywordTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertFalse($lexemeList[0]->hasType(LexemeType::allCapitals()));
        $this->assertFalse($lexemeList[0]->hasType(LexemeType::allNumbers()));
        $this->assertFalse($lexemeList[0]->hasType(LexemeType::capitalsNumbers()));
        $this->assertEquals(3, $this->octetCount($lexemeList));

        $keywordTest = StringIterator::withLiteral('B15a');
        $tokenlist = $tokenizer->parse($keywordTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertFalse($lexemeList[0]->hasType(LexemeType::allCapitals()));
        $this->assertFalse($lexemeList[0]->hasType(LexemeType::allNumbers()));
        $this->assertFalse($lexemeList[0]->hasType(LexemeType::capitalsNumbers()));
        $this->assertEquals(4, $this->octetCount($lexemeList));


        $notGroupTest = StringIterator::withLiteral(' this is not a group :(');
        $tokenlist = $tokenizer->parse($notGroupTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertEquals(23, $this->octetCount($lexemeList));

        $groupTest1Level = StringIterator::withLiteral('("charset" "us-ascii")');
        $tokenlist = $tokenizer->parse($groupTest1Level);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::group()));
        // Tokens inside
        $this->assertTrue($lexemeList->offsetGet(0)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(1)->offsetGet(0)->type()->isQuoted());
        $this->assertTrue($lexemeList->offsetGet(2)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(3)->offsetGet(0)->type()->isQuoted());
        $this->assertEquals(22, $this->octetCount($lexemeList));


        $groupTest2Level  = StringIterator::withLiteral('("text" ("charset" "us-ascii"))');
        $tokenlist = $tokenizer->parse($groupTest2Level);
        $lexemeList = $object->parse($tokenlist);

        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::group()));
        // Tokens inside
        $this->assertTrue($lexemeList->offsetGet(0)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(1)->offsetGet(0)->type()->isQuoted());
        $this->assertTrue($lexemeList->offsetGet(2)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(3)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(4)->offsetGet(0)->type()->isQuoted());
        $this->assertTrue($lexemeList->offsetGet(5)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(6)->offsetGet(0)->type()->isQuoted());
        $this->assertEquals(31, $this->octetCount($lexemeList));


        $groupTest2Level  = StringIterator::withLiteral('("text" ("charset" "us-ascii")("charset" "us-ascii"))');
        $tokenlist = $tokenizer->parse($groupTest2Level);
        $lexemeList = $object->parse($tokenlist);

        $this->assertTrue($lexemeList[0]->hasType(LexemeType::atom()));
        $this->assertTrue($lexemeList[0]->hasType(LexemeType::group()));
        // Tokens inside
        // it should flatten the structure to just one level
        // Interpreters can read the group tokens to ensure workout where the last character
        $this->assertTrue($lexemeList->offsetGet(0)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(1)->offsetGet(0)->type()->isQuoted());
        $this->assertTrue($lexemeList->offsetGet(2)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(3)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(4)->offsetGet(0)->type()->isQuoted());
        $this->assertTrue($lexemeList->offsetGet(5)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(6)->offsetGet(0)->type()->isQuoted());
        $this->assertTrue($lexemeList->offsetGet(7)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(8)->offsetGet(0)->type()->isQuoted());
        $this->assertTrue($lexemeList->offsetGet(9)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(10)->offsetGet(0)->type()->isQuoted());
        $this->assertEquals(53, $this->octetCount($lexemeList));

        $groupTest3Level = StringIterator::withLiteral('((("charset")))');
        $tokenlist = $tokenizer->parse($groupTest3Level);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList->offsetGet(0)->hasType(LexemeType::atom()));
        $this->assertTrue($lexemeList->offsetGet(0)->hasType(LexemeType::group()));

        $this->assertTrue($lexemeList->offsetGet(0)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(1)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(2)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(3)->offsetGet(0)->type()->isQuoted());
        $this->assertEquals(15, $this->octetCount($lexemeList));

        $groupTest3Level = StringIterator::withLiteral('(("a" ("b"))("c" ("d")) "e" ("f"))');
        $tokenlist = $tokenizer->parse($groupTest3Level);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList->offsetGet(0)->hasType(LexemeType::atom()));
        $this->assertTrue($lexemeList->offsetGet(0)->hasType(LexemeType::group()));
        //
        $this->assertTrue($lexemeList->offsetGet(0)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(1)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(2)->offsetGet(0)->type()->isQuoted()); // a
        $this->assertTrue($lexemeList->offsetGet(3)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(4)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(5)->offsetGet(0)->type()->isQuoted()); // b
        $this->assertTrue($lexemeList->offsetGet(6)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(7)->offsetGet(0)->type()->isQuoted()); // c
        $this->assertTrue($lexemeList->offsetGet(8)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(9)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(10)->offsetGet(0)->type()->isQuoted()); // d
        $this->assertTrue($lexemeList->offsetGet(11)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(12)->offsetGet(0)->type()->isQuoted()); // e
        $this->assertTrue($lexemeList->offsetGet(13)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(14)->offsetGet(0)->type()->isGroup());
        $this->assertTrue($lexemeList->offsetGet(15)->offsetGet(0)->type()->isQuoted()); // f
        $this->assertEquals(34, $this->octetCount($lexemeList));;

        // Tokens inside
        // it should flatten the structure to just one level
        // Interpreters can read the group tokens to work out where the last character for a group is

        // test optional
        $optionalTest = StringIterator::withLiteral('* OK [PERMANENTFLAGS (\Answered \Flagged \Deleted \Seen \Draft \*)] Flags permitted.'."\x0d\x0A");
        $tokenlist = $tokenizer->parse($optionalTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList->offsetGet(0)->offsetGet(0)->type()->isNotWhiteSpaceOrControl());
        $this->assertTrue($lexemeList->offsetGet(1)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(2)->offsetGet(0)->type()->isNotWhiteSpaceOrControl());
        $this->assertTrue($lexemeList->offsetGet(3)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(4)->hasType(LexemeType::optional()));
        $this->assertTrue($lexemeList->offsetGet(4)->offsetGet(0)->type()->isNonFoldedLiteral());
        $this->assertTrue($lexemeList->offsetGet(5)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(6)->offsetGet(0)->type()->isNotWhiteSpaceOrControl());
        $this->assertTrue($lexemeList->offsetGet(7)->offsetGet(0)->type()->isWhiteSpace());
        $this->assertTrue($lexemeList->offsetGet(8)->offsetGet(0)->type()->isNotWhiteSpaceOrControl());
        $this->assertTrue($lexemeList->offsetGet(9)->offsetGet(0)->type()->isDot());
        $this->assertTrue($lexemeList->offsetGet(10)->offsetGet(0)->type()->isEndOfLine());
        $this->assertEquals(86, $this->octetCount($lexemeList));

        // test field
        $fieldTest = StringIterator::withLiteral("\x0D\x0A".'MIME-Version: 1.0'."\x0D\x0A");
        $tokenlist = $tokenizer->parse($fieldTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList->offsetGet(0)->hasType(LexemeType::newLine()));
        $this->assertTrue($lexemeList->offsetGet(1)->hasType(LexemeType::fieldHeader()));
        $this->assertTrue($lexemeList->offsetGet(2)->hasType(LexemeType::fieldBody()));
        $this->assertTrue($lexemeList->offsetGet(3)->hasType(LexemeType::newLine()));
        $this->assertEquals(21, $this->octetCount($lexemeList));

        $fieldTest = StringIterator::withLiteral(
            "\x0d\x0A".'MIME-Version: 1.0'."\x0d\x0A"
            .'Subject: Test Email: Annalise Luettgen'."\x0d\x0A"
        );

        $tokenlist = $tokenizer->parse($fieldTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertTrue($lexemeList->offsetGet(0)->hasType(LexemeType::newLine()));
        $this->assertTrue($lexemeList->offsetGet(1)->hasType(LexemeType::fieldHeader()));
        $this->assertTrue($lexemeList->offsetGet(2)->hasType(LexemeType::fieldBody()));
        $this->assertTrue($lexemeList->offsetGet(3)->hasType(LexemeType::newLine()));
        $this->assertTrue($lexemeList->offsetGet(4)->hasType(LexemeType::fieldHeader()));
        $this->assertTrue($lexemeList->offsetGet(5)->hasType(LexemeType::fieldBody()));
        $this->assertTrue($lexemeList->offsetGet(6)->hasType(LexemeType::newLine()));
        $this->assertEquals(61, $this->octetCount($lexemeList));

        $notFieldTest = StringIterator::withLiteral(
           'MIME-Version: 1.0'."\x0d\x0A"
        );
        $tokenlist = $tokenizer->parse($notFieldTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertEquals(19, $this->octetCount($lexemeList));


        $notFieldTest = StringIterator::withLiteral(
            ' Version: 1.0'."\x0d\x0A"
        );
        $tokenlist = $tokenizer->parse($notFieldTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertEquals(15, $this->octetCount($lexemeList));



        $notFieldTest = StringIterator::withLiteral(
            "\x0d\x0A".'a : 1.0'."\x0d\x0A"
        );
        $tokenlist = $tokenizer->parse($notFieldTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertEquals(11, $this->octetCount($lexemeList));

        $notFieldTest = StringIterator::withLiteral(
            "\x0d\x0A".':'
        );
        $tokenlist = $tokenizer->parse($notFieldTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertEquals(3, $this->octetCount($lexemeList));

        $notGroupFieldTest = StringIterator::withLiteral(
            "\x0d\x0A".'X-Mailer: PHPMailer 6.0.5 (https://github.com/PHPMailer/PHPMailer)'."\x0D\x0A"
        );
        $tokenlist = $tokenizer->parse($notGroupFieldTest);
        $lexemeList = $object->parse($tokenlist);
        $this->assertCount(4, $lexemeList);
        $this->assertEquals(70, $this->octetCount($lexemeList));

        // test noise reduction
        $noise = StringIterator::withLiteral('"a"'."\x0D\x0A\x20\x20\x20\x20\x20\x20\x20\x20".'"b"');
        $tokenlist = $tokenizer->parse($noise);
        $lexemeList = $object->parse($tokenlist);
        $this->assertCount(2, $lexemeList);
        $this->assertEquals(16, $this->octetCount($lexemeList));

        $a = "\x0D\x0AContent-Type: multipart/mixed;\x0D\x0A        boundary=\"b1_DJdjz17adcO1Vkuk6O7ioGY6JIiljLQAWVYV3MWgC7s\"\x0D\x0A";
        $stringIterator = StringIterator::withLiteral($a);
        $tokenlist = $tokenizer->parse($stringIterator);
        $lexemeList = $object->parse($tokenlist);
        $this->assertCount(4, $lexemeList);
        $this->assertTrue($lexemeList->offsetGet(0)->hasType(LexemeType::newLine()));
        $this->assertEquals("Content-Type", $lexemeList->offsetGet(1)->toString());
        $this->assertEquals(" multipart/mixed;boundary=\"b1_DJdjz17adcO1Vkuk6O7ioGY6JIiljLQAWVYV3MWgC7s\"", $lexemeList->offsetGet(2)->toString());
        $this->assertTrue($lexemeList->offsetGet(3)->hasType(LexemeType::newLine()));
        $this->assertEquals(101, $this->octetCount($lexemeList));

        // Line Length
        // Lexemizer should strip out the line length tokens
        $justRight = StringIterator::withLiteral(str_repeat("a", 998) . "\x0D\x0A");
        $tokenlist = $tokenizer->parse($justRight);
        $lexemeList = $object->parse($tokenlist);
        $this->assertCount(2, $lexemeList);
        $this->assertCount(1, $lexemeList->offsetGet(0));
        $this->assertEquals(1000, $this->octetCount($lexemeList));

        $justRecommended = StringIterator::withLiteral(str_repeat("a", 78) . "\x0D\x0A");
        $tokenlist = $tokenizer->parse($justRecommended);
        $lexemeList = $object->parse($tokenlist);
        $this->assertCount(2, $lexemeList);
        $this->assertCount(1, $lexemeList->offsetGet(0));
        $this->assertEquals(80, $this->octetCount($lexemeList));

        // test octet count
        $response = StringIterator::withLiteral(file_get_contents(codecept_data_dir().'FETCH_BODY_HEADER.txt'));
        $tokenlist = $tokenizer->parse($response);
        $lexemeList = $object->parse($tokenlist);
        $this->assertEquals(816, $this->octetCount($lexemeList));
    }
}
