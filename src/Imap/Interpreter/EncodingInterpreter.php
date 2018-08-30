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

namespace SalesAgility\Imap\Interpreter;


/**
 * Class EncodingInterpreter
 * @package SalesAgility\Imap\Interpreter
 */
class EncodingInterpreter
{
    /**
     * @param $value
     * @return string
     */
    public function decode($value)
    {
        $decoded_string = '';
        $decoded = imap_mime_header_decode($value);
        foreach ($decoded as $stdclass) {
            $decoded_string .= $stdclass->text;
        }

        return trim($decoded_string);
    }
}