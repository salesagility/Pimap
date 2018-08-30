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

use SalesAgility\Imap\Token\Token;
use SalesAgility\Imap\Token\TokenList;

/**
 * Class Lexeme
 * @package SalesAgility\Imap\Lexeme
 * Higher level tokens and rules
 * @see https://www.ietf.org/rfc/rfc2822.txt
 */
class Lexeme implements LexemeIteratorInterface
{
    /** @var TokenList $iterator */
    private $iterator;

    /** @var int $first */
    private $first;

    /** @var int $current */
    private $current;

    /** @var int $last */
    private $last;

    /** @var int $length */
    private $length;

    /**
     * @var Token[] $tokenList
     */
    private $tokenList = array();

    /** @var LexemeType[] $type */
    private $types = array();

    /**
     * @var int used to validate the size of headers and bodies
     * @see https://tools.ietf.org/html/rfc3501#page-16
     */
    private $octetCount = 0;

    /**
     * Token constructor.
     */
    public function __construct()
    {
        $this->iterator = new TokenList();
    }

    /**
     * @param LexemeType $type
     * @return bool
     */
    public function hasType(LexemeType $type)
    {
        foreach ($this->types as $t) {
            if ($t->isType($type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param LexemeType $type
     */
    public function addType(LexemeType $type)
    {
        $this->types[] = $type;
    }


    /**
     * @param Token $token
     */
    public function addToken(Token $token)
    {
        $this->tokenList[] = $token;
        if ($this->current === null) {
            $this->current = 0;
        }

        if ($this->first === null) {
            $this->first = 0;
        }

        if ($this->length === null) {
            $this->length = 0;
        }

        ++$this->length;

        if ($this->last === null) {
            $this->last = -1;
        }

        ++$this->last;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $string = '';
        /** @var Token $token */
        foreach ($this->tokenList as $token) {
            $string .= $token->toString();
        }

        return $string;
    }

    /**
     * Return the current element
     * @return Token
     */
    public function current()
    {
        return $this->tokenList[$this->current];
    }

    /**
     *  Move forward to next element
     */
    public function next()
    {
        $this->current += 1;
    }

    /**
     * Return the key of the current element
     * @return int
     */
    public function key()
    {
        return $this->current;
    }

    /**
     * Checks if current position is valid
     * @return bool
     */
    public function valid()
    {
        return $this->length > 0
            && $this->current <= $this->last
            && ($this->current - $this->first) < $this->length;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        $this->current = $this->first;
    }

    /**
     * Fast forward the iterator to the last element
     */
    public function fastForward()
    {
        $this->current = $this->last;
    }

    /**
     * @param int pos
     * @return bool|int
     */
    public function seek($pos)
    {
        $cpos = $this->current;
        $this->current = $pos;
        if (!$this->valid()) {
            $this->current = $cpos;
            return false;
        }

        return $pos;
    }

    /**
     * @param int $offset
     * @return Token
     */
    public function offsetGet($offset)
    {
        return $this->tokenList[$offset];
    }


    /**
     * @return int
     */
    public function octetCount()
    {
        return $this->octetCount;
    }

    /**
     * @param int $octets
     */
    public function addOctets($octets)
    {
        if ($this->hasType(LexemeType::group())) {
            $this->octetCount = 2;
        } elseif ($this->hasType(LexemeType::newLine())) {
            $this->octetCount = 2;
        } else {
            $this->octetCount += $octets;
        }
    }
}
