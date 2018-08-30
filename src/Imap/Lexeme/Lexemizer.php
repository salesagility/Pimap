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

use SalesAgility\Imap\Token\Token;
use SalesAgility\Imap\Token\Tokenizer;
use SalesAgility\Imap\Token\TokenList;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Utility\Assert;

/**
 * Class Lexemizer
 * @package SalesAgility\Imap\Lexeme
 * @see https://www.ietf.org/rfc/rfc2822.txt
 * Higher level parser to aid in better interpretation
 */
class Lexemizer
{
    private $skippedOctets = 0;

    /**
     * @param TokenList $tokens
     * @return LexemeList
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    public function parse(TokenList $tokens)
    {
        $lexemeList = new LexemeList();

        foreach ($tokens as $tokenIndex => $token) {

            // reduce protocol noise
            if ($token->type()->isFoldingWhiteSpace()) {
                $this->skipOctets($token->count());
                $ref = $lexemeList->current();
                $this->addSkippedOctets($ref);
                continue;
            }

            if ($token->type()->isControlCharacter()) {
                $this->skipOctets($token->count());
                continue;
            }

            // Error Detection / Correction
            if ($token->type()->isRecommendedLineLength()) {
                $this->skipOctets($token->count());
                continue;
            }

            if ($token->type()->isRequiredLineLength()) {
                $this->skipOctets($token->count());
                continue;
            }

            if ($token->type()->isEndOfLine()) {
                $lexemeList[] = $this->fromToken($token, [LexemeType::newLine()]);
                continue;
            }

            if ($token->type()->isWhiteSpace()) {
                $lexemeList[] = $this->fromToken($token, [LexemeType::whitespace()]);
                continue;
            }

            // recursively tokenize paired items
            if ($token->type()->isGroup()) {
                // mark the beginning and end of each group
                // tokenize the insides of that group
                $this->seekGroup($tokens, $lexemeList);
                continue;
            }

            if ($token->type()->isQuoted()) {
                $lexemeList[] = $this->fromTokens($tokens, $tokenIndex, 1, [LexemeType::quotedString(), LexemeType::atom()]);
                continue;
            }

            if ($token->type()->isNonFoldedLiteral()) {
                // is optional?
                $lexemeList[] = $this->fromTokens($tokens, $tokenIndex, 1, [LexemeType::optional()]);
                continue;
            }

            $lexemeTypes = array();
            $a = ord('a');
            $z = ord('z');
            $A = ord('A');
            $Z = ord('Z');

            $hasCapitals = false;
            $hasLowerCase = false;
            $hasNumeric = false;
            $hasSpecial = false;
            // Might contain an attribute
            foreach ($token as $c => $character) {
                $ord = ord($character);
                if ($ord >= $A && $character <= $Z) {
                    $hasCapitals = true;
                }

                if (is_numeric($character)) {
                    $hasNumeric = true;
                }

                if ($ord >= $a && $character <= $z) {
                    $hasLowerCase = true;
                }

                if ($token->type()->isSpecial()) {
                    $hasSpecial = true;
                }
            }

            if ($hasSpecial) {
                if ($token->toString() === ':') {
                    $field = $this->seekField($lexemeList, $tokens);
                    if ($field !== false) {
                        $lexemeList[] = $field;
                        // New line marks the end of a field
                        // however the next field needs to detect the previous line
                        // so we need to split out the new line
                        $lexemeList[] = $this->fromToken($tokens->current(), [LexemeType::newLine()]);
                        continue;
                    }
                }
            } elseif (!$hasCapitals && $hasNumeric && !$hasLowerCase) {
                $lexemeTypes[] = LexemeType::allNumbers();
            } elseif ($hasCapitals && $hasNumeric && !$hasLowerCase) {
                $lexemeTypes[] = LexemeType::capitalsNumbers();
            } elseif ($hasCapitals && !$hasNumeric && !$hasLowerCase) {
                $lexemeTypes[] = LexemeType::allCapitals();
            }

            $lexemeTypes[] = LexemeType::atom();
            $lexemeList[] = $this->fromTokens($tokens, $tokenIndex, 1, $lexemeTypes);
        }


        return $lexemeList;
    }

    /**
     * @param TokenList $tokens
     * @param LexemeList $lexemeList
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    private function seekGroup(TokenList &$tokens, LexemeList &$lexemeList)
    {
        $lexemeList[] = $this->fromTokens($tokens, $tokens->key(), 1, [LexemeType::group(), LexemeType::atom()]);

        $token = $tokens->current();
        $start = $token->firstKey() + 1;
        $end = $token->lastKey() - 1;
        $innerString = $token->getInnerString();
        $insideGroup = new StringIterator($innerString, $start, $end - $start + 1);

        $tokenParser = new Tokenizer();
        $groupTokens = $tokenParser->parse($insideGroup);
        // Recursively find groups and append them
        $groupLexemes = $this->parse($groupTokens);
        $this->append($lexemeList, $groupLexemes);
//        // 1 for opening and closing parenthesis
//        $this->skipOctets(2);
    }

    /**
     * @param LexemeList $lexemeList
     * @param TokenList $tokenList
     * @return bool|Lexeme
     * @throws \Exception
     * @see https://tools.ietf.org/html/rfc2822#page-18
     */
    private function seekField(LexemeList &$lexemeList, TokenList &$tokenList)
    {
        Assert::is($tokenList->current()->toString() === ':', ' TokenList::current() must be a colon');
        // There must be at least 4 tokens to make a field
        if (!$tokenList->offsetExists(3)) {
            return false;
        }

        if ($tokenList->key() < 2) {
            return false;
        }

        $key = $tokenList->key();
        $newline = $tokenList->offsetGet($key - 2);
        if (!$newline->type()->isEndOfLine()) {
            return false;
        }

        // fast forward the key() to the end
        $lexemeList->fastForward();
        // Add field header to lexeme before the ":"
        $lexemeList->offsetGet($lexemeList->key())->addType(LexemeType::fieldHeader());
        // ':' was skipped but we still need to record the octet
        $this->skipOctets($tokenList->current()->count());

        // seek body
        $lexeme = new Lexeme();
        $tokenList->next();
        $lexeme->addType(LexemeType::fieldBody());
        while ($tokenList->valid()) {

            if ($tokenList->current()->type()->isFoldingWhiteSpace()) {
                $this->skipOctets($tokenList->current()->count());
                $tokenList->next();
                continue;
            }

            if ($tokenList->current()->type()->isEndOfLine()) {
                break;
            }

            $token = $tokenList->current();
            $this->addToken($lexeme, $token);
            $tokenList->next();
        }
        Assert::is($tokenList->valid(), 'lexemizer couldn\'t detect the end of the field');
        $this->addSkippedOctets($lexeme);
        Assert::is($tokenList->current()->type()->isEndOfLine(), 'last token in a field must must be the end of line');
        return $lexeme;
    }

    /**
     * @param TokenList $tokens
     * @param $offset
     * @param $count
     * @param array $types
     * @return Lexeme
     */
    private function fromTokens(TokenList $tokens, $offset, $count, array $types)
    {
        $lexeme = new Lexeme();

        /** @var LexemeType $type */
        foreach ($types as $type) {
            $lexeme->addType($type);
        }

        $tokens->seek($offset);

        $countDown = $count;
        while ($tokens->valid() && $countDown > 0) {
            $token = $tokens->current();
            $this->addToken($lexeme, $token);
            $tokens->next();
            --$countDown;
        }

        // fix offset by 1 error when there is only 1 token
        if ($count == 1) {
            $tokens->seek($offset);
        }

//        $this->addSkippedOctets($lexeme);
        return $lexeme;
    }

    /**
     * @param Token $token
     * @param array $types
     * @return Lexeme
     */
    private function fromToken(Token $token, array $types)
    {
        $lexeme = new Lexeme();

        /** @var LexemeType $type */
        foreach ($types as $type) {
            $lexeme->addType($type);
        }
        $this->addToken($lexeme, $token);
        $this->addSkippedOctets($lexeme);
        return $lexeme;
    }

    /**
     * @param LexemeList $a
     * @param LexemeList $b
     */
    /**
     * @param LexemeList $a
     * @param LexemeList $b
     */
    private function append(LexemeList &$a, LexemeList &$b)
    {
        foreach ($b as $append) {
            $a[] = $append;
        }
    }

    /**
     * @param Lexeme $lexeme
     * @param Token $token
     */
    /**
     * @param Lexeme $lexeme
     * @param Token $token
     */
    private function addToken(Lexeme &$lexeme, Token &$token)
    {
        $lexeme->addToken($token);

        $lexeme->addOctets($token->count());
    }

    /**
     * @param $amount
     * Keep a tally of how many octets have been skipped
     */
    private function skipOctets($amount)
    {
        $this->skippedOctets += $amount;
    }

    /**
     * @param Lexeme $lexeme
     */
    /**
     * @param Lexeme $lexeme
     */
    private function addSkippedOctets(Lexeme &$lexeme)
    {
        // Octet count must include tokens which have been skipped
        $lexeme->addOctets($this->skippedOctets);
        $this->skippedOctets = 0;
    }
}