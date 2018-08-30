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
 * Interface UnsubscribeCommandInterface
 * @package SalesAgility\Imap\CommandBuilder\Commands
 */
interface UnsubscribeCommandInterface
{
    /**
     * Removes the specified mailbox name to the
     * server's set of "active" or "subscribed" mailboxes as returned by
     * the LSUB command.
     * @param string $mailbox eg. Important
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface
     */
    public function unsubscribe($mailbox);
}