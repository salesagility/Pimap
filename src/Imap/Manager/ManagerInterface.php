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

use SalesAgility\Imap\CommandBuilder\PimapSupportedTopLevelCommandsInterface;
use SalesAgility\Imap\Response\MessageList;
use SalesAgility\Imap\CommandBuilder\CommandBuildArgumentsInterface;
use SalesAgility\Imap\CommandBuilder\PhpImapExtensionSupportedTopLevelCommandsInterface;
use SalesAgility\Imap\Stream\CommandTransporterInterface;

/**
 * Interface ManagerInterface
 * @package SalesAgility\Imap\Manager
 */
interface ManagerInterface
{
    /**
     * @param CommandTransporterInterface $connection
     */
    public function setTransporter(CommandTransporterInterface $connection);

    /**
     * @return CommandTransporterInterface
     */
    public function transporter();

    /**
     * @return PimapSupportedTopLevelCommandsInterface|PhpImapExtensionSupportedTopLevelCommandsInterface
     */
    public function command();

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return MessageList
     */
    public function run(CommandBuildArgumentsInterface $command);
}