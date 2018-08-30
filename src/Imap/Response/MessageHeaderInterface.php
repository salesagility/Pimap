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

namespace SalesAgility\Imap\Response;


/**
 * Interface MessageHeaderInterface
 * @package SalesAgility\Imap\Response
 */
interface MessageHeaderInterface extends \ArrayAccess
{
    /**
     * @return \DateTimeImmutable|null
     */
    public function date();

    /**
     * @return string[]
     */
    public function to();

    /**
     * @return string[]
     */
    public function from();

    /**
     * @return string[]
     */
    public function replyTo();

    /**
     * @return string[]
     */
    public function cc();

    /**
     * @return string[]
     */
    public function bcc();

    /**
     * @return string
     */
    public function subject();

    /**
     * @return string
     */
    public function messageId();
}