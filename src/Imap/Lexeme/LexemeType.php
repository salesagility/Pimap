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


namespace SalesAgility\Imap\Lexeme;


/**
 * Class LexemeType
 * @package SalesAgility\Imap\Lexeme
 * Higher level tokens
 */
class LexemeType
{
    const ALL_CAPITAL_LETTERS = 1001;
    const ALL_NUMBERS = 1002;
    const CAPITAL_LETTERS_NUMBERS = 1003;
    const CTEXT = 1004;
    const CCONTENT = 1005;
    const COMMENT = 1006;
    const CFWS = 1007;
    const ATEXT = 1008;
    const ATOM = 1009;
    const DOT_ATOM_TEXT = 10010;
    const DOT_ATOM = 10011;
    const QTEXT = 10012;
    const QCONTENT = 10013;
    const QUOTED_STRING = 10014;
    const WORD = 10015;
    const PHRASE = 10016;
    const UTEXT = 10017;
    const DATE_TIME = 10018;
    const DAY_OF_WEEK = 10019;
    const DAY_NAME = 10020;
    const DATE = 10021;
    const YEAR = 10022;
    const MONTH = 10023;
    const MONTH_NAME = 10024;
    const DAY = 10025;
    const TIME = 10026;
    const TIME_OF_DAY = 10027;
    const HOUR = 10028;
    const MINUTE = 10029;
    const SECOND = 10030;
    const ZONE = 10031;
    const ADDRESS = 10032;
    const MAILBOX = 10033;
    const NAME_ADDR = 10034;
    const ANGLE_ADDR = 10035;
    const GROUP_ADDR = 10036;
    const DISPLAY_NAME = 10037;
    const MAILBOX_LIST = 10038;
    const ADDRESS_LIST = 10040;
    const ADDR_SPEC = 10041;
    const LOCAL_PART = 10042;
    const DOMAIN = 10043;
    const DOMAIN_LITERAL = 10044;
    const DCONTENT = 10045;
    const DTEXT = 10046;
    const MESSAGE = 10047;
    const BODY = 10048;
    const FIELD_HEADER = 10049;
    const FIELD_BODY = 10050;
    const SYSTEM_FLAG = 10051;
    const FLAG_CLASS = 10052;
    const FLAG_TYPE = 10053;
    const SYSTEM_RESPONSE = 10054;
    const COMMAND = 10055;
    const SYSTEM_CODE = 10056;
    const RESPONSE = 10057;
    const ATTRIBUTE = 10058;
    const MAILBOX_NAME_ATTRIBUTE = 10059;
    const SPECIFICATION_REQUIREMENT_TERM = 10060;
    const MESSAGE_FIELD = 10061;
    const PART_SPECIFIER = 10062;
    const STATUS_ITEM = 10063;
    const OPTIONAL = 10064;
    const GROUP = 10065;
    const NEWLINE = 10066;
    const WHITESPACE = 10067;

    private $type;

    /**
     *  LexemeType Constructor
     * @param int type
     *
     */
    private function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @param LexemeType $type
     * @return bool
     */
    public function isType(LexemeType $type)
    {
        return $type->type === $this->type;
    }

    /**
     * @return LexemeType
     */
    public static function allCapitals()
    {
        return new self(self::ALL_CAPITAL_LETTERS);
    }

    /**
     * @return bool
     */
    public function isAllCapitals()
    {
        return $this->type === self::ALL_CAPITAL_LETTERS;
    }

    /**
     * @return LexemeType
     */
    public static function allNumbers()
    {
        return new self(self::ALL_NUMBERS);
    }

    /**
     * @return bool
     */
    public function isAllNumbers()
    {
        return $this->type === self::ALL_NUMBERS;
    }

    /**
     * @return LexemeType
     */
    public static function capitalsNumbers()
    {
        return new self(self::CAPITAL_LETTERS_NUMBERS);
    }

    /**
     * @return bool
     */
    public function isCapitalsNumbers()
    {
        return $this->type === self::CAPITAL_LETTERS_NUMBERS;
    }

    /**
     * @return LexemeType
     */
    public static function ctext()
    {
        return new self(self::CTEXT);
    }

    /**
     * @return bool
     */
    public function isCtext()
    {
        return $this->type === self::CTEXT;
    }

    /**
     * @return LexemeType
     */
    public static function ccontent()
    {
        return new self(self::CCONTENT);
    }

    /**
     * @return bool
     */
    public function isCcontent()
    {
        return $this->type === self::CCONTENT;
    }

    /**
     * @return LexemeType
     */
    public static function comment()
    {
        return new self(self::COMMENT);
    }

    /**
     * @return bool
     */
    public function isComment()
    {
        return $this->type === self::COMMENT;
    }

    /**
     * @return LexemeType
     */
    public static function cfws()
    {
        return new self(self::CFWS);
    }

    /**
     * @return bool
     */
    public function isCfws()
    {
        return $this->type === self::CFWS;
    }

    /**
     * @return LexemeType
     */
    public static function atext()
    {
        return new self(self::ATEXT);
    }

    /**
     * @return bool
     */
    public function isAtext()
    {
        return $this->type === self::ATEXT;
    }

    /**
     * @return LexemeType
     */
    public static function atom()
    {
        return new self(self::ATOM);
    }

    /**
     * @return bool
     */
    public function isAtom()
    {
        return $this->type === self::ATOM;
    }

    /**
     * @return LexemeType
     */
    public static function dotAtomText()
    {
        return new self(self::DOT_ATOM_TEXT);
    }

    /**
     * @return bool
     */
    public function isDotAtomText()
    {
        return $this->type === self::DOT_ATOM_TEXT;
    }

    /**
     * @return LexemeType
     */
    public static function qtext()
    {
        return new self(self::QTEXT);
    }

    /**
     * @return bool
     */
    public function isQtext()
    {
        return $this->type === self::QTEXT;
    }

    /**
     * @return LexemeType
     */
    public static function qcontent()
    {
        return new self(self::QCONTENT);
    }

    /**
     * @return bool
     */
    public function isQcontent()
    {
        return $this->type === self::QCONTENT;
    }

    /**
     * @return LexemeType
     */
    public static function quotedString()
    {
        return new self(self::QUOTED_STRING);
    }

    /**
     * @return bool
     */
    public function isQuotedString()
    {
        return $this->type === self::QUOTED_STRING;
    }

    /**
     * @return LexemeType
     */
    public static function word()
    {
        return new self(self::WORD);
    }

    /**
     * @return bool
     */
    public function isWord()
    {
        return $this->type === self::WORD;
    }

    /**
     * @return LexemeType
     */
    public static function phrase()
    {
        return new self(self::PHRASE);
    }

    /**
     * @return bool
     */
    public function isPhrase()
    {
        return $this->type === self::PHRASE;
    }

    /**
     * @return LexemeType
     */
    public static function utext()
    {
        return new self(self::UTEXT);
    }

    /**
     * @return bool
     */
    public function isUtext()
    {
        return $this->type === self::UTEXT;
    }

    /**
     * @return LexemeType
     */
    public static function dateTime()
    {
        return new self(self::DATE_TIME);
    }

    /**
     * @return bool
     */
    public function isDateTime()
    {
        return $this->type === self::DATE_TIME;
    }

    /**
     * @return LexemeType
     */
    public static function dayOfWeek()
    {
        return new self(self::DAY_OF_WEEK);
    }

    /**
     * @return bool
     */
    public function isDayOfWeek()
    {
        return $this->type === self::DAY_OF_WEEK;
    }

    /**
     * @return LexemeType
     */
    public static function dayName()
    {
        return new self(self::DAY_NAME);
    }

    /**
     * @return bool
     */
    public function isDayName()
    {
        return $this->type === self::DAY_NAME;
    }

    /**
     * @return LexemeType
     */
    public static function date()
    {
        return new self(self::DATE);
    }

    /**
     * @return bool
     */
    public function isDate()
    {
        return $this->type === self::DATE;
    }

    /**
     * @return LexemeType
     */
    public static function year()
    {
        return new self(self::YEAR);
    }

    /**
     * @return bool
     */
    public function isYear()
    {
        return $this->type === self::YEAR;
    }

    /**
     * @return LexemeType
     */
    public static function month()
    {
        return new self(self::MONTH);
    }

    /**
     * @return bool
     */
    public function isMonth()
    {
        return $this->type === self::MONTH;
    }

    /**
     * @return LexemeType
     */
    public static function monthName()
    {
        return new self(self::MONTH_NAME);
    }

    /**
     * @return bool
     */
    public function isMonthName()
    {
        return $this->type === self::MONTH_NAME;
    }

    /**
     * @return LexemeType
     */
    public static function day()
    {
        return new self(self::DAY);
    }

    /**
     * @return bool
     */
    public function isDay()
    {
        return $this->type === self::DAY;
    }

    /**
     * @return LexemeType
     */
    public static function time()
    {
        return new self(self::TIME);
    }

    /**
     * @return bool
     */
    public function isTime()
    {
        return $this->type === self::TIME;
    }

    /**
     * @return LexemeType
     */
    public static function timeOfDay()
    {
        return new self(self::TIME_OF_DAY);
    }

    /**
     * @return bool
     */
    public function isTimeOfDay()
    {
        return $this->type === self::TIME_OF_DAY;
    }

    /**
     * @return LexemeType
     */
    public static function hour()
    {
        return new self(self::HOUR);
    }

    /**
     * @return bool
     */
    public function isHour()
    {
        return $this->type === self::HOUR;
    }

    /**
     * @return LexemeType
     */
    public static function minute()
    {
        return new self(self::MINUTE);
    }

    /**
     * @return bool
     */
    public function isMinute()
    {
        return $this->type === self::MINUTE;
    }

    /**
     * @return LexemeType
     */
    public static function second()
    {
        return new self(self::SECOND);
    }

    /**
     * @return bool
     */
    public function isSecond()
    {
        return $this->type === self::SECOND;
    }

    /**
     * @return LexemeType
     */
    public static function zone()
    {
        return new self(self::ZONE);
    }

    /**
     * @return bool
     */
    public function isZone()
    {
        return $this->type === self::ZONE;
    }

    /**
     * @return LexemeType
     */
    public static function address()
    {
        return new self(self::ADDRESS);
    }

    /**
     * @return bool
     */
    public function isAddress()
    {
        return $this->type === self::ADDRESS;
    }

    /**
     * @return LexemeType
     */
    public static function mailbox()
    {
        return new self(self::MAILBOX);
    }

    /**
     * @return bool
     */
    public function isMailbox()
    {
        return $this->type === self::MAILBOX;
    }

    /**
     * @return LexemeType
     */
    public static function nameAddress()
    {
        return new self(self::NAME_ADDR);
    }

    /**
     * @return bool
     */
    public function isNameAddress()
    {
        return $this->type === self::NAME_ADDR;
    }

    /**
     * @return LexemeType
     */
    public static function angleAddressPair()
    {
        return new self(self::ANGLE_ADDR);
    }

    /**
     * @return bool
     */
    public function isAngleAddressPair()
    {
        return $this->type === self::ANGLE_ADDR;
    }

    /**
     * @return LexemeType
     */
    public static function groupAddress()
    {
        return new self(self::GROUP_ADDR);
    }

    /**
     * @return bool
     */
    public function isGroupAddress()
    {
        return $this->type === self::GROUP_ADDR;
    }

    /**
     * @return LexemeType
     */
    public static function displayName()
    {
        return new self(self::DISPLAY_NAME);
    }

    /**
     * @return bool
     */
    public function isDisplayName()
    {
        return $this->type === self::DISPLAY_NAME;
    }

    /**
     * @return LexemeType
     */
    public static function mailboxList()
    {
        return new self(self::MAILBOX_LIST);
    }

    /**
     * @return bool
     */
    public function isMailboxList()
    {
        return $this->type === self::MAILBOX_LIST;
    }

    /**
     * @return LexemeType
     */
    public static function addressList()
    {
        return new self(self::ADDRESS_LIST);
    }

    /**
     * @return bool
     */
    public function isAddressList()
    {
        return $this->type === self::ADDRESS_LIST;
    }

    /**
     * @return LexemeType
     */
    public static function addressSpec()
    {
        return new self(self::ADDR_SPEC);
    }

    /**
     * @return bool
     */
    public function isAddressSpec()
    {
        return $this->type === self::ADDR_SPEC;
    }

    /**
     * @return LexemeType
     */
    public static function localPart()
    {
        return new self(self::LOCAL_PART);
    }

    /**
     * @return bool
     */
    public function isLocalPart()
    {
        return $this->type === self::LOCAL_PART;
    }

    /**
     * @return LexemeType
     */
    public static function domain()
    {
        return new self(self::DOMAIN);
    }

    /**
     * @return bool
     */
    public function isDomain()
    {
        return $this->type === self::DOMAIN;
    }

    /**
     * @return LexemeType
     */
    public static function domainLiteral()
    {
        return new self(self::DOMAIN_LITERAL);
    }

    /**
     * @return bool
     */
    public function isDomainLiteral()
    {
        return $this->type === self::DOMAIN_LITERAL;
    }

    /**
     * @return LexemeType
     */
    public static function dcontent()
    {
        return new self(self::DCONTENT);
    }

    /**
     * @return bool
     */
    public function isDcontent()
    {
        return $this->type === self::DCONTENT;
    }

    /**
     * @return LexemeType
     */
    public static function dtext()
    {
        return new self(self::DTEXT);
    }

    /**
     * @return bool
     */
    public function isDtext()
    {
        return $this->type === self::DTEXT;
    }

    /**
     * @return LexemeType
     */
    public static function message()
    {
        return new self(self::MESSAGE);
    }

    /**
     * @return bool
     */
    public function isMessage()
    {
        return $this->type === self::MESSAGE;
    }

    /**
     * @return LexemeType
     */
    public static function fieldHeader()
    {
        return new self(self::FIELD_HEADER);
    }

    /**
     * @return bool
     */
    public function isFieldHeader()
    {
        return $this->type === self::FIELD_HEADER;
    }

    /**
     * @return LexemeType
     */
    public static function fieldBody()
    {
        return new self(self::FIELD_BODY);
    }

    /**
     * @return bool
     */
    public function isFieldBody()
    {
        return $this->type === self::FIELD_BODY;
    }

    /**
     * @return LexemeType
     */
    public static function systemFlag()
    {
        return new self(self::SYSTEM_FLAG);
    }

    /**
     * @return bool
     */
    public function isSystemFlag()
    {
        return $this->type === self::SYSTEM_FLAG;
    }

    /**
     * @return LexemeType
     */
    public static function flagClass()
    {
        return new self(self::FLAG_CLASS);
    }

    /**
     * @return bool
     */
    public function isFlagClass()
    {
        return $this->type === self::FLAG_CLASS;
    }

    /**
     * @return LexemeType
     */
    public static function flagType()
    {
        return new self(self::FLAG_TYPE);
    }

    /**
     * @return bool
     */
    public function isFlagType()
    {
        return $this->type === self::FLAG_TYPE;
    }

    /**
     * @return LexemeType
     */
    public static function systemResponse()
    {
        return new self(self::SYSTEM_RESPONSE);
    }

    /**
     * @return bool
     */
    public function isSystemResponse()
    {
        return $this->type === self::SYSTEM_RESPONSE;
    }

    /**
     * @return LexemeType
     */
    public static function command()
    {
        return new self(self::COMMAND);
    }

    /**
     * @return bool
     */
    public function isCommand()
    {
        return $this->type === self::COMMAND;
    }

    /**
     * @return LexemeType
     */
    public static function systemCode()
    {
        return new self(self::SYSTEM_CODE);
    }

    /**
     * @return bool
     */
    public function isSystemCode()
    {
        return $this->type === self::SYSTEM_CODE;
    }

    /**
     * @return LexemeType
     */
    public static function attribute()
    {
        return new self(self::ATTRIBUTE);
    }

    /**
     * @return bool
     */
    public function isAttribute()
    {
        return $this->type === self::ATTRIBUTE;
    }

    /**
     * @return LexemeType
     */
    public static function mailboxNameAttribute()
    {
        return new self(self::MAILBOX_NAME_ATTRIBUTE);
    }

    /**
     * @return bool
     */
    public function isMailboxNameAttribute()
    {
        return $this->type === self::MAILBOX_NAME_ATTRIBUTE;
    }

    /**
     * @return LexemeType
     */
    public static function specificationRequirementTerm()
    {
        return new self(self::SPECIFICATION_REQUIREMENT_TERM);
    }

    /**
     * @return bool
     */
    public function isSpecificationRequirementTerm()
    {
        return $this->type === self::SPECIFICATION_REQUIREMENT_TERM;
    }

    /**
     * @return LexemeType
     */
    public static function messageField()
    {
        return new self(self::MESSAGE_FIELD);
    }

    /**
     * @return bool
     */
    public function isMessageField()
    {
        return $this->type === self::MESSAGE_FIELD;
    }

    /**
     * @return LexemeType
     */
    public static function partSpecifier()
    {
        return new self(self::PART_SPECIFIER);
    }

    /**
     * @return bool
     */
    public function isPartSpecifier()
    {
        return $this->type === self::PART_SPECIFIER;
    }

    /**
     * @return LexemeType
     */
    public static function statusItem()
    {
        return new self(self::STATUS_ITEM);
    }

    /**
     * @return bool
     */
    public function isStatusItem()
    {
        return $this->type === self::STATUS_ITEM;
    }

    /**
     * @return LexemeType
     */
    public static function group()
    {
        return new self(self::GROUP);
    }

    /**
     * @return bool
     */
    public function isGroup()
    {
        return $this->type === self::GROUP;
    }

    /**
     * @return LexemeType
     */
    public static function optional()
    {
        return new self(self::OPTIONAL);
    }

    /**
     * @return bool
     */
    public function isOptional()
    {
        return $this->type === self::OPTIONAL;
    }

    /**
     * @return LexemeType
     */
    public static function newLine()
    {
        return new self(self::NEWLINE);
    }

    /**
     * @return bool
     */
    public function isNewLine()
    {
        return $this->type === self::NEWLINE;
    }

    /**
     * @return LexemeType
     */
    public static function whitespace()
    {
        return new self(self::WHITESPACE);
    }

    /**
     * @return bool
     */
    public function isWhitespace()
    {
        return $this->type === self::WHITESPACE;
    }
    // STATUS ITEM
}
