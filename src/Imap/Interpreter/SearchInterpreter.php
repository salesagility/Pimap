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


use SalesAgility\Imap\Lexeme\LexemeType;
use SalesAgility\Imap\Lexeme\Lexemizer;
use SalesAgility\Imap\Response\MessageFactory;
use SalesAgility\Imap\Response\Message;
use SalesAgility\Imap\Response\MessageList;
use SalesAgility\Imap\Token\Tokenizer;
use SalesAgility\Iteration\StringIterator;

class SearchInterpreter implements StringIteratorInterpreter
{
    /**
     * @param StringIterator $iterator
     * @return array|MessageList
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    public function parse(StringIterator $iterator)
    {
        $messageList = new MessageList();

        $tokenizer = new Tokenizer();
        $lexemizer = new Lexemizer();
        $lexemeInterpreter = new LexemeInterpreter();
        $tokens = $tokenizer->parseWithoutLineRestrictions($iterator);
        $lexemes = $lexemizer->parse($tokens);

        while ($lexemes->valid()) {
            if ($lexemeInterpreter->isNumber($lexemes)) {
                $message = MessageFactory::instance();
                $message->offsetSet('number', $lexemes->current()->toString());
                $messageList[] = $message;
            }

            $lexemes->next();
        }

        return $messageList;
    }
}