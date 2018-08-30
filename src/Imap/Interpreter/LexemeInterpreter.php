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
use SalesAgility\Imap\Lexeme\LexemeType;

/**
 * Class LexemeInterpreter
 * @package SalesAgility\Imap\Interpreter
 */
class LexemeInterpreter
{
    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    public function isNewLine(LexemeList &$lexemes)
    {
        return $lexemes->current()->hasType(LexemeType::newLine());
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    public function isKeyword(LexemeList &$lexemes)
    {
        return $lexemes->current()->hasType(LexemeType::allCapitals())
            || $lexemes->current()->hasType(LexemeType::capitalsNumbers());
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    public function isNumber(LexemeList &$lexemes)
    {
        return $lexemes->current()->hasType(LexemeType::allNumbers());
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    public function isWhitespace(LexemeList &$lexemes)
    {
        return $lexemes->current()->hasType(LexemeType::whitespace());
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    public function isGroup(LexemeList &$lexemes)
    {
        return $lexemes->current()->hasType(LexemeType::group());
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    public function isOptional(LexemeList &$lexemes)
    {
        return $lexemes->current()->hasType(LexemeType::optional());
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    public function isFlag(LexemeList &$lexemes)
    {
        return $lexemes->current()->toString() === '\\';
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    public function isAtom(LexemeList &$lexemes)
    {
        return $lexemes->current()->hasType(LexemeType::atom());
    }

    /**
     * @param LexemeList $lexemes
     * @return int
     */
    public function lastInGroup(LexemeList &$lexemes)
    {
        $groupKey = $lexemes->key();
        $count = $lexemes->current()->current()->lastKey();
        $lastKey = $groupKey;

        while ($lexemes->valid()) {
            $lexemes->next();

            // handle when no new line is after the outer group
            if (!$lexemes->valid()) {
                $lastKey = $lexemes->key() - 1;
                $lexemes->seek($lastKey);
                break;
            }

            // Skip empty fields like Subject
            if ($lexemes->current()->key() === null) {
                continue;
            }

            $lexemes->current()->fastForward();
            if ($lexemes->current()->current()->firstKey() >= $count) {
                $lastKey = $lexemes->key();
                break;
            }

            if ($lexemes->current()->current()->lastKey() >= $count) {
                $lastKey = $lexemes->key();
                break;
            }
        }

        $lexemes->seek($groupKey);
        return $lastKey;
    }
}