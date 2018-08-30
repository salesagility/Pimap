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

namespace SalesAgility\Imap;

use SalesAgility\Stream\ConnectionException;
use SalesAgility\Imap\Enumerator\ResponseCode;

/**
 * Class ImapException
 * @package SalesAgility\Imap
 */
class ImapException extends ConnectionException
{
    const CODE_TIMEOUT = 408;
    const COMMAND_MISSING_PREFIX = 9000;

    /**
     * @param $message
     * @param int $code
     * @return ImapException
     */
    public static function NoResponse($message, $code = ResponseCode::NO_RESPONSE)
    {
        return new self('No response: ' . $message, $code);
    }

    /**
     * @param $message
     * @param int $code
     * @return ImapException
     */
    public static function BadResponse($message, $code = ResponseCode::BAD_RESPONSE)
    {
        return new self('Bad response: ' . $message, $code);
    }

    /**
     * @param string $message
     * @return ImapException
     */
    public static function MissingCommandPrefix($message = '')
    {
        return new self('Missing Command Prefix: ' . $message, self::COMMAND_MISSING_PREFIX);
    }
}