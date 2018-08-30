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

use SalesAgility\Imap\Response\MessageBody;

class MessageBodyTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testOffsetExists()
    {
        $class = new MessageBody();
        $this->assertTrue($class->offsetExists('structure'));
        $this->assertTrue($class->offsetExists('html'));
        $this->assertTrue($class->offsetExists('text'));
        $this->assertTrue($class->offsetExists('attachments'));
        $this->assertFalse($class->offsetExists('test'));
    }

    public function testOffsetGet()
    {
        $class = new MessageBody();
        $this->assertEquals(null, $class->offsetGet('structure'));
        $this->assertEquals('', $class->offsetGet('html'));
        $this->assertEquals('', $class->offsetGet('text'));
        $this->assertEquals(array(), $class->offsetGet('attachments'));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageBody();
                $class->offsetGet('test');
            }
        );
    }

    public function testOffsetSet()
    {
        $class = new MessageBody();
        $value = new \SalesAgility\Imap\Response\MessageBodyStructure();
        $class->offsetSet('structure', $value);
        $this->assertEquals($value, $class->offsetGet('structure'));

        $class->offsetSet('html', '<html>');
        $this->assertEquals('<html>', $class->offsetGet('html'));

        $class->offsetSet('text', 'plain');
        $this->assertEquals('plain', $class->offsetGet('text'));

        $value = new \SalesAgility\Imap\Response\MessageAttachment();
        $class->offsetSet('attachments', $value);
        $this->assertEquals(array($value), $class->offsetGet('attachments'));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageBody();
                $class->offsetSet('test', 1);
            }
        );
    }

    public function testOffsetUnset()
    {
        $class = new MessageBody();
        $value = new \SalesAgility\Imap\Response\MessageBodyStructure();
        $class->offsetSet('structure', $value);

        $class->offsetSet('html', '<html>');

        $class->offsetSet('text', 'plain');

        $value = new \SalesAgility\Imap\Response\MessageAttachment();
        $class->offsetSet('attachments', $value);

        $class->offsetUnset('structure');
        $class->offsetUnset('html');
        $class->offsetUnset('text');
        $class->offsetUnset('attachments');

        $this->assertEquals(null, $class->offsetGet('structure'));
        $this->assertEquals('', $class->offsetGet('html'));
        $this->assertEquals('', $class->offsetGet('text'));
        $this->assertEquals(array(), $class->offsetGet('attachments'));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageBody();
                $class->offsetUnset('test');
            }
        );
    }

    public function testStructure()
    {
        $class = new MessageBody();
        $this->assertEquals(null, $class->structure());
    }

    public function testHtml()
    {
        $class = new MessageBody();
        $this->assertEquals('', $class->html());
    }

    public function testAttachments()
    {
        $class = new MessageBody();
        $this->assertEquals(array(), $class->attachments());
    }

    public function testText()
    {
        $class = new MessageBody();
        $this->assertEquals('', $class->text());
    }
}
