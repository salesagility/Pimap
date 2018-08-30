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

use SalesAgility\Imap\Response\MailboxList;

class MailboxListTest extends \Codeception\Test\Unit
{

    /** @var UnitTester $tester */
    protected $tester;
    public function testRewind()
    {
        $object = new MailboxList();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object->next();
        $object->next();
        $object->next();
        $object->rewind();
        $this->assertEquals(0, $object->key());
    }

    public function testOffsetSet()
    {
        $object = new MailboxList();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object->next();
        $object->next();
        $this->assertFalse($object->valid());


        $this->tester->expectException(
            new \InvalidArgumentException('Mailbox List can only store integer key values'),
            function () {
                $object = new MailboxList();
                $object['foo'] = 1;
            }
        );

        $this->tester->expectException(
            new \InvalidArgumentException('Mailbox List can only store values which derive from a Mailbox'),
            function () {
                $object = new MailboxList();
                $object[] = new \InvalidArgumentException('Mailbox List can only store values which derive from a Mailbox');
            }
        );
    }

    public function testOffsetUnset()
    {
        $object = new MailboxList();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();

        $object->offsetUnset(0);
        $this->assertFalse($object->offsetExists(2));
    }

    public function testKey()
    {
        $object = new MailboxList();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $this->assertEquals(0, $object->key());
        $object->next();
        $this->assertEquals(1, $object->key());
        $object->next();
        $this->assertEquals(2, $object->key());
        $object->next();
    }

    public function testCurrent()
    {
        $object = new MailboxList();

        $mailbox = new \SalesAgility\Imap\Response\Mailbox();
        $mailbox->offsetSet('name', 'Invoices');

        $object[] =$mailbox;

        $this->assertEquals($mailbox, $object->current());
    }

    public function testNext()
    {
        $object = new MailboxList();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $this->assertEquals(0, $object->key());
        $object->next();
        $this->assertEquals(1, $object->key());
    }

    public function testOffsetGet()
    {
        $object = new MailboxList();

        $mailbox = new \SalesAgility\Imap\Response\Mailbox();
        $mailbox->offsetSet('name', 'Invoices');

        $object[] =$mailbox;

        $this->assertEquals($mailbox, $object->offsetGet(0));
    }

    public function testValid()
    {
        $object = new MailboxList();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertTrue($object->valid());
        $object->next();
        $this->assertFalse($object->valid());
    }

    public function testOffsetExists()
    {
        $object = new MailboxList();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $object[] = new \SalesAgility\Imap\Response\Mailbox();
        $this->assertTrue($object->offsetExists(0));
        $this->assertTrue($object->offsetExists(1));
        $this->assertTrue($object->offsetExists(2));
        $this->assertFalse($object->offsetExists(3));
    }
}
