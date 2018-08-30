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

use SalesAgility\Iteration\StringIterator;


/**
 * Class Response
 * @package SalesAgility\Imap\ImapResponse
 */
class Response
{
    /** @var string $status */
    private $status;

    /** @var StringIterator $responseMessage */
    private $responseMessage;

    /** @var StringIterator $includedInResponse */
    private $includedInResponse;

    /**
     * Response constructor.
     * @param string $status
     * @param StringIterator $responseMessage
     * @param StringIterator $includedInResponse
     */
    public function __construct($status, StringIterator $responseMessage, StringIterator $includedInResponse)
    {
        $this->status = $status;
        $this->responseMessage = $responseMessage;
        $this->includedInResponse = $includedInResponse;
    }

    /**
     * @return string
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * @return StringIterator
     */
    public function message()
    {
        return $this->responseMessage;
    }

    /**
     * @return StringIterator
     */
    public function included()
    {
        return $this->includedInResponse;
    }
}