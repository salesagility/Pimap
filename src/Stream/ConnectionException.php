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


namespace SalesAgility\Stream;

/**
 * Class ConnectionException
 * @package SalesAgility\Stream
 */
class ConnectionException extends \Exception
{
    const CODE_TIMEOUT = 408;
    const CODE_CONNECTION_FAILURE = 503;

    /**
     * @param $message
     * @return ConnectionException
     */
    public static function connectionFailure($message)
    {
        return new self('Failed to connected: ' . $message, self::CODE_CONNECTION_FAILURE);
    }

    /**
     * @param $message
     * @return ConnectionException
     */
    public static function timeout($message)
    {
        return new self('Timed Out: ' . $message, self::CODE_TIMEOUT);
    }
}