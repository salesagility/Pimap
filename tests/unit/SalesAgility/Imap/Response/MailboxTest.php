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

use SalesAgility\Imap\Response\Mailbox;

class MailboxTest extends \Codeception\Test\Unit
{

    public function testAttributes()
    {
        $object = new Mailbox();
        $this->assertEquals(array(), $object->attributes());
    }

    public function testName()
    {
        $object = new Mailbox();
        $this->assertEquals('', $object->name());
    }

    public function testOffsetExists()
    {
        $object = new Mailbox();

        $this->assertTrue($object->offsetExists('flags'));
        $this->assertTrue($object->offsetExists('attributes'));
        $this->assertTrue($object->offsetExists('hierarchy'));
        $this->assertTrue($object->offsetExists('name'));
        $this->assertTrue($object->offsetExists('exists'));
        $this->assertTrue($object->offsetExists('recent'));
        $this->assertTrue($object->offsetExists('unseen'));
        $this->assertTrue($object->offsetExists('uidvalidity'));
        $this->assertTrue($object->offsetExists('uidnext'));
        $this->assertFalse($object->offsetExists('test'));
    }

    public function testOffsetSet()
    {
        $object = new Mailbox();

        $object->offsetSet('flags', 'Answered');
        $object->offsetSet('attributes', 'Noselect');
        $object->offsetSet( 'hierarchy', '/');
        $object->offsetSet('name', 'Invoices');
        $object->offsetSet('exists', '1');
        $object->offsetSet('recent', '1');
        $object->offsetSet('unseen', '1');
        $object->offsetSet('uidvalidity', '1528185994');
        $object->offsetSet('uidnext', '2');

        $this->assertEquals(array('Answered'), $object->offsetGet('flags'));
        $this->assertEquals(array('Noselect'), $object->offsetGet('attributes'));
        $this->assertEquals('/', $object->offsetGet('hierarchy'));
        $this->assertEquals('Invoices', $object->offsetGet('name'));
        $this->assertEquals('1', $object->offsetGet('exists'));
        $this->assertEquals('1', $object->offsetGet('recent'));
        $this->assertEquals('1', $object->offsetGet('unseen'));
        $this->assertEquals('1528185994', $object->offsetGet('uidvalidity'));
        $this->assertEquals('2', $object->offsetGet('uidnext'));
    }

    public function testUidNext()
    {
        $object = new Mailbox();
        $this->assertEquals('', $object->uidNext());
    }

    public function testFlags()
    {
        $object = new Mailbox();
        $this->assertEquals(array(), $object->flags());
    }

    public function testRecent()
    {
        $object = new Mailbox();
        $this->assertEquals('', $object->name());
    }

    public function testOffsetGet()
    {
        $object = new Mailbox();

        $object->offsetSet('uidnext', '2');
        $this->assertEquals('2', $object->offsetGet('uidnext'));

    }

    public function testUnseen()
    {
        $object = new Mailbox();
        $this->assertEquals('', $object->unseen());
    }

    public function testUidValidity()
    {
        $object = new Mailbox();
        $this->assertEquals('', $object->uidValidity());
    }

    public function testHierarchy()
    {
        $object = new Mailbox();
        $this->assertEquals('', $object->hierarchy());
    }

    public function testExists()
    {
        $object = new Mailbox();
        $this->assertEquals('', $object->exists());
    }

    public function testOffsetUnset()
    {
        $object = new Mailbox();

        $object->offsetSet('flags', 'Answered');
        $object->offsetSet('attributes', 'Noselect');
        $object->offsetSet( 'hierarchy', '/');
        $object->offsetSet('name', 'Invoices');
        $object->offsetSet('exists', '1');
        $object->offsetSet('recent', '1');
        $object->offsetSet('unseen', '1');
        $object->offsetSet('uidvalidity', '1528185994');
        $object->offsetSet('uidnext', '2');

        $object->offsetUnset('flags');
        $object->offsetUnset('attributes');
        $object->offsetUnset( 'hierarchy');
        $object->offsetUnset('name');
        $object->offsetUnset('exists');
        $object->offsetUnset('recent');
        $object->offsetUnset('unseen');
        $object->offsetUnset('uidvalidity');
        $object->offsetUnset('uidnext');
        $object->offsetUnset('test');

        $this->assertEquals(array(), $object->offsetGet('flags'));
        $this->assertEquals(array(), $object->offsetGet('attributes'));
        $this->assertEquals('', $object->offsetGet('hierarchy'));
        $this->assertEquals('', $object->offsetGet('name'));
        $this->assertEquals('', $object->offsetGet('exists'));
        $this->assertEquals('', $object->offsetGet('recent'));
        $this->assertEquals('', $object->offsetGet('unseen'));
        $this->assertEquals('', $object->offsetGet('uidvalidity'));
        $this->assertEquals('', $object->offsetGet('uidnext'));
    }
}
