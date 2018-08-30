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

use SalesAgility\Imap\Response\MessageList;
use SalesAgility\Imap\Response\Message;

class MessageListTest extends \Codeception\Test\Unit
{
    /** @var UnitTester */
    protected $tester;
    public function testKey()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $object[] = $message1;
        $object[] = $message2;
        $object[] = $message3;

        $this->assertEquals(0, $object->key());
        $object->next();
        $this->assertEquals(1, $object->key());
        $object->next();
        $this->assertEquals(2, $object->key());
    }

    public function testCurrent()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $object[] = $message1;
        $object[] = $message2;
        $object[] = $message3;

        $this->assertSame($message1, $object->current());
        $object->next();
        $this->assertSame($message2, $object->current());
        $object->next();
        $this->assertSame($message3, $object->current());
    }

    public function testOffsetExists()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $object[] = $message1;
        $object[] = $message2;
        $object[] = $message3;

        $this->assertTrue($object->offsetExists(0));
        $this->assertTrue($object->offsetExists(1));
        $this->assertTrue($object->offsetExists(2));
        $this->assertFalse($object->offsetExists(3));
    }

    public function testNext()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $object[] = $message1;
        $object[] = $message2;
        $object[] = $message3;
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertEquals(1, $object->key());
    }

    public function testOffsetSet()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $object[0] = $message1;
        $object[1] = $message2;
        $object[2] = $message3;
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertEquals(1, $object->key());


        $this->tester->expectException(
            new \InvalidArgumentException('Message List can only store integer key values'),
            function () {
                $object = new MessageList();
                $message1 = new Message();
                $object['v'] = $message1;
            }
        );

        $this->tester->expectException(
            new \InvalidArgumentException('Message List can only store values which derive from a Message'),
            function () {
                $object = new MessageList();
                $object[] = "string";
            }
        );
    }

    public function testOffsetUnset()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $object[] = $message1;
        $object[] = $message2;
        $object[] = $message3;
        $object->offsetUnset(1);
        $this->assertTrue($object->offsetExists(2));
        $this->assertFalse($object->offsetExists(1));
    }

    public function testOffsetGet()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $object[] = $message1;
        $object[] = $message2;
        $object[] = $message3;

        $this->assertSame($message1, $object->offsetGet(0));
        $this->assertSame($message2, $object->offsetGet(1));
        $this->assertSame($message3, $object->offsetGet(2));
    }

    public function testRewind()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $object[] = $message1;
        $object[] = $message2;
        $object[] = $message3;

        $object->next();
        $object->next();
        $object->next();
        $object->rewind();
        $this->assertEquals(0, $object->key());
    }

    public function testValid()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $object[] = $message1;
        $object[] = $message2;
        $object[] = $message3;
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertFalse($object->valid());
    }

    public function testReverseOrder()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message1->offsetSet('number', '1');
        $message2 = new Message();
        $message2->offsetSet('number', '2');
        $message3 = new Message();
        $message3->offsetSet('number', '3');

        $object[] = $message1;
        $object[] = $message2;
        $object[] = $message3;

        // is ascending order
        $this->assertTrue($object->direction() === 1);
        $this->assertEquals('1',$object->offsetGet(0)->number());
        $this->assertEquals('2',$object->offsetGet(1)->number());
        $this->assertEquals('3',$object->offsetGet(2)->number());

        $object->reverseOrder();

        // is descending order
        $this->assertTrue($object->direction() === -1);
        $this->assertEquals('1',$object->offsetGet(2)->number());
        $this->assertEquals('2',$object->offsetGet(1)->number());
        $this->assertEquals('3',$object->offsetGet(0)->number());
    }

    public function testCount()
    {
        $object = new MessageList();
        $message1 = new Message();
        $message2 = new Message();
        $message3 = new Message();

        $object[] = $message1;
        $object[] = $message2;
        $object[] = $message3;

        // When size === 0, we should recieve an empty list
        $expectedList = new MessageList();
        $actualList = $object->page(0, 0);
        $this->assertEquals($expectedList, $actualList);

        // Expect to see singe page
        $expectedList = $object;
        $actualList = $object->page(0, 3);
        $this->assertEquals($expectedList, $actualList);

        // Expect to see singe page
        $actualList = $object->page(0, 1);
        $this->assertEquals(1, $actualList->count());

        $actualList = $object->page(1, 1);
        $this->assertEquals(1, $actualList->count());

        $actualList = $object->page(2, 1);
        $this->assertEquals(1, $actualList->count());
    }

    public function testPage()
    {

    }
}
