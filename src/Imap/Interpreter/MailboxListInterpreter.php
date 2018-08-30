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
use SalesAgility\Imap\Lexeme\Lexemizer;
use SalesAgility\Imap\Token\Tokenizer;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Imap\Response\Mailbox;
use SalesAgility\Imap\Response\MailboxList;

class MailboxListInterpreter implements StringIteratorInterpreter
{
    /**
     * @param StringIterator $iterator
     * @return array|MailboxList
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    public function parse(StringIterator $iterator)
    {
        $mailboxList = new MailboxList();

        $tokenizer = new Tokenizer();
        $lexemizer = new Lexemizer();
        $tokens = $tokenizer->parseWithoutLineRestrictions($iterator);
        $lexemes = $lexemizer->parse($tokens);

        while ($lexemes->valid()) {
            if ($lexemes->current()->toString() === '*') {
                $lexemes->next();
                $mailboxList[] = $this->parseMailbox($lexemes, $mailboxList);
            }

            $lexemes->next();
        }

        return $mailboxList;
    }

    private function parseMailbox(LexemeList &$lexemes)
    {
        $mailbox = new Mailbox();
        $lexemeInterpreter = new LexemeInterpreter();

        while ($lexemes->valid()) {
            if ($lexemeInterpreter->isWhitespace($lexemes)) {
                $lexemes->next();
                continue;
            } elseif ($lexemeInterpreter->isNewLine($lexemes)) {
                $lexemes->next();
                break;
            } elseif ($lexemeInterpreter->isKeyword($lexemes)) {
                $lexemes->next();
                continue;
            } elseif ($lexemeInterpreter->isGroup($lexemes)) {
                $this->parseNameAttributes($lexemes, $mailbox);
                $lastLexeme = $lexemeInterpreter->lastInGroup($lexemes);
                $lexemes->seek($lastLexeme);
                continue;
            } elseif ($lexemeInterpreter->isAtom($lexemes)) {
                if (empty($mailbox->offsetGet('hierarchy'))) {
                    $mailbox->offsetSet('hierarchy', trim($lexemes->current()->toString(), '"'));
                } elseif (empty($mailbox->offsetGet('name'))) {
                    $mailbox->offsetSet('name', trim($lexemes->current()->toString(), '"'));
                    break;
                }
                $lexemes->next();
                continue;
            }

            $lexemes->next();
        }

        return $mailbox;
    }

    private function parseNameAttributes(LexemeList &$lexemes, Mailbox &$mailbox)
    {
        // get substring of group
        $string = $lexemes->current()->current()->toString();
        $string = trim($string, '()');
        $string = str_replace('\\', '', $string);
        $attributes = explode(' ', $string);
        foreach ($attributes as $attribute) {
            $mailbox->offsetSet('attributes', $attribute);

        }
    }
}