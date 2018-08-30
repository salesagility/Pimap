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

use SalesAgility\Imap\Response\MessageFlags;

class MessageFlagsTest extends \Codeception\Test\Unit
{
    /** @var UnitTester */
    protected $tester;
    /** @var ReflectionProperty */
    private static $flags;

    protected function _before()
    {
        if (self::$flags === null) {
            $reflection = new ReflectionClass(MessageFlags::class);
            self::$flags = $reflection->getProperty('flags');
            self::$flags->setAccessible(true);
        }
    }

    public function test__construct()
    {
        $object = new MessageFlags();
        $expected = array(
            'Answered' => false,
            'Deleted' => false,
            'Draft' => false,
            'Flagged' => false,
            'Recent' => false,
            'Seen' => false,
        );

        $this->assertEquals($expected, self::$flags->getValue($object));
    }

    public function testOffsetExists()
    {
        $object = new MessageFlags();

        $this->assertTrue($object->offsetExists('Answered'));
        $this->assertTrue($object->offsetExists('Deleted'));
        $this->assertTrue($object->offsetExists('Draft'));
        $this->assertTrue($object->offsetExists('Flagged'));
        $this->assertTrue($object->offsetExists('Recent'));
        $this->assertTrue($object->offsetExists('Seen'));
        $this->assertFalse($object->offsetExists('test'));
    }

    public function testOffsetUnset()
    {
        $this->tester->expectException(
            new \Exception('Unset is not supported'),
            function() {
                $object = new MessageFlags();
                unset($object['Answered']);
            }
        );
    }

    public function testOffsetSet()
    {
        $object = new MessageFlags();
        $object['Answered'] = true;
        $expected = array(
            'Answered' => true,
            'Deleted' => false,
            'Draft' => false,
            'Flagged' => false,
            'Recent' => false,
            'Seen' => false,
        );

        $this->assertEquals($expected, self::$flags->getValue($object));
    }

    public function testOffsetGet()
    {
        $object = new MessageFlags();
        $object['Answered'] = true;
        $this->assertEquals(true,$object->offsetGet('Answered'));
    }

    public function testIsAnswered()
    {
        $object = new MessageFlags();
        $object['Answered'] = false;
        $this->assertFalse($object->isAnswered());
        $object['Answered'] = true;
        $this->assertTrue($object->isAnswered());
    }

    public function testIsDeleted()
    {
        $object = new MessageFlags();
        $object['Deleted'] = false;
        $this->assertFalse($object->isDeleted());
        $object['Deleted'] = true;
        $this->assertTrue($object->isDeleted());
    }

    public function testIsSeen()
    {
        $object = new MessageFlags();
        $object['Seen'] = false;
        $this->assertFalse($object->isSeen());
        $object['Seen'] = true;
        $this->assertTrue($object->isSeen());
    }

    public function testIsRecent()
    {
        $object = new MessageFlags();
        $object['Recent'] = false;
        $this->assertFalse($object->isRecent());
        $object['Recent'] = true;
        $this->assertTrue($object->isRecent());
    }

    public function testIsDraft()
    {
        $object = new MessageFlags();
        $object['Draft'] = false;
        $this->assertFalse($object->isDraft());
        $object['Draft'] = true;
        $this->assertTrue($object->isDraft());
    }

    public function testIsFlagged()
    {
        $object = new MessageFlags();
        $object['Flagged'] = false;
        $this->assertFalse($object->isFlagged());
        $object['Flagged'] = true;
        $this->assertTrue($object->isFlagged());
    }


}
