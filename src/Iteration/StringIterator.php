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

namespace SalesAgility\Iteration;

/**
 * Class StringIterator
 * @package SalesAgility\Iteration
 */
class StringIterator implements StringIteratorInterface
{
    /** @var int $first */
    private $first;

    /** @var int $current */
    private $current;

    /** @var int $last */
    private $last;

    /** @var int $length */
    private $length;

    /** @var &string $string */
    private $string;

    /**
     * StringIterator constructor.
     * @param string $string
     * @param int $offset
     * @param int $count
     * @throws \InvalidArgumentException
     */
    public function __construct(&$string, $offset = 0, $count = -1)
    {
        if (!is_string($string)) {
            throw new \InvalidArgumentException('$string must be a string');
        }

        if (!is_integer($offset)) {
            throw new \InvalidArgumentException('$offset must be a integer');
        }

        if (!is_integer($count)) {
            throw new \InvalidArgumentException('$count must be a integer');
        }

        // calculate starting position
        $this->string = &$string;
        $this->first = $offset;
        $this->current = $this->first;

        // calculate ending position
        if ($count === -1) {
            $this->length = strlen($string);
            $this->last = ($this->length - 1);
        } elseif ($count === 0) {
            $this->length = 0;
            $this->last = 0;
        } else {
            $this->length = $count;
            $this->last = $this->first + ($this->length - 1);
        }
    }

    /**
     * @param string $string
     * @param int $offset
     * @param int $count
     * @return StringIterator
     */
    public static function withLiteral($string, $offset = 0, $count = -1)
    {
        return new self($string, $offset, $count);
    }

    /**
     * @param StringIterator $iterator
     * @param int $offset
     * @param int $count
     * @return StringIterator
     */
    public static function withStringIterator(StringIterator $iterator, $offset = 0, $count = -1)
    {
        return new StringIterator($iterator->string, $offset, $count);
    }

    /**
     * Return the current element
     * @return string
     */
    public function current()
    {
        return $this->string[$this->current];
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
     * @param int $pos
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
     * @return string innerString
     */
    public function getInnerString()
    {
        return $this->string;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->length;
    }

    /**
     * @return int first character position
     */
    public function first()
    {
        return $this->first;
    }

    /**
     * @return int last character position
     */
    public function last()
    {
        return $this->last;
    }

    /**
     * @return bool|string
     */
    public function toString()
    {
        return substr($this->string, $this->first, $this->last - $this->first - 1);
    }
}