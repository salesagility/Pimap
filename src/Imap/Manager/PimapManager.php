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


namespace SalesAgility\Imap\Manager;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SalesAgility\Imap\Interpreter\ResponseInterpreter;
use SalesAgility\Imap\Interpreter\MailboxInterpreter;
use SalesAgility\Imap\Interpreter\MailboxListInterpreter;
use SalesAgility\Imap\Interpreter\MessageInterpreter;
use SalesAgility\Imap\Interpreter\SearchInterpreter;
use SalesAgility\Imap\Interpreter\StringIteratorInterpreter;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Imap\CommandBuilder\CommandBuildArgumentsInterface;
use SalesAgility\Imap\CommandBuilder\PimapCommandBuilder;
use SalesAgility\Imap\CommandBuilder\PimapSupportedTopLevelCommandsInterface;
use SalesAgility\Imap\Stream\CommandTransporterInterface;
use SalesAgility\Imap\Pipeline\PipeLineAwareInterface;
use SalesAgility\Imap\Pipeline\PipelineInterface;
use SalesAgility\Pattern\ContainerAwareInterface;
use SalesAgility\Utility\StringValue;

/**
 * Class PimapManager
 * @package SalesAgility\Imap\Manager
 */
class PimapManager implements ManagerInterface, ContainerAwareInterface, PipeLineAwareInterface, LoggerAwareInterface
{
    /** @var ContainerInterface $container */
    public $container;
    /** @var CommandTransporterInterface $transporter */
    private $transporter;
    /** @var LoggerInterface */
    private $log;
    /** @var PipelineInterface */
    private $pipeline;

    /**
     * PimapManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param CommandTransporterInterface $transporter
     */
    public function setTransporter(CommandTransporterInterface $transporter)
    {
        $this->transporter =& $transporter;
    }

    /**
     * @return CommandTransporterInterface
     */
    public function transporter()
    {
        return $this->transporter;
    }

    /**
     * @return PimapSupportedTopLevelCommandsInterface
     */
    public function command()
    {
        return PimapCommandBuilder::instance();
    }

    /**
     * @param CommandBuildArgumentsInterface $commandBuildArguments
     * @return mixed
     * @throws \SalesAgility\Imap\Token\TokenException
     * @throws \Exception
     */
    public function run(CommandBuildArgumentsInterface $commandBuildArguments)
    {
        switch ($commandBuildArguments->command()) {
            case 'UID':
                $responseMessage = $this->runCommand($commandBuildArguments);

                if (array_key_exists('FETCH', $commandBuildArguments->commandArguments())) {
                    $interpreter = new MessageInterpreter();
                } elseif (array_key_exists('SEARCH', $commandBuildArguments->commandArguments())) {
                    $interpreter = new SearchInterpreter();
                } else {
                    // COPY and STORE commands return a simple OK/BAD/NO response.
                    $tag = $this->pipeline->getLastPipe()->getTag();
                    $interpreter = new ResponseInterpreter();
                    return $interpreter->parse($tag, new StringIterator($responseMessage));
                }

                return $this->parseResponse($interpreter, $responseMessage);
            case 'FETCH':
                $responseMessage = $this->runCommand($commandBuildArguments);
                $interpreter = new MessageInterpreter();
                return $this->parseResponse($interpreter, $responseMessage);
            case 'SEARCH':
                $responseMessage = $this->runCommand($commandBuildArguments);
                $interpreter = new SearchInterpreter();
                return $this->parseResponse($interpreter, $responseMessage);
            case 'STATUS':
                $responseMessage = $this->runCommand($commandBuildArguments);
                $interpreter = new MailboxInterpreter();
                return $this->parseResponse($interpreter, $responseMessage);
            case 'SELECT':
                $responseMessage = $this->runCommand($commandBuildArguments);
                $interpreter = new MailboxInterpreter();
                return $this->parseResponse($interpreter, $responseMessage);
            case 'EXAMINE':
                $responseMessage = $this->runCommand($commandBuildArguments);
                $interpreter = new MailboxInterpreter();
                return $this->parseResponse($interpreter, $responseMessage);
            case 'LIST':
                $responseMessage = $this->runCommand($commandBuildArguments);
                $interpreter = new MailboxListInterpreter();
                return $this->parseResponse($interpreter, $responseMessage);
            case 'LSUB':
                $responseMessage = $this->runCommand($commandBuildArguments);
                $interpreter = new MailboxListInterpreter();
                return $this->parseResponse($interpreter, $responseMessage);
            case 'IDLE':
                $responseMessage = $this->runIDLE();
                $interpreter = new MailboxInterpreter();
                return $interpreter->parse(new StringIterator($responseMessage));
            default:
                $responseMessage = $this->runCommand($commandBuildArguments);
                $tag = $this->pipeline->getLastPipe()->getTag();
                $interpreter = new ResponseInterpreter();
                return $interpreter->parse($tag, new StringIterator($responseMessage));
        }
    }

    /**
     * @param PipelineInterface $pipeline
     */
    public function setPipeLine(PipelineInterface $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @return PipelineInterface
     */
    public function pipeline()
    {
        return $this->pipeline;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->log = $logger;
    }

    /**
     * @param CommandBuildArgumentsInterface $commandBuildArguments
     * @return bool|string
     * @throws \Exception
     */
    private function runCommand(CommandBuildArgumentsInterface $commandBuildArguments)
    {
        $this->transporter->transmitCommand($commandBuildArguments);
        $response = $this->transporter->receive();
        $this->pipeline->getLastPipe()->addResponse($response);
        return $response;
    }

    /**
     * Custom handling for IDLE command
     * @see https://tools.ietf.org/html/rfc2177
     * @return string
     * @throws \Exception
     */
    private function runIDLE()
    {
        $command = $this->command()->idle()->build();
        $this->pipeline->add($command);
        $tag = $this->pipeline->getLastPipe()->getTag();
        $strCommand = $command->tagged($tag)->asString();
        $this->transporter->transmit($strCommand . "\x0D\x0A");

        // wait for idling response
        $response = '';
        while (true) {
            $response .= $this->transporter->connection()->readMessage();
            if (StringValue::startsWith($response, '+ idling')) {
                break;
            }
        }

        // wait for message response
        while (true) {
            $responseCurrent = $this->transporter->connection()->readMessage();
            $response .= $responseCurrent;
            if (StringValue::startsWith($responseCurrent, '*')
                && StringValue::endsWith($responseCurrent, 'EXISTS' . "\r\n")
            ) {
                break;
            }
        }

        // Complete IDLE and wait for OK response
        $this->transporter->transmit('DONE' . "\x0D\x0A");
        while (!$this->transporter->isEndOfFile('')) {
            $message = $this->transporter->connection()->readMessage();
            if ($message === null) {
                break;
            }
            $response .= $message;
        }

        $this->transporter->connection()->readMessage();

        return $response;
    }

    /**
     * Keeps this class DRY
     * @param StringIteratorInterpreter $interpreter
     * @param $responseMessage
     * @return mixed
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    private function parseResponse(StringIteratorInterpreter $interpreter, $responseMessage)
    {
        $tag = $this->pipeline->getLastPipe()->getTag();
        $responseInterpreter = new ResponseInterpreter();
        $response = $responseInterpreter->parse($tag, new StringIterator($responseMessage));
        $parsed = $interpreter->parse($response->included());
        $this->pipeline->getLastPipe()->addParsed($parsed);
        return $parsed;
    }

}