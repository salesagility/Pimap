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


namespace SalesAgility\Imap\CommandBuilder\Commands;

/**
 * Interface StoreCommandArgumentsInterface
 * @package SalesAgility\Imap\CommandBuilder\Commands
 */
interface StoreCommandArgumentsInterface
{
    /**
     * @param string $message
     * @return StoreCommandArgumentsInterface|\SalesAgility\Imap\CommandBuilder\CommandBuildInterface
     */
    public function withMessage($message);

    /**
     * @param $messageFrom
     * @param $messageTo
     * @return StoreCommandArgumentsInterface|\SalesAgility\Imap\CommandBuilder\CommandBuildInterface
     */
    public function withRange($messageFrom, $messageTo);

    /**
     * Replace the flags for the message (other than \Recent) with the
     * argument.  The new value of the flags is returned as if a FETCH
     * of those flags was done.
     * @param string $flag
     * @return StoreCommandArgumentsInterface|\SalesAgility\Imap\CommandBuilder\CommandBuildInterface
     */
    public function replaceFlag($flag);

    /**
     * Add the argument to the flags for the message.  The new value
     * of the flags is returned as if a FETCH of those flags was done.
     * @param string $flag
     * @return StoreCommandArgumentsInterface|\SalesAgility\Imap\CommandBuilder\CommandBuildInterface
     */
    public function addFlag($flag);

    /**
     * Remove the argument from the flags for the message.  The new
     * value of the flags is returned as if a FETCH of those flags was
     * done.
     * @param string $flag
     * @return StoreCommandArgumentsInterface|\SalesAgility\Imap\CommandBuilder\CommandBuildInterface
     */
    public function removeFlag($flag);
}