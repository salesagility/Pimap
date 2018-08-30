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

namespace SalesAgility\Stream;


use Psr\Log\LoggerAwareInterface;

/**
 * Interface StreamConnectionInterface
 * @package SalesAgility\Stream
 */
interface StreamConnectionInterface
{
    /**
     * @param string $string eg tcp://localhost:143
     * @throws \Exception
     */
    public function setConnectionString($string);

    /**
     * @param $security
     */
    public function enableEncryption($security);

    /**
     *
     */
    public function disableEncryption();

    /**
     * @throws \ErrorException
     */
    public function connect();

    /**
     *
     */
    public function disconnect();

    /**
     * @return bool
     */
    public function isConnected();

    /**
     * @param $message
     * @throws \Exception
     */
    public function transmitMessage($message);

    /**
     * @return bool|string
     */
    public function readMessage();

    /**
     * @return bool
     */
    public function isEndOfFile($string = "");
}