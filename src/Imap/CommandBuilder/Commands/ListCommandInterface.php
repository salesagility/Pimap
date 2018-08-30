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
 * Interface ListCommandInterface
 * @package SalesAgility\Imap\CommandBuilder\Commands
 */
interface ListCommandInterface
{
    /**
     *  The LIST command returns a subset of names from the complete set
     * of all names available to the client.  Zero or more untagged LIST
     * replies are returned, containing the name attributes, hierarchy
     * delimiter, and name; see the description of the LIST reply for
     * more detail
     * @param string $reference eg. "" - if empty list shows all content from root
     * @param string $mailbox eg. "*" - if empty list shows all content from root
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface
     */
    public function listMailbox($reference = "", $mailbox = "*");
}