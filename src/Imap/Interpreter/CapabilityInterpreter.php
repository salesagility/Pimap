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


use SalesAgility\Iteration\StringIterator;
use SalesAgility\Imap\Response\Capability;

class CapabilityInterpreter implements StringIteratorInterpreter
{
    /**
     * @param StringIterator $iterator
     * @return Capability
     */
    public function parse(StringIterator $iterator)
    {
        $response = $iterator->toString();
        $posStart = strpos($response, 'CAPABILITY') + 11;
        $posEnd = strpos($response, "\r\n", $posStart);
        if (empty($posEnd)) {
            $length = strlen($response) - $posStart;
        } else {
            $length = $posEnd - $posStart;
        }
        $substring = substr($response, $posStart, $length);
        $capabilities = explode(" ", $substring);
        $object = new Capability();
        foreach ($capabilities as $capability) {
            if ($capability === "\20") {
                continue;
            }

            if ($capability === '[') {
                continue;
            }

            if ($capability === ']') {
                // if parsing response of select or examine
                break;
            }

            $object->offsetSet($capability, true);
        }

        return $object;
    }
}