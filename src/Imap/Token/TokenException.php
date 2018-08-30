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


namespace SalesAgility\Imap\Token;


/**
 * Class TokenException
 * @package SalesAgility\Imap\Token
 */
class TokenException extends \Exception
{
    const LINE_REQUIREMENT = 1;

    /**
     * @return TokenException
     */
    public static function requiredLineLengthExceeded()
    {
        return new self('RFC2822 requires line length is less than or equals to than 998 characters', self::LINE_REQUIREMENT);
    }

}