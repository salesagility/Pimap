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


namespace SalesAgility\Imap\Token;


use SalesAgility\Iteration\StringIterator;
use SalesAgility\Iteration\StringIteratorInterface;

/**
 * Class Tokenizer
 * @package SalesAgility\Imap\Token
 * @see https://www.ietf.org/rfc/rfc2822.txt
 * Detects Primitive Tokens and boundary positions
 */
class Tokenizer
{
    /**
     * @param StringIterator $characters
     * @return TokenList
     * @throws TokenException
     */
    public function parse(StringIterator $characters)
    {
        $list = new TokenList();
        $lastEOL = 0;
        $recommendedLineLength = 78;
        $requiredLineLength = 998;
        $requiredLineLengthFound = false;

        // When groups are being parsed the $characterIndexOffset
        // ensures that the line length detection is correct.
        $characterIndexOffset = $characters->key();

        foreach ($characters as $characterIndex => $character) {
            // Line length error detection
            if (($characterIndex - $characterIndexOffset) - $lastEOL === $recommendedLineLength) {
                $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::recommendedLineLength());
            } elseif (($characterIndex - $characterIndexOffset) - $lastEOL === $requiredLineLength) {
                $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::requiredLineLength());
                $requiredLineLengthFound = true;
            }

            if ($this->isCarriageReturn($character)) {
                // what kind of token?
                if (false !== ($token = $this->seekLineFolding($characters))) {
                    $list[] = $token;
                    $lastEOL = $characterIndex;
                    $requiredLineLengthFound = false;
                    continue;
                } elseif (false !== ($token = $this->seekEndOfLine($characters))) {
                    $list[] = $token;
                    $lastEOL = $characterIndex;
                    $requiredLineLengthFound = false;
                    continue;
                } else {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::controlCharacter());
                    if ($requiredLineLengthFound) {
                        throw TokenException::requiredLineLengthExceeded();
                    }
                    continue;
                }
            }

            if ($this->isWhiteSpace($character)) {
                $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::whiteSpace());
                if ($requiredLineLengthFound) {
                    throw TokenException::requiredLineLengthExceeded();
                }
                continue;
            }

            if ($this->isLineFeed($character)) {
                $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::controlCharacter());

                if ($requiredLineLengthFound) {
                    throw TokenException::requiredLineLengthExceeded();
                }
                continue;
            }

            if ($this->isSpecial($character)) {
                if ($requiredLineLengthFound) {
                    throw TokenException::requiredLineLengthExceeded();
                }
                // What kind?
                if ($this->isSpecialEscape($character)) {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                    continue;
                }

                if ($this->isSpecialAt($character)) {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::atSign());
                    continue;
                }

                if ($this->isSpecialDot($character)) {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::dot());
                    continue;
                }

                if ($this->isSpecialListSeparator($character)) {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::listSeparator());
                    continue;
                }

                if ($this->isSpecialColon($character)) {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                    continue;
                }
                /**
                 * Note:
                 * The tokenizer does not recursively process inside
                 * groups, options, addresses, quoted pairs etc.
                 *
                 * It is left to the high level parsers to decide when
                 * to tokenize the inner tokens of a pair, as it can pass the subset back to
                 * the tokenizer later for further processing
                 */
                // what kind of token?
                if ($this->isSpecialGroup($character)) {
                    if (false !== ($token = $this->seekClosingPair($characters, '(', ')'))) {
                        $list[] = $token;
                        continue;
                    } else {
                        // possible error? - let the higher level parsers decide
                        $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                        continue;
                    }
                }

                if ($this->isSpecialOption($character)) {
                    if (false !== ($token = $this->seekClosingPair($characters, '[', ']'))) {
                        $list[] = $token;
                        continue;
                    } else {
                        // possible error? - let the higher level parsers decide
                        $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                        continue;
                    }
                }

                if ($this->isSpecialAngledAddress($character)) {
                    if (false !== ($token = $this->seekClosingPair($characters, '<', '>'))) {
                        $list[] = $token;
                        continue;
                    } else {
                        // possible error? - let the higher level parsers decide
                        $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                        continue;
                    }
                }
                // Character is maybe error?
                $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                continue;
            }

            if ($this->isQuotedPair($character)) {
                if (false !== ($token = $this->seekClosingPair($characters, '"', '"'))) {
                    $list[] = $token;
                    continue;
                } else {
                    // possible error? - let the higher level parsers decide
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                    continue;
                }
            }

            if ($this->isNotWhiteSpaceOrControl($character)) {
                if ($requiredLineLengthFound) {
                    throw TokenException::requiredLineLengthExceeded();
                }
                $token = $this->seekNotWhiteSpaceOrControl($characters);
                $list[] = $token;
                continue;
            }
        }

        return $list;
    }

    public function parseWithoutLineRestrictions(StringIterator $characters)
    {
        $list = new TokenList();

        foreach ($characters as $characterIndex => $character) {


            if ($this->isCarriageReturn($character)) {
                // what kind of token?
                if (false !== ($token = $this->seekLineFolding($characters))) {
                    $list[] = $token;
                    continue;
                } elseif (false !== ($token = $this->seekEndOfLine($characters))) {
                    $list[] = $token;
                    continue;
                } else {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::controlCharacter());
                    continue;
                }
            }

            if ($this->isWhiteSpace($character)) {
                $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::whiteSpace());
                continue;
            }

            if ($this->isLineFeed($character)) {
                $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::controlCharacter());
                continue;
            }

            if ($this->isSpecial($character)) {

                // What kind?
                if ($this->isSpecialEscape($character)) {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                    continue;
                }

                if ($this->isSpecialAt($character)) {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::atSign());
                    continue;
                }

                if ($this->isSpecialDot($character)) {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::dot());
                    continue;
                }

                if ($this->isSpecialListSeparator($character)) {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::listSeparator());
                    continue;
                }

                if ($this->isSpecialColon($character)) {
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                    continue;
                }
                /**
                 * Note:
                 * The tokenizer does not recursively process inside
                 * groups, options, addresses, quoted pairs etc.
                 *
                 * It is left to the high level parsers to decide when
                 * to tokenize the inner tokens of a pair, as it can pass the subset back to
                 * the tokenizer later for further processing
                 */
                // what kind of token?
                if ($this->isSpecialGroup($character)) {
                    if (false !== ($token = $this->seekClosingPair($characters, '(', ')'))) {
                        $list[] = $token;
                        continue;
                    } else {
                        // possible error? - let the higher level parsers decide
                        $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                        continue;
                    }
                }

                if ($this->isSpecialOption($character)) {
                    if (false !== ($token = $this->seekClosingPair($characters, '[', ']'))) {
                        $list[] = $token;
                        continue;
                    } else {
                        // possible error? - let the higher level parsers decide
                        $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                        continue;
                    }
                }

                if ($this->isSpecialAngledAddress($character)) {
                    if (false !== ($token = $this->seekClosingPair($characters, '<', '>'))) {
                        $list[] = $token;
                        continue;
                    } else {
                        // possible error? - let the higher level parsers decide
                        $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                        continue;
                    }
                }
                // Character is maybe error?
                $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                continue;
            }

            if ($this->isQuotedPair($character)) {
                if (false !== ($token = $this->seekClosingPair($characters, '"', '"'))) {
                    $list[] = $token;
                    continue;
                } else {
                    // possible error? - let the higher level parsers decide
                    $list[] = $this->tokenFrom($characters, $characterIndex, 1, TokenType::special());
                    continue;
                }
            }

            if ($this->isNotWhiteSpaceOrControl($character)) {
                $token = $this->seekNotWhiteSpaceOrControl($characters);
                $list[] = $token;
                continue;
            }
        }

        return $list;
    }


    /**
     * @param string $character
     * @return bool
     */
    private function isNotWhiteSpaceOrControl($character)
    {
        // though this token type does allow specials
        // we need to detect them for the purposes of helping
        // the higher level parsers
        return !$this->isWhiteSpace($character)
            && !$this->isCarriageReturn($character)
            && !$this->isLineFeed($character);
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isWhiteSpace($character)
    {
        return $character === "\x20" || $character === "\x09";
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isCarriageReturn($character)
    {
        return $character === "\x0D";
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isLineFeed($character)
    {
        return $character === "\x0A";
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isSpecial($character)
    {
        switch ($character) {
            case "\x28":
                return true;
            case "\x29":
                return true;
            case "\x3C":
                return true;
            case "\x3E":
                return true;
            case "\x5B":
                return true;
            case "\x5D":
                return true;
            case "\x3A":
                return true;
            case "\x3B":
                return true;
            case "\x40":
                return true;
            case "\x5C":
                return true;
            case "\x2E":
                return true;
            case "\x2C":
                return true;
            default:
                return false;
        }
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isQuotedPair($character)
    {
        return $character === "\x22";
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isSpecialGroup($character)
    {
        return $character === "\x28";
    }

    /**
     * @param string $character
     * @return bool
     */
    public function isSpecialClosingGroup($character)
    {
        return $character === "\x29";
    }

    /**
     * @param string $character
     * @return bool
     */
    public function isSpecialClosingOption($character)
    {
        return $character === "\x5D";
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isSpecialOption($character)
    {
        return $character === "\x5B";
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isSpecialAngledAddress($character)
    {
        return $character === "\x3C";
    }

    /**
     * @param string $character
     * @return bool
     */
    public function isSpecialClosingAngledAddress($character)
    {
        return $character === "\x3E";
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isSpecialAt($character)
    {
        return $character === "\x40";
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isSpecialDot($character)
    {
        return $character === "\x2E";
    }

    /**
     * @param string $character
     * @return bool
     */
    private function isSpecialListSeparator($character)
    {
        return $character === "\x2C";
    }

    /**
     * @param string $character
     * @return bool
     */
    public function isSpecialEscape($character)
    {
        return $character === "\x5C";
    }

    /**
     * @param string $character
     * @return bool
     */
    public function isSpecialColon($character)
    {
        return $character === "\x3A";
    }

    /**
     * @param string $character
     * @return bool
     */
    public function isDoubleQuote($character)
    {
        return $character === "\x22";
    }

    /**
     * # IMPORTANT REQUIREMENT: seek[something] functions
     *
     * On a successful outcome:
     * StringIterator::seek() must be set to the last character of the token you are seeking. This is to
     * ensure that all tokens will be correctly identified.
     *
     * On a unsuccessful outcome
     * StringIterator::seek() must be set to the initial StringIterator::key() that was passed into the function
     *
     * On an exception:
     * An exception must be thrown, an exception is as sign that the process cannot continue.
     */


    /**
     * WARNING: Moves the StringIterator::key() to the position the start of the next line
     * @param StringIteratorInterface $characters
     * @return bool|Token false when EOL is not found
     */
    private function seekEndOfLine(StringIteratorInterface $characters)
    {
        if (!$this->isCarriageReturn($characters->current())) {
            throw new \InvalidArgumentException('Iterator::current() must start with a carriage return');
        }

        $startPosition = $characters->key();
        // We have already check that the current is CR
        $characters->next();

        if (!$characters->valid()) {
            $characters->seek($startPosition);
            return false;
        }

        if (!$this->isLineFeed($characters->current())) {
            $characters->seek($startPosition);
            return false;
        }

        $endPosition = $characters->key();
        $count = $endPosition - $startPosition + 1;

        return $this->tokenFrom($characters, $startPosition, $count, TokenType::endOfLine());
    }

    /**
     * WARNING: Moves the StringIterator::key() to the position of the closing character
     * @param StringIterator $characters current position must be the opening character
     * @param string $openCharacter start of the pair "(" / "{"  / DQUOTE
     * @param string $closeCharacter end of the part ")" / "}" / DQUOTE
     * @return bool|Token false when closing pair is not found
     */
    private function seekClosingPair(StringIterator &$characters, $openCharacter, $closeCharacter)
    {
        if ($characters->current() !== $openCharacter) {
            throw new \InvalidArgumentException('Iterator->current() position must be at the open character');
        }

        $startPosition = $characters->key();
        $groupsTotal = 0;

        // can't use foreach statement as it runs StringIteratorInterface::rewind()
        while ($characters->valid()) {
            $character = $characters->current();
            if ($character === $openCharacter) {
                if ($openCharacter !== $closeCharacter) {
                    ++$groupsTotal;
                } elseif ($openCharacter === $closeCharacter && $groupsTotal === 1) {
                    --$groupsTotal;
                } else {
                    // since the open and close character are the same
                    // we need to bypass the increment
                    // the second " will not be de-incremented
                    // when expected
                    // handle escaped close character
                    $groupsTotal = 1;
                }
            } elseif ($character === $closeCharacter) {
                // handle escaped close character
                --$groupsTotal;
            } else {
                $characters->next();
                continue;
            }

            if ($groupsTotal === 0) {
                if ($character === $closeCharacter) {
                    $endPosition = $characters->key() + 1;
                    $count = $endPosition - $startPosition;

                    if ($this->isSpecialGroup($openCharacter)) {
                        return $this->tokenFrom($characters, $startPosition, $count, TokenType::group());
                    } elseif ($this->isSpecialOption($openCharacter)) {
                        return $this->tokenFrom($characters, $startPosition, $count, TokenType::nonFoldedLiteral());
                    } elseif ($this->isDoubleQuote($openCharacter)) {
                        return $this->tokenFrom($characters, $startPosition, $count, TokenType::quoted());
                    } elseif ($this->isSpecialAngledAddress($openCharacter)) {
                        return $this->tokenFrom($characters, $startPosition, $count, TokenType::angledAddress());
                    } else {
                        return $this->tokenFrom($characters, $startPosition, $count, TokenType::paired());
                    }
                }
            }

            $characters->next();
        }

        $characters->seek($startPosition);
        return false;
    }

    /**
     * WARNING: Moves the StringIterator::key() to end of the fold
     * Iterates over the currentLine and the next line to determine
     * where the line is folding.
     * @param StringIteratorInterface $characters
     * @return bool|Token false when line folding is not found
     */
    private function seekLineFolding(StringIteratorInterface &$characters)
    {
        if (!$this->isCarriageReturn($characters->current())) {
            throw new \InvalidArgumentException('Iterator::current() must start with a carriage return');
        }

        $startPosition = $characters->key();
        // We have already check that the current is CR
        $characters->next();
        if (!$characters->valid()) {
            $characters->seek($startPosition);
            return false;
        }

        if (!$this->isLineFeed($characters->current())) {
            $characters->seek($startPosition);
            return false;
        }

        $characters->next();
        if (!$characters->valid()) {
            $characters->seek($startPosition);
            return false;
        }

        if (!$this->isWhiteSpace($characters->current())) {
            $characters->seek($startPosition);
            return false;
        }

        // can't use foreach statement as it runs StringIteratorInterface::rewind()
        // Find where folding ends
        while ($characters->valid()) {
            $character = $characters->current();

            if ($this->isNotWhiteSpaceOrControl($character)) {
                break;
            }

            $characters->next();
        }

        $endPosition = $characters->key();
        $characters->seek($endPosition - 1);
        $ref = $characters->getInnerString();
        $tokenString = new StringIterator($ref, $startPosition, $endPosition - $startPosition);

        return new Token($tokenString, TokenType::foldingWhiteSpace());
    }


    /**
     * WARNING: Moves the StringIterator::key() to the position of the last NO_WSP_CTL
     * @param StringIteratorInterface $characters
     * @return Token
     */
    private function seekNotWhiteSpaceOrControl(StringIteratorInterface &$characters)
    {
        if (!$this->isNotWhiteSpaceOrControl($characters->current())) {
            throw new \InvalidArgumentException('Iterator::current() must not start with whitespace or control character');
        }

        $startPosition = $characters->key();
        $count = 0;
        // can't use foreach statement as it runs StringIteratorInterface::rewind()
        while ($characters->valid()) {
            $character = $characters->current();

            if ($this->isQuotedPair($character)) {
                $startQuotedPair = $characters->key();
                // skip passed quoted pair (for comments in header fields)
                if (false !== ($pair = $this->seekClosingPair($characters, '"', '"'))) {
                    $firstKeyInPair = $pair->first();
                    $lastKeyInPair = $pair->last();
                    $skippedCharacters = $lastKeyInPair - $firstKeyInPair;
                    $characters->seek($lastKeyInPair);
                    $count += $skippedCharacters;
                } else {
                    $characters->seek($startQuotedPair);
                }
            }

            if (!$this->isNotWhiteSpaceOrControl($character)) {
                break;
            }

            // Even through this function should include specials
            // we need to detect them to help the higher level
            // parsers.
            if ($this->isSpecial($character)) {
                break;
            }

            ++$count;
            $characters->next();
        }

        $endPosition = $characters->key() - 1; // TODO: work out if this is where the header error is coming from
        $characters->seek($endPosition);

        return $this->tokenFrom($characters, $startPosition, $count, TokenType::notWhiteSpaceOrControl());
    }


    /**
     * This is just to keep the line length of the parse method to less than 120 characters
     * @param StringIterator $characters
     * @param int $offset
     * @param int $count
     * @param TokenType $tokenType
     * @return Token
     */
    public function tokenFrom(StringIterator $characters, $offset, $count, TokenType $tokenType)
    {
        return new Token(StringIterator::withStringIterator($characters, $offset, $count), $tokenType);
    }
}