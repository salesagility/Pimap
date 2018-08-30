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

namespace SalesAgility\Imap\Interpreter;

use SalesAgility\Imap\Lexeme\LexemeList;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Utility\Assert;

/**
 * Class OctetInterpreter
 * @package SalesAgility\Imap\Interpreter
 */
class OctetInterpreter
{
    /**
     * @var int used to validate the size of headers and bodies
     * @see https://tools.ietf.org/html/rfc3501#page-16
     */
    private $count = 0;

    /**
     * @param LexemeList $lexemes
     * @return int
     * @throws \Exception
     */
    public function parse(LexemeList &$lexemes)
    {
        $iterator = StringIterator::withLiteral($lexemes->current()->toString());
        $validFirstChar = $iterator->current() === '{';
        $iterator->fastForward();
        $validLastChar = $iterator->current() === '}';
        $number = (int)trim($iterator->getInnerString(), '{}');
        Assert::is($validFirstChar && $validLastChar && is_numeric($number), 'Message Interpreter: expected number of octets');

        return $number;
    }


    /**
     * @param $integer
     */
    public function add($integer)
    {
        $this->count += $integer;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    public function reset()
    {
        $this->count = 0;
    }

    /**
     * @param LexemeList $lexemes
     * @param $offset
     * @param $limit
     * @return bool
     */
    public function isEndOfOctetBoundary(LexemeList & $lexemes, $offset, $limit)
    {
        $lexeme = $lexemes->current();
        $lastCharacterPosition = $offset + $limit;
        /** @var StringIterator $token */
        foreach ($lexeme as $token) {
            if ($token->first() >= $lastCharacterPosition) {
                return true;
            };

            if ($token->last() >= $lastCharacterPosition) {
                return true;
            }
        }

        return false;

    }
}