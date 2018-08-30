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

use SalesAgility\Imap\Response\Capability;

class CapabilityTest extends \Codeception\Test\Unit
{

    public function testOffsetGet()
    {
        $object = new Capability();
        $object['imap'] = true;
        self::assertEquals(true, $object->offsetGet('imap'));
    }

    public function testOffsetExists()
    {
        $object = new Capability();
        $object['imap'] = true;
        self::assertTrue($object->offsetExists('imap'));
        self::assertFalse($object->offsetExists('test'));
    }

    public function testOffsetSet()
    {
        $object = new Capability();
        $object->offsetSet('imap', true);
        self::assertTrue($object->offsetExists('imap'));
        self::assertFalse($object->offsetExists('test'));
    }

    public function testOffsetUnset()
    {
        $object = new Capability();
        $object->offsetSet('imap', true);
        $object->offsetUnset('imap');
        self::assertFalse($object->offsetExists('imap'));
    }
}
