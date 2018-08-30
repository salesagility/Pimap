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
 * Interface StringIteratorInterface
 * @package SalesAgility\Iteration
 */
interface StringIteratorInterface extends \Iterator
{
    /**
     * Return the current element
     * @return string
     */
    public function current();

    /**
     *  Move forward to next element
     */
    public function next();

    /**
     * Return the key of the current element
     * @return int
     */
    public function key();

    /**
     * Checks if current position is valid
     * @return bool
     */
    public function valid();

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind();

    /**
     * Fast forward the iterator to the last element
     */
    public function fastForward();

    /**
     * @param int $position
     */
    public function seek($position);

    /**
     * @return string innerString
     */
    public function getInnerString();

    /**]
     * @return int
     */
    public function count();

    /**
     * @return int first character position
     */
    public function first();

    /**
     * @return int last character position
     */
    public function last();
}