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

use SalesAgility\Iteration\StringIterator;
use SalesAgility\Iteration\StringIteratorInterface;

/**
 * Class Token
 * @package SalesAgility\Imap\Token
 * @see https://www.ietf.org/rfc/rfc2822.txt
 * Primitive Tokens
 */
class Token implements TokenIteratorInterface
{
    /** @var TokenType $type */
    private $type;

    /**
     * Token constructor.
     * @param StringIteratorInterface $iterator
     * @param TokenType $type
     */
    public function __construct(StringIteratorInterface $iterator, TokenType $type)
    {
        $this->iterator = $iterator;
        $this->type = $type;
    }

    /**
     * @return TokenType
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $currentKey = $this->iterator->key();
        $this->iterator->rewind();
        $string = "";

        foreach ($this->iterator as $character) {
            $string .= $character;
        }

        $this->iterator->seek($currentKey);
        return $string;
    }

    /**
     * @return int
     */
    public function firstKey()
    {
        $currentKey = $this->key();
        $this->rewind();
        $firstKey = $this->key();
        $this->seek($currentKey);
        return $firstKey;
    }

    /**
     * @return int
     */
    public function lastKey()
    {
        $currentKey = $this->key();
        $this->fastForward();
        $lastKey = $this->key();
        $this->seek($currentKey);
        return $lastKey;
    }

    /** @var StringIteratorInterface $iterator */
    protected $iterator;

    /**
     * @return string
     */
    public function current()
    {
        return $this->iterator->current();
    }

    public function next()
    {
        $this->iterator->next();
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    public function rewind()
    {
        $this->iterator->rewind();
    }

    public function fastForward()
    {
        $this->iterator->fastForward();
    }

    /**
     * @param int $position
     */
    public function seek($position)
    {
        $this->iterator->seek($position);
    }

    /**
     * @return string
     */
    public function getInnerString()
    {
        return $this->iterator->getInnerString();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->iterator->count();
    }

    /**
     * @return int
     */
    public function first()
    {
        return $this->iterator->first();
    }

    /**
     * @return int
     */
    public function last()
    {
        return $this->iterator->last();
    }

    /**
     * @return StringIterator
     */
    public function getInnerIterator()
    {
        return $this->iterator;
    }
}