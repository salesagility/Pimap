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

use SalesAgility\Imap\Response\MessageList;


/**
 * Interface FetchCommandInterface
 * @package SalesAgility\Imap\CommandBuilder\Commands
 */
interface FetchCommandInterface
{
    /**
     * The FETCH command retrieves data associated with a message in the
     * mailbox.
     * @param int|string $message
     * @return FetchCommandArgumentsInterface
     */
    public function fetch($message);

    /**
     * The FETCH command retrieves data associated with a message in the
     * mailbox.
     * @param int|string $messageFrom
     * @param int|string $messageTo
     * @return FetchCommandArgumentsInterface
     */
    public function fetchRange($messageFrom, $messageTo);

    /**
     * The FETCH command retrieves data associated with a message in the
     * mailbox.
     *
     * Typically used to fetch the messages from the output of a search command
     * @param MessageList $messages
     * @return FetchCommandArgumentsInterface
     */
    public function fetchSet(MessageList $messages);
}