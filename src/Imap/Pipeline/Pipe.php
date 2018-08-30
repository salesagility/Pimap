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

use SalesAgility\Imap\Lexeme\LexemeList;
use SalesAgility\Imap\Response\MessageList;
use SalesAgility\Imap\Token\TokenList;
use SalesAgility\Imap\CommandBuilder\CommandBuildArgumentsInterface;
use SalesAgility\Utility\Assert;

/**
 * Class Pipe
 * @package SalesAgility\Imap
 */
class Pipe implements PipeInterface
{
    /**
     * @var string tag
     * should only be assigned once
     */
    private $tag;

    /**
     * @var CommandBuildArgumentsInterface command
     * should only be assigned once
     */
    private $command;

    /**
     * @var string[] $response
     */
    private $response;

    /**
     * @var array
     */
    private $parsed;

    /** @var TokenList $tokenList */
    private $tokenList;

    /** @var LexemeList $lexemeList */
    private $lexemeList;

    /** @var MessageList $messageList */
    private $messageList;

    /**
     * Pipe constructor.
     * @param string $tag
     * @param CommandBuildArgumentsInterface $command
     * @throws \Exception
     */
    public function __construct($tag, CommandBuildArgumentsInterface $command)
    {
        Assert::is(gettype($tag) === 'string', '$tag must be a string eg A1');
        Assert::is(!empty($tag), '$tag must not be empty');

        $this->tag = $tag;
        $command->tagged($tag);
        $this->command = $command;
        $this->response = array();
        $this->parsed = array();
    }

    /**
     * @param $response
     * @throws \Exception
     */
    public function addResponse($response)
    {
        Assert::is(gettype($response) === 'string', '$response must be a string eg A1 OK');
        $this->response[] = $response;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return implode('', $this->response);
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return CommandBuildArgumentsInterface
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function buildCommand()
    {
        return $this->tag . "\x20" . $this->command->command();
    }

    /**
     * @param TokenList $tokenList
     */
    public function addTokenList(TokenList $tokenList)
    {
        $this->tokenList = $tokenList;
    }

    /**
     * @return bool
     */
    public function isTokenized()
    {
        return $this->tokenList !== null;
    }

    /**
     * @return mixed|TokenList
     */
    public function tokenList()
    {
        return $this->tokenList;
    }

    /**
     * @param LexemeList $lexemeList
     */
    public function addLexemeList(LexemeList $lexemeList)
    {
        $this->lexemeList = $lexemeList;
    }

    /**
     * @return bool
     */
    public function isLexemized()
    {
        return $this->lexemeList !== null;
    }

    /**
     * @return mixed|LexemeList
     */
    public function lexemeList()
    {
        return $this->lexemeList;
    }

    /**
     * @param $parsedContent
     */
    public function addParsed($parsedContent)
    {
        $this->parsed = $parsedContent;
    }

    /**
     * @return array
     */
    public function parsed()
    {
        return $this->parsed;
    }
}