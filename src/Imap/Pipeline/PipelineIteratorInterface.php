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


namespace SalesAgility\Imap\Pipeline;


/**
 * Interface PipelineIteratorInterface
 * @package SalesAgility\Imap\Pipeline
 */
interface PipelineIteratorInterface extends \OuterIterator
{
    /**
     * Return the current element
     * @return PipeInterface
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
     * @return  &PipeIterator innerString
     */
    public function getInnerIterator();
}