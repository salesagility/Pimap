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


use SalesAgility\Imap\ImapException;
use SalesAgility\Imap\Pipeline\Pipeline;
use SalesAgility\Imap\Pipeline\PipeLineAwareInterface;
use SalesAgility\Imap\Pipeline\PipelineInterface;
use SalesAgility\Imap\CommandBuilder\CommandBuildArgumentsInterface;
use SalesAgility\Stream\StreamConnectionInterface;
use SalesAgility\Utility\Assert;
use SalesAgility\Utility\StringValue;

/**
 * Class MessageTransporter for imap protocol
 * @package SalesAgility\Imap
 *
 */
class MessageTransporter implements CommandTransporterInterface, PipeLineAwareInterface
{
    /** @var StreamConnectionInterface $connection */
    private $connection;

    /** @var Pipeline $pipeline */
    protected $pipeline;

    /**
     * @var CommandBuildArgumentsInterface
     */
    private $command = '';

    public function __construct($container)
    {
    }

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
        $message = '';
        while (!$this->isEndOfFile($message)) {
            $message = $this->connection->readMessage();
            if ($message === null) {
                break;
            }
            $response .= $message;
        }
        return $response;
    }

    /**
     * @param string $string
     * @return bool
     * @throws ImapException
     */
    public function isEndOfFile($string)
    {
        if ($string === null) {
            return true;
        }

        if ($this->hasTag($string)) {
            $response = str_replace($this->pipeline->getLastPipe()->getTag() . ' ', '', $string);
            if (StringValue::startsWith($response, 'OK')) {
                return true;
            } elseif (StringValue::startsWith($response, 'BAD')) {
                throw ImapException::BadResponse($string);
            } elseif (StringValue::startsWith($response, 'NO')) {
                throw ImapException::NoResponse($string);
            }
        }

        return false;
    }

    /**
     * @param PipelineInterface $pipeline
     */
    public function setPipeLine(PipelineInterface $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @throws \Exception
     */
    private function isConfigured()
    {
        Assert::is($this->connection !== null, 'Connection must be set');
        Assert::is($this->pipeline !== null, 'Pipeline must be set');
        return true;
    }

    /**
     * @param $message
     * @return bool
     */
    private function hasTag($message)
    {
        if (empty($this->command)) {
            $pattern = '/^[A\-]\d{1,}/';

            preg_match($pattern, $message, $matches);
            if (empty($matches) || count($matches) !== 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @throws \Exception
     */
    public function transmitCommand(CommandBuildArgumentsInterface $command)
    {
        $this->isConfigured();
        $this->pipeline->add($command);
        $tag = $this->pipeline->getLastPipe()->getTag();
        $strCommand = $command->tagged($tag)->asString();
        $this->transmit($strCommand . "\x0D\x0A");
    }
}