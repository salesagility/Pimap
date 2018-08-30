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

namespace SalesAgility\Utility;

/**
 * Class StringValue
 * @package SalesAgility\Utility
 */
class StringValue
{
    /**
     * @param string $haystack
     * @param string $needle
     * @return bool true when $haystack starts with $needle
     * @throws \Exception
     */
    public static function startsWith($haystack, $needle)
    {
        Assert::is(is_string($haystack), 'StringValue::startsWith $haystack must be a string');
        Assert::is(is_string($needle), 'StringValue::startsWith $needle must be a string');
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool true when $haystack ends with $needle
     * @throws \Exception
     */
    public static function endsWith($haystack, $needle)
    {
        Assert::is(is_string($haystack), 'StringValue::endsWith $haystack must be a string');
        Assert::is(is_string($needle), 'StringValue::endsWith $needle must be a string');

        $length = strlen($needle);

        return $length === 0 || (substr($haystack, -$length) === $needle);
    }
}