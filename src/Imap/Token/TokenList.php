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
 * Class TokenList
 * @package SalesAgility\Imap\Token
 * A list of \SalesAgility\Imap\Token\Token objects
 */
class TokenList implements \Iterator, \ArrayAccess
{
    private $tokenList = array();
    private $currentKey = 0;

    /**
     * @return Token
     */
    public function current()
    {
        return $this->tokenList[$this->currentKey];
    }

    public function next()
    {
        ++$this->currentKey;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->currentKey;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->currentKey >= 0 && $this->currentKey < count($this->tokenList);
    }

    public function rewind()
    {
        $this->currentKey = 0;
    }

    /**
     * @param $offset
     */
    public function seek($offset)
    {
        $this->currentKey = $offset;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->tokenList);
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
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $newOffset = false;
        if ($offset === null) {
            $newOffset = true;
        } elseif (gettype($offset) !== "integer") {
            throw new \InvalidArgumentException('Token List can only store integer key values');
        }

        if ($value instanceof Token) {
            if (!$newOffset) {
                $this->tokenList[$offset] = $value;
            } else {
                $count = count($this->tokenList);
                if ($count === 0) {
                    $this->tokenList[0] = $value;
                } else {
                    $this->tokenList[$count] = $value;
                }
            }
        } else {
            throw new \InvalidArgumentException('Token List can only store values which derive from a Token');
        }
    }

    /**
     * @param $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->tokenList[$offset]);
        $this->tokenList = array_values($this->tokenList);
    }
}