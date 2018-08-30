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

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use SalesAgility\Utility\Assert;

/**
 * Class MockConnection
 * @package SalesAgility\Stream
 */
class MockConnection implements StreamConnectionInterface, LoggerAwareInterface
{
    /**
     * Constants for setting encryption
     * @see Connection::enableEncryption();
     */
    const TLS = STREAM_CRYPTO_METHOD_TLS_CLIENT;

    /** @var null|Resource $resource */
    private $resource = null;

    /** @var string $connectionString */
    private $connectionString;

    /** @var int $security */
    private $security;

    /** @var LoggerInterface */
    private $log;

    /** @var string */
    public $messageSent = '';

    /** @var string[] $messageReceived */
    public $messageReceived = array(
        '* OK mock server v1.0',
        'some header',
        'some body',
    );

    /** @var int $messagePosition */
    private $messagePosition = 0;

    /** @var int $messageLast */
    private $messageLast = 3;

    /**
     * @param string $string eg tcp://localhost:143
     * @throws \Exception
     */
    public function setConnectionString($string)
    {
        Assert::is(gettype($string) === 'string', 'connection string must be a string');
        Assert::is(!empty($string), 'connection string must not be empty');
        $this->connectionString = $string;
    }

    /**
     * @param $security
     */
    public function enableEncryption($security)
    {
        $this->security = STREAM_CRYPTO_METHOD_TLS_CLIENT;
    }

    /**
     *
     */
    public function disableEncryption()
    {
        $this->security = false;
    }

    /**
     */
    public function connect()
    {
        $this->resource = 1;
    }

    /**
     *
     */
    public function disconnect()
    {
        $this->resource = null;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->resource !== null;
    }

    /**
     * @param $message
     * @return void
     */
    public function transmitMessage($message)
    {
        $this->messageSent .= $message;
    }

    /**
     * @return bool|string
     */
    public function readMessage()
    {
        if ($this->messagePosition >= count($this->messageReceived)) {
            return null;
        }

        $msg = $this->messageReceived[$this->messagePosition];
        ++$this->messagePosition;
        return $msg;
    }

    /**
     * @param string $string
     * @return bool
     */
    public function isEndOfFile($string = "")
    {
        return $this->messagePosition >= $this->messageLast;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->log = $logger;
    }
}