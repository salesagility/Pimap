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
 * Class LexemeList
 * @package SalesAgility\Imap\Lexeme
 * A list of \SalesAgility\Imap\Lexeme\Lexeme objects
 */
class LexemeList implements LexemeIteratorInterface, \ArrayAccess
{
    private $lexemeList = array();
    private $currentKey = 0;

    /**
     * @return Lexeme
     */
    public function current()
    {
        return $this->lexemeList[$this->currentKey];
    }

    /**
     *  Move key to next postion
     */
    public function next()
    {
        ++$this->currentKey;
    }

    /**
     * @return int key position in  list
     */
    public function key()
    {
        return $this->currentKey;
    }

    /**
     * @return bool if key is within range
     */
    public function valid()
    {
        return $this->currentKey < count($this->lexemeList);
    }

    /**
     * set the key to the first position
     */
    public function rewind()
    {
        $this->currentKey = 0;
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->lexemeList);
    }

    /**
     * @param int $offset
     * @return Lexeme
     */
    public function offsetGet($offset)
    {
        return $this->lexemeList[$offset];
    }

    /**
     * @param int $offset
     * @param Lexeme $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {

        $newOffset = false;
        if ($offset === null) {
            $newOffset = true;
        } elseif (gettype($offset) !== "integer") {
            throw new \InvalidArgumentException('Lexeme List can only store integer key values');
        }

        if ($value instanceof Lexeme) {
            if (!$newOffset) {
                $this->lexemeList[$offset] = $value;
            } else {
                $count = count($this->lexemeList);
                if ($count === 0) {
                    $this->lexemeList[0] = $value;
                } else {
                    $this->lexemeList[$count] = $value;
                }
            }
        } else {
            throw new \InvalidArgumentException('Lexeme List can only store values which derive from a Lexeme');
        }
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->lexemeList[$offset]);
    }

    /**
     * @param $offset
     */
    public function seek($offset)
    {
        $this->currentKey = $offset;
    }

    public function fastForward()
    {
        $this->currentKey = count($this->lexemeList) - 1;
    }
}
