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

use SalesAgility\Imap\Interpreter\CapabilityInterpreter;

class CapabilityInterpreterTest extends \Codeception\Test\Unit
{

    public function testParse()
    {
        $object = new CapabilityInterpreter();
        $file = file_get_contents(codecept_data_dir().'CAPABILITY.txt');
        $response = \SalesAgility\Iteration\StringIterator::withLiteral($file);
        $actual = $object->parse($response);
        $this->assertTrue($actual->offsetExists('IMAP4rev1'));
        $this->assertTrue($actual->offsetExists('IDLE'));
        $this->assertTrue($actual->offsetExists('AUTH=PLAIN'));
        $this->assertFalse($actual->offsetExists('unsupported'));


        $file = file_get_contents(codecept_data_dir().'PASSIVE_CAPABILITY.txt');
        $response = \SalesAgility\Iteration\StringIterator::withLiteral($file);
        $actual = $object->parse($response);
        $this->assertTrue($actual->offsetExists('IMAP4rev1'));
        $this->assertTrue($actual->offsetExists('IDLE'));
        $this->assertTrue($actual->offsetExists('AUTH=PLAIN'));
        $this->assertFalse($actual->offsetExists('unsupported'));
    }
}
