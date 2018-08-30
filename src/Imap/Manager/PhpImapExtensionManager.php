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
use SalesAgility\Imap\CommandBuilder\CommandBuildArgumentsInterface;
use SalesAgility\Imap\CommandBuilder\PhpImapExtensionCommandBuilder;
use SalesAgility\Imap\Pipeline\Pipeline;
use SalesAgility\Imap\Pipeline\PipeLineAwareInterface;
use SalesAgility\Imap\Pipeline\PipelineInterface;
use SalesAgility\Imap\Stream\CommandTransporterInterface;
use SalesAgility\Imap\Stream\PhpImapExtensionMessageTransporter;
use SalesAgility\Pattern\ContainerAwareInterface;

/**
 * Class PhpImapExtensionManager
 * @package SalesAgility\Imap\Manager
 */
class PhpImapExtensionManager implements ManagerInterface, ContainerAwareInterface, PipeLineAwareInterface, LoggerAwareInterface
{
    /** @var LoggerInterface $log */
    private $log;
    /** @var ContainerInterface $container */
    private $container;
    /** @var PhpImapExtensionMessageTransporter */
    private $transporter;
    /** @var Pipeline $pipeline */
    private $pipeline;

    /**
     * PhpImapExtensionManager constructor.
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
        $this->transporter = $transporter;
    }

    /**
     * @return CommandTransporterInterface|PhpImapExtensionMessageTransporter
     */
    public function transporter()
    {
        return $this->transporter;
    }

    /**
     * @return \SalesAgility\Imap\CommandBuilder\PhpImapExtensionSupportedTopLevelCommandsInterface
     */
    public function command()
    {
        return PhpImapExtensionCommandBuilder::instance();
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return \SalesAgility\Imap\Response\Response
     * @throws \ErrorException
     * @throws \SalesAgility\Imap\ImapException
     */
    public function run(CommandBuildArgumentsInterface $command)
    {
        return $this->transporter->transmitCommand($command);
    }

    /**
     * @return Pipeline
     */
    public function pipeline()
    {
        return $this->pipeline;
    }

    /**
     * @param PipelineInterface $pipeline
     */
    public function setPipeLine(PipelineInterface $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->log = $logger;
    }
}