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

use Psr\Container\ContainerInterface;
use SalesAgility\Pattern\ContainerAwareInterface;
use SalesAgility\Utility\Assert;

/**
 * Class Connection
 * @package SalesAgility\Stream
 */
class Connection implements StreamConnectionInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;
    /**
     * Constants for setting encryption
     * @see Connection::enableEncryption();
     */
    const TLS = STREAM_CRYPTO_METHOD_TLS_CLIENT;

    /** @var null|Resource $resource */
    private $resource = null;

    /** @var int $connectionTimeout */
    private $connectionTimeout = 30;

    /** @var int $connectionTTL */
    private $connectionTTL = 1800;

    /** @var string $connectionString */
    private $connectionString;

    /** @var int $security */
    private $security;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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
    public function enableEncryption($security = STREAM_CRYPTO_METHOD_TLS_CLIENT)
    {
        $this->security = $security;
    }

    /**
     *
     */
    public function disableEncryption()
    {
        $this->security = false;
    }

    /**
     * @throws ConnectionException
     */
    public function connect()
    {
        $errorNumber = null;
        $errorString = null;
        set_time_limit($this->connectionTTL);
        ignore_user_abort(true);

        if (empty($this->connectionString)) {
            throw ConnectionException::connectionFailure('Connection String is empty. expected tcp://address:port');
        }

        $this->resource = stream_socket_client($this->connectionString, $errorNumber, $errorString, $this->connectionTimeout);


        if (empty($this->resource)) {
            throw ConnectionException::connectionFailure('Unable to connect to ' . $this->connectionString);
        }

        if (!empty($this->security)) {
            try {
                stream_socket_enable_crypto($this->resource, true, $this->security);
            } catch (\Exception $e) {
                $this->container->get('Logger')->error($e->getMessage());
                throw ConnectionException::connectionFailure('Failed to enable security');
            }
        }
    }

    /**
     *
     */
    public function disconnect()
    {
        fclose($this->resource);
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
     * @throws \Exception
     */
    public function transmitMessage($message)
    {
        Assert::is(gettype($message) === 'string', 'message must be a string');
        Assert::is(!empty($message), 'message must not be empty');
        fwrite($this->resource, $message);
    }

    /**
     * @return bool|string
     */
    public function readMessage()
    {
        return fgets($this->resource, 1000);
    }

    /**
     * @param string $string
     * @return bool
     */
    public function isEndOfFile($string = "")
    {
        return feof($this->resource);
    }
}