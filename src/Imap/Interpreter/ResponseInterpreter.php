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
use SalesAgility\Imap\Lexeme\Lexemizer;
use SalesAgility\Imap\Token\Tokenizer;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Imap\Response\Response;

/**
 * Class ImapResponseInterpreter
 * @package SalesAgility\Imap\Interpreter
 */
class ResponseInterpreter
{
    /**
     * @param string $tag
     * @param StringIterator $iterator
     * @return Response
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    public function parse($tag, StringIterator $iterator)
    {

        $tokenizer = new Tokenizer();
        $tokenList = $tokenizer->parse($iterator);
        $lexemizer = new Lexemizer();
        $lexemeList = $lexemizer->parse($tokenList);

        while ($lexemeList->valid()) {
            // Find end of file
            $lexemeList->current()->rewind();
            if ($lexemeList->current()->hasType(LexemeType::capitalsNumbers())) {
                if ($lexemeList->current()->current()->toString() === $tag) {
                    // create sub string for the included non tagged responses
                    $endOfIncluded = $lexemeList->current()->current()->first();
                    if ($endOfIncluded === 0) {
                        $includedInMessages = StringIterator::withLiteral('', 0, 0);
                    } else {
                        $includedInMessages = StringIterator::withStringIterator($iterator, 0, $endOfIncluded);
                    }

                    // Get the tagged response
                    $key = $lexemeList->key();
                    $lexemeList->fastForward();
                    $lexemeList->current()->fastForward();
                    $lastCharacter = $lexemeList->current()->current()->last();
                    $count = ($lastCharacter - $endOfIncluded) + 1;
                    $responseMessage = StringIterator::withStringIterator($iterator, $endOfIncluded, $count);
                    $lexemeList->seek($key);

                    // get status message from tagged response
                    $lexemeList->next(); // tag
                    $lexemeList->next(); // whitespace
                    $status = $lexemeList->current()->toString(); // Response Status Code
                    return new Response($status, $responseMessage, $includedInMessages);
                }
            }
            $lexemeList->next();
        }
    }
}