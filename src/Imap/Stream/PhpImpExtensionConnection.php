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


namespace SalesAgility\Imap\Stream;


use Psr\Log\LoggerInterface;
use SalesAgility\Stream\StreamConnectionInterface;

/**
 * Class PhpImpExtensionConnection
 * @package SalesAgility\Imap\Stream
 * The native php extension handles it own connection and transport
 */
class PhpImpExtensionConnection implements StreamConnectionInterface
{
    public $connection;
    public $server = '';
    public $port = '';
    public $security = '';
    public $username = '';
    public $password = '';
    public $mailbox = 'INBOX';
    /** @var LoggerInterface */
    private $log;

    public function __construct($container)
    {
    }

    /**
     * @param string $string
     * @throws \Exception
     */
    public function setConnectionString($string)
    {
        if (strpos($string, '{') !== false) {
            throw new \Exception('Expected tcp://hostname:portnumber');
        }

        $string = str_replace('tcp://', '', $string);
        $string = str_replace('/', '', $string);
        $opt = explode(':', $string);
        $this->server = $opt[0];
        $this->port = $opt[1];
    }

    /**
     * @param string $security
     */
    public function enableEncryption($security = '/imap/ssl/novalidate-cert')
    {
        $this->security = $security;
    }

    public function disableEncryption()
    {
        $this->security = '';
    }

    /**
     * @return bool
     */
    public function connect()
    {
        if ($this->username === '') {
            // wait for login command
            $this->connection = 1;
            return false;
        }
        $mailbox = '{' . $this->server . ':' . $this->port . $this->security . '}' . $this->mailbox;
        $this->connection = imap_open($mailbox, $this->username, $this->password, OP_HALFOPEN);
        return true;
    }

    public function disconnect()
    {
        if (!empty($this->connection) and $this->connection !== 1) {
            imap_close($this->connection);
        }

        $this->connection = null;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->connection !== null;
    }

    /**
     * @param $message
     * @throws \Exception
     */
    public function transmitMessage($message)
    {
        throw new \Exception('Not supported');
    }

    /**
     * @return bool|string|void
     * @throws \Exception
     */
    public function readMessage()
    {
        throw new \Exception('Not supported');
    }

    /**
     * @param string $string
     * @return bool|void
     * @throws \Exception
     */
    public function isEndOfFile($string = "")
    {
        throw new \Exception('Not supported');
    }
}