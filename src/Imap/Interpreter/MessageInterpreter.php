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

use SalesAgility\Imap\Interpreter\LexemeInterpreter;
use SalesAgility\Imap\Interpreter\OctetInterpreter;
use SalesAgility\Imap\Interpreter\Rfc2822Interpreter;
use SalesAgility\Imap\Lexeme\LexemeList;
use SalesAgility\Imap\Lexeme\LexemeType;
use SalesAgility\Imap\Lexeme\Lexemizer;
use SalesAgility\Imap\Response\Message;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Imap\Token\Tokenizer;
use SalesAgility\Utility\Assert;
use SalesAgility\Imap\Response\MessageList;
use SalesAgility\Imap\Response\MessageFactory;

/**
 * Class MessageInterpreter
 * @package SalesAgility\Imap\Interpreter
 */
class MessageInterpreter implements StringIteratorInterpreter
{
    /** @var LexemeInterpreter $lexeme */
    private $lexeme;

    /** @var OctetInterpreter $octet */
    private $octet;

    /** @var Rfc2822Interpreter $rfc2822 */
    private $rfc2822;

    /** @var Tokenizer $tokenizer */
    private $tokenizer;

    /** @var Lexemizer $lexemizer */
    private $lexemizer;

    /**
     * MessageInterpreter constructor.
     */
    public function __construct()
    {
        $this->lexeme = new LexemeInterpreter();
        $this->octet = new OctetInterpreter();
        $this->rfc2822 = new Rfc2822Interpreter();
        $this->tokenizer = new Tokenizer();
        $this->lexemizer = new Lexemizer();
    }

    /**
     * @param StringIterator $message
     * @return \SalesAgility\Imap\Response\MessageList
     * @throws \SalesAgility\Imap\Token\TokenException
     * @throws \Exception
     */
    public function parse(StringIterator $message)
    {
        $tokens = $this->tokenizer->parse($message);
        $lexemes = $this->lexemizer->parse($tokens);
        $messages = new MessageList();

        while ($lexemes->valid()) {
            $messages[] = $this->seekMessage($lexemes);
        }

        return $messages;
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    private function isMessage(LexemeList &$lexemes)
    {
        $test = function (LexemeList &$lexemes) {
            return $lexemes->current()->offsetGet(0)->toString() === '*';
        };

        if ($this->lexeme->isNewLine($lexemes)) {
            $this->seekNextLexeme($lexemes);
            if (!$lexemes->valid()) {
                return false;
            }
            return $test($lexemes);
        } elseif ($lexemes->key() === 0) {
            return $test($lexemes);
        } elseif ($lexemes->offsetGet($lexemes->key() - 1)->hasType(LexemeType::newLine())) {
            return $test($lexemes);
        }

        return false;
    }


    /**
     * @param LexemeList $lexemes
     * @param Message $message
     * @return bool
     * @throws \Exception
     */
    private function parseKeyword(LexemeList &$lexemes, Message &$message)
    {
        $keywords = $lexemes->current()->toString();
        switch ($keywords) {
            case "FETCH":
                $this->seekNextLexeme($lexemes);
                $this->seekMessageNumber($lexemes, $message);

                $this->seekWhitespace($lexemes);

                Assert::is($this->lexeme->isGroup($lexemes), 'Message Interpreter: expected a group but found ' . $lexemes->current()->toString());
                $lastLexemeInGroup = $this->lexeme->lastInGroup($lexemes);
                $this->seekNextLexeme($lexemes);

                while ($lexemes->valid() && $lexemes->key() < $lastLexemeInGroup) {
                    Assert::is($this->lexeme->isKeyword($lexemes), 'Message Interpreter: expected a keyword but found ' . $lexemes->current()->toString());
                    $this->parseFetchKeyword($lexemes, $message);

                    // skip extra new lines and whitespace
                    $this->seekNoise($lexemes);
                }
                return true;
            default:
                break;
        }

        return false;
    }

    /**
     * @param LexemeList $lexemes
     * @param Message $message
     * @return bool
     * @throws \Exception
     */
    private function parseFetchKeyword(LexemeList &$lexemes, Message &$message)
    {
        $keywords = $lexemes->current()->toString();
        switch ($keywords) {
            case "UID":
                $this->seekNextLexeme($lexemes);
                $this->seekWhitespace($lexemes);
                Assert::is($this->lexeme->isNumber($lexemes), 'Message Interpreter: expected a number');
                $message['uid'] = $lexemes->current()->toString();
                $this->seekNextLexeme($lexemes);
                $this->seekWhitespace($lexemes);
                $this->skipNewLines($lexemes);

                break;
            case "FLAGS":
                $this->seekNextLexeme($lexemes);
                $this->seekWhitespace($lexemes);

                Assert::is($this->lexeme->isGroup($lexemes), 'Message Interpreter: expected a group of flags');
                $lastCharacterOfGroup = $lexemes->current()->offsetGet(0)->lastKey();
                $this->seekNextLexeme($lexemes);


                while ($lexemes->valid()) {
                    $this->seekWhitespace($lexemes);

                    Assert::is($this->lexeme->isFlag($lexemes), 'Message Interpreter: expected a escape character to mark the flag');
                    $this->seekNextLexeme($lexemes);

                    Assert::is($this->lexeme->isAtom($lexemes), 'Message Interpreter: expected atom');
                    $message['flags'][$lexemes->current()->toString()] = true;
                    $this->seekNextLexeme($lexemes);

                    if ($lexemes->current()->offsetGet(0)->firstKey() >= $lastCharacterOfGroup) {
                        $this->seekWhitespace($lexemes);
                        $this->skipNewLines($lexemes);
                        return true;
                    }
                    $this->seekNextLexeme($lexemes);
                }
                $this->skipNewLines($lexemes);
                break;
            case "BODY":
                /** @see https://tools.ietf.org/html/rfc3501#page-54 */
                $this->seekNextLexeme($lexemes);
                $this->seekWhitespace($lexemes);

                if ($this->lexeme->isOptional($lexemes)) {
                    // BODY[section]
                    $section = $this->parseFetchBodySection($lexemes, $message);

                    if ($section !== null) {
                        // skip new lines at the end of the fetch
                        $this->skipNewLines($lexemes);
                        return $section;
                    }
                }
                break;
            case "BODYSTRUCTURE":
                $bodyStructure = $this->parseFetchBodyStructure($lexemes, $message);
                if ($bodyStructure !== null) {
                    if ($lexemes->current()->toString() === ')') {
                        $lexemes->next();
                    }
                    $this->seekWhitespace($lexemes);
                    $this->skipNewLines($lexemes);
                    return $bodyStructure;
                }
                break;
            default:
                throw new \Exception('Message Interpreter: unsupported keyword');
        }

        return null;
    }

    /**
     * @param LexemeList $lexemes
     * @param Message $message
     * @return bool|null
     * @throws \Exception
     */
    private function parseFetchBodyStructure(LexemeList &$lexemes, Message &$message)
    {
        // Quoted-Printable Content-Transfer-Encoding
        /** @see https://tools.ietf.org/html/rfc2045#page-19 */
        $this->seekNextLexeme($lexemes);
        $this->seekWhitespace($lexemes);

        if ($this->lexeme->isGroup($lexemes)) {
            $lastCharacterOfGroup = $lexemes->current()->offsetGet(0)->lastKey();
            $this->seekNextLexeme($lexemes);
        } else {
            throw new \Exception('Message Interpreter: expected a number');
        }

        $htmlBodyFound = false;
        while ($lexemes->valid()) {
            // Detect
            // is Plain?
            // Is html?
            // Has Attachments
            //
            // MIME Part contains:
            //  Type
            //  Charset
            //  Boundary
            //  Transfer Encoding
            //  Length
            //  Lines
            if ($lexemes->current()->offsetGet(0)->type()->isQuoted()) {
                if (strtolower($lexemes->current()->toString()) === '"text"') {
                    $this->seekNextLexeme($lexemes);
                    $this->seekWhitespace($lexemes);

                    if (strtolower($lexemes->current()->toString()) === '"plain"') {
                        $message['body']['structure']->offsetSet('plain', true);
                    } elseif (strtolower($lexemes->current()->toString()) === '"html"') {
                        // scan ahead by up 9 tokens test if html is an attachment
                        // the "NAME" keyword is a good indicator that it is an attachment
                        $scanTo = 10;
                        $isAttachment = false;
                        while ($lexemes->valid() && $scanTo > 0) {
                            if ($lexemes->current()->offsetGet(0)->type()->isQuoted()) {
                                if (strtolower($lexemes->current()->toString()) === '"name"') {
                                    $isAttachment = true;
                                    break;
                                }
                            }

                            --$scanTo;
                            $this->seekNextLexeme($lexemes);
                        }

                        if ($isAttachment || $htmlBodyFound) {
                            $message['body']['structure']->offsetSet('attachments', true);
                        } else {
                            $message['body']['structure']->offsetSet('html', true);
                            $htmlBodyFound = true;
                        }
                    }
                } elseif (strtolower($lexemes->current()->toString()) === '"image"') {
                    $message['body']['structure']->offsetSet('attachments', true);
                } elseif (strtolower($lexemes->current()->toString()) === '"audio"') {
                    $message['body']['structure']->offsetSet('attachments', true);
                } elseif (strtolower($lexemes->current()->toString()) === '"video"') {
                    $message['body']['structure']->offsetSet('attachments', true);
                } elseif (strtolower($lexemes->current()->toString()) === '"application"') {
                    $message['body']['structure']->offsetSet('attachments', true);
                } elseif (strtolower($lexemes->current()->toString()) === '"attachment"') {
                    $message['body']['structure']->offsetSet('attachments', true);
                }
            }

            if ($lexemes->current()->offsetGet(0)->firstKey() >= $lastCharacterOfGroup) {
                return true;
            }
            $this->seekNextLexeme($lexemes);
        }

        return null;
    }

    /**
     * @param LexemeList $lexemes
     * @param Message $message
     * @return bool|null
     * @throws \Exception
     */
    private function parseFetchBodySection(LexemeList &$lexemes, Message &$message)
    {
        $keywords = $lexemes->current()->toString();
        switch ($keywords) {
            case "[HEADER]":
                return $this->rfc2822->parseHeader($lexemes, $message);
            case "[TEXT]":
                return $this->rfc2822->parseBody($lexemes, $message);
            case "TEXT":
                return $this->rfc2822->parseBody($lexemes, $message);
            default:
                throw new \Exception('Message Interpreter: unsupported keyword');
        }
    }


    /**
     * Provides offset protection
     * Calculates the elapsed $octetCount
     * @param LexemeList $lexemes
     */
    private function seekNextLexeme(LexemeList &$lexemes)
    {
        if ($lexemes->offsetExists($lexemes->key() + 1)) {
            if (!$this->lexeme->isGroup($lexemes)) {
                $this->octet->add($lexemes->current()->octetCount());
            }
        }

        $lexemes->next();
    }

    /**
     * @param LexemeList $lexemes
     * @return bool|Message
     * @throws \Exception
     */
    /**
     * @param LexemeList $lexemes
     * @return bool|Message
     * @throws \Exception
     */
    private function seekMessage(LexemeList &$lexemes)
    {
        Assert::is($this->isMessage($lexemes), 'Message Interpreter: unable to determine the start of message');
        // CRLF* 1 KEYWORD
        $this->seekNextLexeme($lexemes);
        $this->seekWhitespace($lexemes);
        $this->seekNumber($lexemes);
        $this->seekWhitespace($lexemes);
        $message = $this->seekKeyword($lexemes);

        // Ignore CRLF
        while ($lexemes->valid()) {
            if (!$lexemes->current()->hasType(LexemeType::newLine())) {
                break;
            }
            $lexemes->next();
        }

        return $message;
    }

    /**
     * @param LexemeList $lexemes
     * @return bool|Message
     * @throws \Exception
     */
    /**
     * @param LexemeList $lexemes
     * @return bool|Message
     * @throws \Exception
     */
    private function seekKeyword(LexemeList &$lexemes)
    {
        if ($this->lexeme->isKeyword($lexemes)) {
            $message = MessageFactory::instance();
            $this->parseKeyword($lexemes, $message);
            Assert::is($message !== false, 'Message Interpreter: keyword is unsupported/invalid');
            return $message;
        }

        return false;
    }

    /**
     * @param LexemeList $lexemes
     * @param Message $message
     */
    /**
     * @param LexemeList $lexemes
     * @param Message $message
     */
    private function seekMessageNumber(LexemeList &$lexemes, Message &$message)
    {
        $message['number'] = $lexemes->offsetGet($lexemes->key() - 3)->toString();
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    private function seekWhitespace(LexemeList &$lexemes)
    {
        if ($this->lexeme->isWhitespace($lexemes)) {
            $this->seekNextLexeme($lexemes);
        }

        return false;
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    private function seekNumber(LexemeList &$lexemes)
    {
        if ($this->lexeme->isNumber($lexemes)) {
            $this->seekNextLexeme($lexemes);
        }

        return false;
    }

    /**
     * Skip extra whitespace, newlines folding lines ect.
     * @param LexemeList $lexemes
     */
    private function seekNoise(LexemeList &$lexemes)
    {
        while ($lexemes->valid()) {

            $isNoise = $lexemes->current()->hasType(LexemeType::newLine())
                || $lexemes->current()->hasType(LexemeType::whitespace());

            if ($isNoise) {
                $lexemes->next();
            } else {
                break;
            }
        }
    }

    /**
     * @param LexemeList $lexemes
     */
    /**
     * @param LexemeList $lexemes
     */
    public function skipNewLines(LexemeList & $lexemes)
    {
        // skip new lines at the end of the fetch
        while ($lexemes->valid()) {
            if (!$lexemes->current()->hasType(LexemeType::newLine())) {
                break;
            }
            $this->seekNextLexeme($lexemes);
        }
    }
}
