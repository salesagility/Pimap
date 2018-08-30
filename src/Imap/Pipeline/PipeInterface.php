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
use SalesAgility\Imap\Token\TokenList;
use SalesAgility\Imap\Response\MessageList;
use SalesAgility\Imap\CommandBuilder\CommandBuildArgumentsInterface;

/**
 * Interface PipeInterface
 * @package SalesAgility\Imap\Pipeline
 */
interface PipeInterface
{
    /**
     * Pipe constructor.
     * @param string $tag
     * @param CommandBuildArgumentsInterface $command
     */
    public function __construct($tag, CommandBuildArgumentsInterface $command);

    /**
     * @param string $response
     * @throws \Exception
     */
    public function addResponse($response);

    /**
     * @return string
     */
    public function getTag();

    /**
     * @return CommandBuildArgumentsInterface
     */
    public function getCommand();

    /**
     * @return string[]
     */
    public function getResponse();

    /**
     * @return string full command including tag
     */
    public function buildCommand();

    /**
     * @param TokenList $tokenList
     * @return void
     */
    public function addTokenList(TokenList $tokenList);

    /**
     * @return bool
     */
    public function isTokenized();

    /**
     * @return mixed
     */
    public function tokenList();

    /**
     * @param LexemeList $lexemeList
     * @return void
     */
    public function addLexemeList(LexemeList $lexemeList);

    /**
     * @return bool
     */
    public function isLexemized();

    /**
     * @return mixed
     */
    public function lexemeList();

    /**
     * @param $parsedContent
     */
    public function addParsed($parsedContent);

    /**
     * @return mixed
     */
    public function parsed();

}