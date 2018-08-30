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
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/27/18
 * Time: 12:08 PM
 */

use SalesAgility\Imap\Lexeme\LexemeType;

class LexemeTypeTest extends \Codeception\Test\Unit
{
    /** @var UnitTester */
    protected $tester;

    public function testAllCapitals()
    {
        $object = LexemeType::allCapitals();
        $this->assertTrue($object->isAllCapitals());
    }

    public function testIsAllCapitals()
    {
        $object = LexemeType::allCapitals();
        $this->assertTrue($object->isAllCapitals());

        $object = LexemeType::atom();
        $this->assertFalse($object->isAllCapitals());
    }

    public function testAllNumbers()
    {
        $object = LexemeType::allNumbers();
        $this->assertTrue($object->isAllNumbers());
    }

    public function testIsAllNumbers()
    {
        $object = LexemeType::allNumbers();
        $this->assertTrue($object->isAllNumbers());

        $object = LexemeType::atom();
        $this->assertFalse($object->isAllNumbers());
    }

    public function testCapitalsNumbers()
    {
        $object = LexemeType::allNumbers();
        $this->assertTrue($object->isAllNumbers());
    }

    public function testIsCapitalsNumbers()
    {
        $object = LexemeType::capitalsNumbers();
        $this->assertTrue($object->isCapitalsNumbers());

        $object = LexemeType::atom();
        $this->assertFalse($object->isCapitalsNumbers());
    }

    public function testCtext()
    {
        $object = LexemeType::ctext();
        $this->assertTrue($object->isCtext());
    }

    public function testIsCtext()
    {
        $object = LexemeType::ctext();
        $this->assertTrue($object->isCtext());

        $object = LexemeType::atom();
        $this->assertFalse($object->isCtext());
    }

    public function testCcontent()
    {
        $object = LexemeType::ccontent();
        $this->assertTrue($object->isCcontent());
    }

    public function testIsCcontent()
    {
        $object = LexemeType::ccontent();
        $this->assertTrue($object->isCcontent());

        $object = LexemeType::atom();
        $this->assertFalse($object->isCcontent());
    }


    public function testComment()
    {
        $object = LexemeType::comment();
        $this->assertTrue($object->isComment());
    }

    public function testIsComment()
    {
        $object = LexemeType::comment();
        $this->assertTrue($object->isComment());

        $object = LexemeType::atom();
        $this->assertFalse($object->isComment());
    }

    public function testCfws()
    {
        $object = LexemeType::cfws();
        $this->assertTrue($object->isCfws());
    }

    public function testIsCfws()
    {
        $object = LexemeType::cfws();
        $this->assertTrue($object->isCfws());

        $object = LexemeType::atom();
        $this->assertFalse($object->isCfws());
    }

    public function testAtext()
    {
        $object = LexemeType::atext();
        $this->assertTrue($object->isAtext());
    }

    public function testIsAtext()
    {
        $object = LexemeType::atext();
        $this->assertTrue($object->isAtext());

        $object = LexemeType::atom();
        $this->assertFalse($object->isAtext());
    }


    public function testAtom()
    {
        $object = LexemeType::atom();
        $this->assertTrue($object->isAtom());
    }

    public function testisAtom()
    {
        $object = LexemeType::atom();
        $this->assertTrue($object->isAtom());

        $object = LexemeType::comment();
        $this->assertFalse($object->isAtom());
    }

    public function testDotAtomText()
    {
        $object = LexemeType::dotAtomText();
        $this->assertTrue($object->isDotAtomText());
    }

    public function testIsDotAtomText()
    {
        $object = LexemeType::dotAtomText();
        $this->assertTrue($object->isDotAtomText());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDotAtomText());
    }

    public function testQtext()
    {
        $object = LexemeType::qtext();
        $this->assertTrue($object->isQtext());
    }

    public function testIsQtext()
    {
        $object = LexemeType::qtext();
        $this->assertTrue($object->isQtext());

        $object = LexemeType::atom();
        $this->assertFalse($object->isQtext());
    }

    public function testQcontent()
    {
        $object = LexemeType::qcontent();
        $this->assertTrue($object->isQcontent());
    }

    public function testIsQContent()
    {
        $object = LexemeType::qcontent();
        $this->assertTrue($object->isQcontent());

        $object = LexemeType::atom();
        $this->assertFalse($object->isQcontent());
    }


    public function testQuotedString()
    {
        $object = LexemeType::quotedString();
        $this->assertTrue($object->isQuotedString());
    }

    public function testIsQuotedString()
    {
        $object = LexemeType::quotedString();
        $this->assertTrue($object->isQuotedString());

        $object = LexemeType::atom();
        $this->assertFalse($object->isQuotedString());
    }


    public function testWord()
    {
        $object = LexemeType::word();
        $this->assertTrue($object->isWord());
    }

    public function testIsWord()
    {
        $object = LexemeType::word();
        $this->assertTrue($object->isWord());

        $object = LexemeType::atom();
        $this->assertFalse($object->isWord());
    }

    public function testPhrase()
    {
        $object = LexemeType::phrase();
        $this->assertTrue($object->isPhrase());
    }

    public function testIsPhrase()
    {
        $object = LexemeType::phrase();
        $this->assertTrue($object->isPhrase());

        $object = LexemeType::atom();
        $this->assertFalse($object->isPhrase());
    }

    public function testUtext()
    {
        $object = LexemeType::utext();
        $this->assertTrue($object->isUtext());
    }

    public function testIsUtext()
    {
        $object = LexemeType::utext();
        $this->assertTrue($object->isUtext());

        $object = LexemeType::atom();
        $this->assertFalse($object->isUtext());
    }

    public function testDateTime()
    {
        $object = LexemeType::dateTime();
        $this->assertTrue($object->isDateTime());
    }

    public function testIsDateTime()
    {
        $object = LexemeType::dateTime();
        $this->assertTrue($object->isDateTime());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDateTime());
    }

    public function testDayOfWeek()
    {
        $object = LexemeType::dayOfWeek();
        $this->assertTrue($object->isDayOfWeek());
    }

    public function testIsDayOfWeek()
    {
        $object = LexemeType::dayOfWeek();
        $this->assertTrue($object->isDayOfWeek());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDayOfWeek());
    }

    public function testDayName()
    {
        $object = LexemeType::dayName();
        $this->assertTrue($object->isDayName());
    }

    public function testIsDayName()
    {
        $object = LexemeType::dayName();
        $this->assertTrue($object->isDayName());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDayName());
    }

    public function testDate()
    {
        $object = LexemeType::date();
        $this->assertTrue($object->isDate());
    }

    public function testIsDate()
    {
        $object = LexemeType::date();
        $this->assertTrue($object->isDate());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDate());
    }

    public function testYear()
    {
        $object = LexemeType::year();
        $this->assertTrue($object->isYear());
    }

    public function testIsYear()
    {
        $object = LexemeType::year();
        $this->assertTrue($object->isYear());

        $object = LexemeType::atom();
        $this->assertFalse($object->isYear());
    }


    public function testMonth()
    {
        $object = LexemeType::month();
        $this->assertTrue($object->isMonth());
    }

    public function testIsMonth()
    {
        $object = LexemeType::month();
        $this->assertTrue($object->isMonth());

        $object = LexemeType::atom();
        $this->assertFalse($object->isMonth());
    }

    public function testMonthName()
    {
        $object = LexemeType::monthName();
        $this->assertTrue($object->isMonthName());
    }

    public function testIsMonthName()
    {
        $object = LexemeType::monthName();
        $this->assertTrue($object->isMonthName());

        $object = LexemeType::atom();
        $this->assertFalse($object->isMonthName());
    }

    public function testDay()
    {
        $object = LexemeType::day();
        $this->assertTrue($object->isDay());
    }

    public function testIsDay()
    {
        $object = LexemeType::day();
        $this->assertTrue($object->isDay());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDay());
    }


    public function testTime()
    {
        $object = LexemeType::time();
        $this->assertTrue($object->isTime());
    }

    public function testIsTime()
    {
        $object = LexemeType::time();
        $this->assertTrue($object->isTime());

        $object = LexemeType::atom();
        $this->assertFalse($object->isTime());
    }

    public function testTimeOfDay()
    {
        $object = LexemeType::timeOfDay();
        $this->assertTrue($object->isTimeOfDay());
    }

    public function testIsTimeOfDat()
    {
        $object = LexemeType::timeOfDay();
        $this->assertTrue($object->isTimeOfDay());

        $object = LexemeType::atom();
        $this->assertFalse($object->isTimeOfDay());
    }

    public function testHour()
    {
        $object = LexemeType::hour();
        $this->assertTrue($object->isHour());
    }

    public function testIsHour()
    {
        $object = LexemeType::hour();
        $this->assertTrue($object->isHour());

        $object = LexemeType::atom();
        $this->assertFalse($object->isHour());
    }


    public function testMinute()
    {
        $object = LexemeType::minute();
        $this->assertTrue($object->isMinute());
    }

    public function testIsMinute()
    {
        $object = LexemeType::minute();
        $this->assertTrue($object->isMinute());

        $object = LexemeType::atom();
        $this->assertFalse($object->isMinute());
    }

    public function testSecond()
    {
        $object = LexemeType::second();
        $this->assertTrue($object->isSecond());
    }

    public function testIsSecond()
    {
        $object = LexemeType::second();
        $this->assertTrue($object->isSecond());

        $object = LexemeType::atom();
        $this->assertFalse($object->isSecond());
    }

    public function testZone()
    {
        $object = LexemeType::zone();
        $this->assertTrue($object->isZone());
    }

    public function testIsZone()
    {
        $object = LexemeType::zone();
        $this->assertTrue($object->isZone());

        $object = LexemeType::atom();
        $this->assertFalse($object->isZone());
    }

    public function testAddress()
    {
        $object = LexemeType::address();
        $this->assertTrue($object->isAddress());
    }

    public function testIsAddress()
    {
        $object = LexemeType::address();
        $this->assertTrue($object->isAddress());

        $object = LexemeType::atom();
        $this->assertFalse($object->isAddress());
    }

    public function testMailbox()
    {
        $object = LexemeType::mailbox();
        $this->assertTrue($object->isMailbox());
    }

    public function testIsMailbox()
    {
        $object = LexemeType::mailbox();
        $this->assertTrue($object->isMailbox());

        $object = LexemeType::atom();
        $this->assertFalse($object->isMailbox());
    }

    public function testNameAddress()
    {
        $object = LexemeType::nameAddress();
        $this->assertTrue($object->isNameAddress());
    }

    public function testIsNameAddress()
    {
        $object = LexemeType::nameAddress();
        $this->assertTrue($object->isNameAddress());

        $object = LexemeType::atom();
        $this->assertFalse($object->isNameAddress());
    }

    public function testAngleAddressPair()
    {
        $object = LexemeType::angleAddressPair();
        $this->assertTrue($object->isAngleAddressPair());
    }

    public function testIsAngleAddressPair()
    {
        $object = LexemeType::angleAddressPair();
        $this->assertTrue($object->isAngleAddressPair());

        $object = LexemeType::atom();
        $this->assertFalse($object->isAngleAddressPair());
    }

    public function testDisplayName()
    {
        $object = LexemeType::displayName();
        $this->assertTrue($object->isDisplayName());
    }

    public function testIsDisplayName()
    {
        $object = LexemeType::displayName();
        $this->assertTrue($object->isDisplayName());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDisplayName());
    }

    public function testMailboxList()
    {
        $object = LexemeType::mailboxList();
        $this->assertTrue($object->isMailboxList());
    }

    public function testIsMailBoxList()
    {
        $object = LexemeType::mailboxList();
        $this->assertTrue($object->isMailboxList());

        $object = LexemeType::atom();
        $this->assertFalse($object->isMailboxList());
    }


    public function testGroupAddress()
    {
        $object = LexemeType::groupAddress();
        $this->assertTrue($object->isGroupAddress());
    }

    public function testIsGroupAddress()
    {
        $object = LexemeType::groupAddress();
        $this->assertTrue($object->isGroupAddress());

        $object = LexemeType::atom();
        $this->assertFalse($object->isGroupAddress());
    }

    public function testAddressList()
    {
        $object = LexemeType::addressList();
        $this->assertTrue($object->isAddressList());
    }

    public function testIsAddressList()
    {
        $object = LexemeType::addressList();
        $this->assertTrue($object->isAddressList());

        $object = LexemeType::atom();
        $this->assertFalse($object->isAddressList());
    }

    public function testAddressSpec()
    {
        $object = LexemeType::addressSpec();
        $this->assertTrue($object->isAddressSpec());
    }

    public function testIsAddressSpec()
    {
        $object = LexemeType::addressSpec();
        $this->assertTrue($object->isAddressSpec());

        $object = LexemeType::atom();
        $this->assertFalse($object->isAddressSpec());
    }

    public function testLocalPart()
    {
        $object = LexemeType::localPart();
        $this->assertTrue($object->isLocalPart());
    }

    public function testIsLocalPart()
    {
        $object = LexemeType::localPart();
        $this->assertTrue($object->isLocalPart());

        $object = LexemeType::atom();
        $this->assertFalse($object->isLocalPart());
    }

    public function testDomain()
    {
        $object = LexemeType::domain();
        $this->assertTrue($object->isDomain());
    }

    public function testIsDomain()
    {
        $object = LexemeType::domain();
        $this->assertTrue($object->isDomain());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDomain());
    }

    public function testDomainLiteral()
    {
        $object = LexemeType::domainLiteral();
        $this->assertTrue($object->isDomainLiteral());
    }

    public function testIsDomainLiteral()
    {
        $object = LexemeType::domainLiteral();
        $this->assertTrue($object->isDomainLiteral());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDomainLiteral());
    }

    public function testDcontent()
    {
        $object = LexemeType::dcontent();
        $this->assertTrue($object->isDcontent());
    }

    public function testIsDContent()
    {
        $object = LexemeType::dcontent();
        $this->assertTrue($object->isDcontent());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDcontent());
    }

    public function testDtext()
    {
        $object = LexemeType::dtext();
        $this->assertTrue($object->isDtext());
    }

    public function testIsDtext()
    {
        $object = LexemeType::dtext();
        $this->assertTrue($object->isDtext());

        $object = LexemeType::atom();
        $this->assertFalse($object->isDtext());
    }

    public function testMessage()
    {
        $object = LexemeType::message();
        $this->assertTrue($object->isMessage());
    }

    public function testIsMessage()
    {
        $object = LexemeType::message();
        $this->assertTrue($object->isMessage());

        $object = LexemeType::atom();
        $this->assertFalse($object->isMessage());
    }

    public function testField()
    {
        $object = LexemeType::fieldHeader();
        $this->assertTrue($object->isFieldHeader());
    }

    public function testIsFieldHeader()
    {
        $object = LexemeType::fieldHeader();
        $this->assertTrue($object->isFieldHeader());

        $object = LexemeType::atom();
        $this->assertFalse($object->isFieldHeader());
    }

    public function testFieldHeader()
    {
        $object = LexemeType::fieldHeader();
        $this->assertTrue($object->isFieldHeader());
    }

    public function testIsFieldBody()
    {
        $object = LexemeType::fieldBody();
        $this->assertTrue($object->isFieldBody());

        $object = LexemeType::atom();
        $this->assertFalse($object->isFieldBody());
    }

    public function testSystemFlag()
    {
        $object = LexemeType::systemFlag();
        $this->assertTrue($object->isSystemFlag());
    }

    public function testIsSystemFlag()
    {

        $object = LexemeType::systemFlag();
        $this->assertTrue($object->isSystemFlag());

        $object = LexemeType::atom();
        $this->assertFalse($object->isFlagClass());
    }

    public function testFlagClass()
    {
        $object = LexemeType::flagClass();
        $this->assertTrue($object->isFlagClass());
    }

    public function testIsFlagClass()
    {
        $object = LexemeType::flagClass();
        $this->assertTrue($object->isFlagClass());

        $object = LexemeType::atom();
        $this->assertFalse($object->isFlagClass());
    }

    public function testFlagType()
    {
        $object = LexemeType::flagType();
        $this->assertTrue($object->isFlagType());
    }

    public function testIsFlatType()
    {
        $object = LexemeType::flagType();
        $this->assertTrue($object->isFlagType());

        $object = LexemeType::atom();
        $this->assertFalse($object->isFlagType());
    }

    public function testSystemResponse()
    {
        $object = LexemeType::systemResponse();
        $this->assertTrue($object->isSystemResponse());
    }

    public function testIsSystemResponse()
    {
        $object = LexemeType::systemResponse();
        $this->assertTrue($object->isSystemResponse());

        $object = LexemeType::atom();
        $this->assertFalse($object->isSystemResponse());
    }

    public function testCommand()
    {
        $object = LexemeType::command();
        $this->assertTrue($object->isCommand());
    }

    public function testIsCommand()
    {
        $object = LexemeType::command();
        $this->assertTrue($object->isCommand());

        $object = LexemeType::atom();
        $this->assertFalse($object->isCommand());
    }

    public function testSystemCode()
    {
        $object = LexemeType::systemCode();
        $this->assertTrue($object->isSystemCode());
    }

    public function testIsSystemCode()
    {
        $object = LexemeType::systemCode();
        $this->assertTrue($object->isSystemCode());

        $object = LexemeType::atom();
        $this->assertFalse($object->isSystemCode());
    }

    public function testAttribute()
    {
        $object = LexemeType::attribute();
        $this->assertTrue($object->isAttribute());
    }

    public function testIsAttribute()
    {
        $object = LexemeType::attribute();
        $this->assertTrue($object->isAttribute());

        $object = LexemeType::atom();
        $this->assertFalse($object->isAttribute());
    }

    public function testMailboxNameAttribute()
    {
        $object = LexemeType::mailboxNameAttribute();
        $this->assertTrue($object->isMailboxNameAttribute());
    }

    public function testIsMailboxNameAttribute()
    {
        $object = LexemeType::mailboxNameAttribute();
        $this->assertTrue($object->isMailboxNameAttribute());

        $object = LexemeType::atom();
        $this->assertFalse($object->isMailboxNameAttribute());
    }


    public function testSpecificationRequirementTerm()
    {
        $object = LexemeType::specificationRequirementTerm();
        $this->assertTrue($object->isSpecificationRequirementTerm());
    }

    public function testIsSpecificationRequirementTerm()
    {
        $object = LexemeType::specificationRequirementTerm();
        $this->assertTrue($object->isSpecificationRequirementTerm());

        $object = LexemeType::atom();
        $this->assertFalse($object->isSpecificationRequirementTerm());
    }

    public function testMessageField()
    {
        $object = LexemeType::messageField();
        $this->assertTrue($object->isMessageField());
    }

    public function testIsMessageField()
    {
        $object = LexemeType::messageField();
        $this->assertTrue($object->isMessageField());

        $object = LexemeType::atom();
        $this->assertFalse($object->isMessageField());
    }

    public function testPartSpecifier()
    {
        $object = LexemeType::partSpecifier();
        $this->assertTrue($object->isPartSpecifier());
    }

    public function testIsPartSpecifier()
    {
        $object = LexemeType::partSpecifier();
        $this->assertTrue($object->isPartSpecifier());

        $object = LexemeType::atom();
        $this->assertFalse($object->isMessageField());
    }

    public function testStatusItem()
    {
        $object = LexemeType::statusItem();
        $this->assertTrue($object->isStatusItem());
    }

    public function testIsStatusItem()
    {
        $object = LexemeType::statusItem();
        $this->assertTrue($object->isStatusItem());

        $object = LexemeType::atom();
        $this->assertFalse($object->isStatusItem());
    }

    public function testGroup()
    {
        $object = LexemeType::group();
        $this->assertTrue($object->isGroup());
    }

    public function testIsGroup()
    {
        $object = LexemeType::group();
        $this->assertTrue($object->isGroup());

        $object = LexemeType::atom();
        $this->assertFalse($object->isGroup());
    }

    public function testOptional()
    {
        $object = LexemeType::optional();
        $this->assertTrue($object->isOptional());
    }

    public function testIsOptional()
    {
        $object = LexemeType::optional();
        $this->assertTrue($object->isOptional());

        $object = LexemeType::atom();
        $this->assertFalse($object->isOptional());
    }


    public function testNewLine()
    {
        $object = LexemeType::newLine();
        $this->assertTrue($object->isNewLine());
    }

    public function testIsNewLine()
    {
        $object = LexemeType::newLine();
        $this->assertTrue($object->isNewLine());

        $object = LexemeType::atom();
        $this->assertFalse($object->isNewLine());
    }

    public function testWhitespace()
    {
        $object = LexemeType::whitespace();
        $this->assertTrue($object->isWhitespace());
    }

    public function testIsWhitespace()
    {
        $object = LexemeType::whitespace();
        $this->assertTrue($object->isWhitespace());

        $object = LexemeType::atom();
        $this->assertFalse($object->isWhitespace());
    }
}
