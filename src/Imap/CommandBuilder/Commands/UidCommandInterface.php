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
 * Interface UidCommandInterface
 * @package SalesAgility\Imap\CommandBuilder\Commands
 */
interface UidCommandInterface
{
    /**
     * The UID command has two forms.  In the first form, it takes as its
     * arguments a COPY, FETCH, or STORE command with arguments
     * appropriate for the associated command.  However, the numbers in
     * the sequence set argument are unique identifiers instead of
     * message sequence numbers.  Sequence set ranges are permitted, but
     * there is no guarantee that unique identifiers will be contiguous.
     * @return FetchCommandInterface|StoreCommandInterface|CopyCommandInterface|SearchCommandInterface
     */
    public function uid();
}