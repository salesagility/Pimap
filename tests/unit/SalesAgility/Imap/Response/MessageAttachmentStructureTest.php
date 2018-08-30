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

use SalesAgility\Imap\Response\MessageAttachmentStructure;

class MessageAttachmentStructureTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testOffsetExists()
    {
        $class = new MessageAttachmentStructure();
        $this->assertTrue($class->offsetExists('type'));
        $this->assertTrue($class->offsetExists('name'));
        $this->assertTrue($class->offsetExists('size'));
        $this->assertFalse($class->offsetExists('test'));
    }

    public function testOffsetGet()
    {
        $class = new MessageAttachmentStructure();
        $this->assertEquals('', $class->offsetGet('type'));
        $this->assertEquals('', $class->offsetGet('name'));
        $this->assertEquals('', $class->offsetGet('size'));
        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function() {
                $class = new MessageAttachmentStructure();
                $class->offsetGet('test');
            }
        );
    }

    public function testOffsetSet()
    {
        $class = new MessageAttachmentStructure();
        $class->offsetSet('type', 'text/plain');
        $this->assertEquals('text/plain', $class->offsetGet('type'));
        $class->offsetSet('name', 'example.txt');
        $this->assertEquals('example.txt', $class->offsetGet('name'));
        $class->offsetSet('size', 2567);
        $this->assertEquals(2567, $class->offsetGet('size'));
        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function() {
                $class = new MessageAttachmentStructure();
                $class->offsetSet('test', 1);
            }
        );
    }

    public function testOffsetUnset()
    {
        $class = new MessageAttachmentStructure();
        $class->offsetSet('type', 'text/plain');
        $class->offsetSet('name', 'example.txt');
        $class->offsetSet('size', 2567);

        $class->offsetUnset('type');
        $class->offsetUnset('name');
        $class->offsetUnset('size');

        $this->assertEquals('', $class->offsetGet('type'));
        $this->assertEquals('', $class->offsetGet('name'));
        $this->assertEquals('', $class->offsetGet('size'));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function() {
                $class = new MessageAttachmentStructure();
                $class->offsetUnset('test');
            }
        );
    }

    public function testType()
    {
        $class = new MessageAttachmentStructure();
        $class->offsetSet('type', 'text/plain');
        $this->assertEquals('text/plain',  $class->type());
    }

    public function testSize()
    {
        $class = new MessageAttachmentStructure();
        $class->offsetSet('size', 2567);
        $this->assertEquals(2567,  $class->size());
    }

    public function testName()
    {
        $class = new MessageAttachmentStructure();
        $class->offsetSet('name', 'example.txt');
        $this->assertEquals('example.txt', $class->name());
    }
}
