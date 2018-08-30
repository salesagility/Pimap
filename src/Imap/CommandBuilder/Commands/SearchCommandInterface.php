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
 * Interface SearchCommandInterface
 * @package SalesAgility\Imap\CommandBuilder\Commands
 */
interface SearchCommandInterface
{
    /**
     * The SEARCH command searches the mailbox for messages that match
     * the given searching criteria.  Searching criteria consist of one
     * or more search keys.  The untagged SEARCH response from the server
     * contains a listing of message sequence numbers corresponding to
     * those messages that match the searching criteria.
     * @return SearchCommandArgumentsInterface
     */
    public function search();
}