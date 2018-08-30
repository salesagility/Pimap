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


namespace SalesAgility\Imap\Token;

/**
 * Class TokenType
 * @package SalesAgility\Imap\Token
 * @see https://www.ietf.org/rfc/rfc2822.txt
 * Primitive Tokens Types
 */
class TokenType
{
    // Token Indicator for parsing purposes only
    const RECOMMENDED_LINE_SIZE = -100;
    const REQUIRED_LINE_SIZE = -101;

    // Types
    const FWSP = 1;
    const NO_WS_CTL = 2;
    const SPECIAL = 3;
    const TEXT = 4;
    const QUOTED_PAIR = 5;
    const EOL = 6;
    const CTL = 7;
    const GROUP = 8;
    const OPTIONAL = 9;
    const ANGLE_ADDR = 10;
    const AT = 11;
    const DOT = 12;
    const LIST_SEPARATOR = 13;
    const WSP = 14;
    const PAIRED = 15;

    private $type;

    /**
     * TokenType constructor.
     * @param $type
     */
    private function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isFoldingWhiteSpace()
    {
        return self::FWSP === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function foldingWhiteSpace()
    {
        return new self(self::FWSP);
    }

    /**
     * @return bool
     */
    public function isEndOfLine()
    {
        return self::EOL === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function endOfLine()
    {
        return new self(self::EOL);
    }

    /**
     * @return bool
     */
    public function isGroup()
    {
        return self::GROUP === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function group()
    {
        return new self(self::GROUP);
    }

    /**
     * @return bool
     */
    public function isNonFoldedLiteral()
    {
        return self::OPTIONAL === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function nonFoldedLiteral()
    {
        return new self(self::OPTIONAL);
    }

    /**
     * @return bool
     */
    public function isControlCharacter()
    {
        return self::CTL === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function controlCharacter()
    {
        return new self(self::CTL);
    }

    /**
     * @return bool
     */
    public function isQuoted()
    {
        return self::QUOTED_PAIR === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function quoted()
    {
        return new self(self::QUOTED_PAIR);
    }

    /**
     * @return bool
     */
    public function isAngledAddress()
    {
        return self::ANGLE_ADDR === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function angledAddress()
    {
        return new self(self::ANGLE_ADDR);
    }

    /**
     * @return bool
     */
    public function isAtSign()
    {
        return self::AT === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function atSign()
    {
        return new self(self::AT);
    }

    /**
     * @return bool
     */
    public function isDot()
    {
        return self::DOT === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function dot()
    {
        return new self(self::DOT);
    }

    /**
     * @return bool
     */
    public function isListSeparator()
    {
        return self::LIST_SEPARATOR === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function listSeparator()
    {
        return new self(self::LIST_SEPARATOR);
    }

    /**
     * @return bool
     */
    public function isWhiteSpace()
    {
        return self::WSP === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function whiteSpace()
    {
        return new self(self::WSP);
    }

    /**
     * @return bool
     */
    public function isNotWhiteSpaceOrControl()
    {
        return self::NO_WS_CTL === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function notWhiteSpaceOrControl()
    {
        return new self(self::NO_WS_CTL);
    }

    /**
     * @return bool
     */
    public function isSpecial()
    {
        return self::SPECIAL === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function special()
    {
        return new self(self::SPECIAL);
    }

    // Token Indicator for parsing purposes only

    /**
     * @return bool
     */
    public function isRecommendedLineLength()
    {
        return self::RECOMMENDED_LINE_SIZE === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function recommendedLineLength()
    {
        return new self(self::RECOMMENDED_LINE_SIZE);
    }

    /**
     * @return bool
     */
    public function isRequiredLineLength()
    {
        return self::REQUIRED_LINE_SIZE === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function requiredLineLength()
    {
        return new self(self::REQUIRED_LINE_SIZE);
    }

    /**
     * @return bool
     */
    public function isPaired()
    {
        return self::PAIRED === $this->type;
    }

    /**
     * @return TokenType
     */
    public static function paired()
    {
        return new self(self::PAIRED);
    }


}