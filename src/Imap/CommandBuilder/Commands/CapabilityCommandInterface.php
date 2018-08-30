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
 * Interface CapabilityCommandInterface
 * @package SalesAgility\Imap\CommandBuilder\Commands
 */
interface CapabilityCommandInterface
{
    /**
     * The CAPABILITY command requests a listing of capabilities that the
     * server supports.  The server MUST send a single untagged
     * CAPABILITY response with "IMAP4rev1" as one of the listed
     * capabilities before the (tagged) OK response.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface
     */
    public function capability();
}