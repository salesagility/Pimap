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
 * Interface SortCommandArgumentsInterface
 * @package SalesAgility\Imap\CommandBuilder\Commands
 */
interface SortCommandArgumentsInterface
{
    /**
     * Internal date and time of the message.  This differs from the
     * ON criteria in SEARCH, which uses just the internal date.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SortCommandArgumentsInterface
     */
    public function byArrival();

    /**
     *  [IMAP] addr-mailbox of the first "cc" address.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SortCommandArgumentsInterface
     */
    public function byCc();

    /**
     * Sent date and time
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SortCommandArgumentsInterface
     */
    public function byDate();

    /**
     * [IMAP] addr-mailbox of the first "From" address.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SortCommandArgumentsInterface
     */
    public function byFrom();

    /**
     *  Followed by another sort criterion, has the effect of that
     * criterion but in reverse (descending) order.
     * Note: REVERSE only reverses a single criterion, and does not
     * affect the implicit "sequence number" sort criterion if all
     * other criteria are identical.  Consequently, a sort of
     * REVERSE SUBJECT is not the same as a reverse ordering of a
     * SUBJECT sort.  This can be avoided by use of additional
     * criteria, e.g., SUBJECT DATE vs. REVERSE SUBJECT REVERSE
     * DATE.  In general, however, it's better (and faster, if the
     * client has a "reverse current ordering" command) to reverse
     * the results in the client instead of issuing a new SORT.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SortCommandArgumentsInterface
     */
    public function byReverse();

    /**
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SortCommandArgumentsInterface
     */
    public function bySize();

    /**
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SortCommandArgumentsInterface
     */
    public function bySubject();

    /**
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SortCommandArgumentsInterface
     */
    public function byTo();
}