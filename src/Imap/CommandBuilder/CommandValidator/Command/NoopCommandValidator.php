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

namespace SalesAgility\Imap\CommandBuilder\CommandValidator\Command;


use SalesAgility\Imap\CommandBuilder\CommandBuildArgumentsInterface;
use SalesAgility\Imap\CommandBuilder\CommandValidator\CommandValidationInterface;
use SalesAgility\Utility\Assert;

/**
 * Class NoopCommandValidator
 * @package SalesAgility\Imap\CommandBuilder\CommandValidator\Command
 */
class NoopCommandValidator implements CommandValidationInterface
{
    /**
     * @return string
     */
    public function command()
    {
        return 'NOOP';
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return bool
     * @throws \Exception
     */
    public function validate(CommandBuildArgumentsInterface $command)
    {
        Assert::is(empty($command->commandArguments()), 'NOOP does not take any arguments.');
        Assert::is($command->command() === 'NOOP', 'NOOP validation failed.');
        return true;
    }
}