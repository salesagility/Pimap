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

use SalesAgility\Utility\Assert;

/**
 * Class MessageTransporter
 * @package SalesAgility\Stream
 * Maintains a protocol link between the consumer and the connection
 * Relays Messages
 * Determines the end of file flag as not all protocols use eof to signify the end of a transmission
 */
class MessageTransporter implements MessageTransporterInterface
{
    /** @var string $command */
    private $command;

    /** @var StreamConnectionInterface $connection */
    private $connection;

    /** @var int $waitFor */
    private $TTL = 160;

    /**
     * @param StreamConnectionInterface $connection
     */
    public function setConnection(StreamConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return StreamConnectionInterface
     */
    public function connection()
    {
        return $this->connection;
    }

    /**
     * @param string $string
     * @throws \Exception
     */
    public function transmit($string)
    {
        $this->isConfigured();
        $this->command = null;
        $this->connection->transmitMessage($string);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function receive()
    {
        $this->isConfigured();
        $response = '';
        $timeout = time() + $this->TTL;
        while (time() < $timeout) {
            $message = $this->connection->readMessage();
            $timeout = time() + $this->TTL;
            $response .= $message;

            if ($this->isEndOfFile($message)) {
                break;
            }
        }
        return $response;
    }

    /**
     * @param string $string
     * @return bool
     */
    public function isEndOfFile($string)
    {
        return $this->connection->isEndOfFile($string);
    }

    /**
     * @throws \Exception
     */
    private function isConfigured()
    {
        Assert::is($this->connection !== null, 'Connection must be set');
        return true;
    }
}