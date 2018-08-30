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


use SalesAgility\Imap\Token\Tokenizer;
use SalesAgility\Imap\Token\TokenList;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Imap\Response\Mailbox;

class MailboxInterpreter implements StringIteratorInterpreter
{
    /**
     * @param StringIterator $iterator
     * @return Mailbox
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    public function parse(StringIterator $iterator)
    {
        $mailbox = new Mailbox();
        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->parse($iterator);

        while ($tokens->valid()) {
            $tokenString = $tokens->current()->toString();

            if ($tokenString === 'FLAGS') {
                $this->parseFlags($tokens, $mailbox);
            } elseif ($tokenString === 'EXISTS') {
                $this->parseKeywordWithNumberPrefix($tokens, $mailbox);
            } elseif ($tokenString === 'RECENT') {
                $this->parseKeywordWithNumberPrefix($tokens, $mailbox);
            }

            // [KEYWORD ...]
            if ($tokens->current()->type()->isNonFoldedLiteral()) {
                $this->parseOption($tokens, $mailbox);
            }

            // KEYWORD
            $this->parseOptional($tokens, $mailbox);

            $tokens->next();
        }

        return $mailbox;
    }

    public function parseFlags(TokenList &$tokens, Mailbox &$mailbox)
    {
        while ($tokens->valid()) {

            // find group
            // use string iterator to create a substring
            // strip replace \
            // convert to array
            // assign to Mailbox
            if ($tokens->current()->type()->isGroup()) {
                $string = $this->buildStringIterator($tokens, $mailbox)->toString();
                $escaped = str_replace("\\", '', $string);
                $flags = explode(' ', $escaped);
                foreach ($flags as $flag) {
                    $mailbox->offsetSet('flags', $flag);
                }

                $tokens->next();
                return;
            }

            if ($tokens->current()->type()->isEndOfLine()) {
                $tokens->next();
                return;
            }

            $tokens->next();
        }

    }

    public function parseKeywordWithNumberPrefix(TokenList &$tokens, Mailbox &$mailbox)
    {
        $keyword = strtolower($tokens->current()->toString());
        $mailbox->offsetSet($keyword, $this->seekNumberBefore($tokens));

    }

    private function parseOption(TokenList &$tokens, Mailbox &$mailbox)
    {
        $tokenStringIterator = $this->buildStringIterator($tokens);
        $tokenizer = new Tokenizer();
        $subTokens = $tokenizer->parse($tokenStringIterator);

        while ($subTokens->valid()) {
            $this->parseOptional($subTokens, $mailbox);
            $subTokens->next();
        }
    }

    private function seekNumberBefore(TokenList &$tokens)
    {
        $initialKey = $tokens->key() + 1;

        $tokens->seek($tokens->key() - 1);
        while ($tokens->valid()) {
            if ($tokens->current()->type()->isNotWhiteSpaceOrControl()) {
                $tokenString = $tokens->current()->toString();
                if (is_numeric($tokenString)) {
                    $tokens->seek($initialKey);
                    return $tokenString;
                }
            }

            if ($tokens->current()->type()->isEndOfLine()) {
                break;
            }

            $tokens->seek($tokens->key() - 1);
        }

        $tokens->seek($initialKey);
        return 0;
    }

    private function seekNumberAfter(TokenList &$tokens)
    {
        while ($tokens->valid()) {
            if ($tokens->current()->type()->isNotWhiteSpaceOrControl()) {
                $tokenString = $tokens->current()->toString();
                if (is_numeric($tokenString)) {
                    return $tokenString;
                }
            }

            if ($tokens->current()->type()->isEndOfLine()) {
                break;
            }

            $tokens->next();
        }

        return 0;
    }

    private function buildStringIterator(TokenList &$tokens)
    {
        return StringIterator::withStringIterator(
            $tokens->current()->getInnerIterator(),
            $tokens->current()->first() + 1,
            $tokens->current()->last() - $tokens->current()->first() + 1
        );
    }

    private function parseOptional(TokenList &$tokens, Mailbox &$mailbox)
    {
        $tokenString = $tokens->current()->toString();
        if ($tokenString === 'UNSEEN') {
            $mailbox->offsetSet('unseen', (string)$this->seekNumberAfter($tokens));
            $tokens->next();
            return;
        } elseif ($tokenString === 'UIDVALIDITY') {
            $mailbox->offsetSet('uidvalidity', (string)$this->seekNumberAfter($tokens));
            $tokens->next();
            return;
        } elseif ($tokenString === 'UIDNEXT') {
            $mailbox->offsetSet('uidnext', (string)$this->seekNumberAfter($tokens));
            $tokens->next();
            return;
        }

    }
}