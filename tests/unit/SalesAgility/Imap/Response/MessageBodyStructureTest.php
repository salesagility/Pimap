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

use SalesAgility\Imap\Response\MessageBodyStructure;

class MessageBodyStructureTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testOffsetExists()
    {
        $class = new MessageBodyStructure();
        $this->assertTrue($class->offsetExists('plain'));
        $this->assertTrue($class->offsetExists('html'));
        $this->assertTrue($class->offsetExists('attachments'));
        $this->assertTrue($class->offsetExists('mimeVersion'));
        $this->assertTrue($class->offsetExists('contentType'));
        $this->assertTrue($class->offsetExists('contentTransferEncoding'));
        $this->assertFalse($class->offsetExists('test'));
    }

    public function testOffsetGet()
    {
        $class = new MessageBodyStructure();
        $this->assertEquals(false, $class->offsetGet('plain'));
        $this->assertEquals(false, $class->offsetGet('html'));
        $this->assertEquals(false, $class->offsetGet('attachments'));
        $this->assertEquals('', $class->offsetGet('mimeVersion'));
        $this->assertEquals('', $class->offsetGet('contentType'));
        $this->assertEquals('', $class->offsetGet('contentTransferEncoding'));
        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageBodyStructure();
                $class->offsetGet('test');
            }
        );
    }

    public function testOffsetSet()
    {
        $class = new MessageBodyStructure();
        $class->offsetSet('plain', true);
        $this->assertEquals(true, $class->offsetGet('plain'));
        $class->offsetSet('html', true);
        $this->assertEquals(true, $class->offsetGet('html'));
        $class->offsetSet('attachments', true);
        $this->assertEquals(true, $class->offsetGet('attachments'));
        $class->offsetSet('mimeVersion', '1.0');
        $this->assertEquals('1.0', $class->offsetGet('mimeVersion'));
        $class->offsetSet('contentType', 'text/plain');
        $this->assertEquals('text/plain', $class->offsetGet('contentType'));
        $class->offsetSet('contentTransferEncoding', 'base64');
        $this->assertEquals('base64', $class->offsetGet('contentTransferEncoding'));
        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageBodyStructure();
                $class->offsetSet('test', 1);
            }
        );
    }

    public function testOffsetUnset()
    {
        $class = new MessageBodyStructure();
        $class->offsetSet('plain', true);
        $class->offsetSet('html', true);
        $class->offsetSet('attachments', true);
        $class->offsetSet('mimeVersion', '1.0');
        $class->offsetSet('contentType', 'text/plain');
        $class->offsetSet('contentTransferEncoding', 'base64');

        $class->offsetUnset('plain');
        $class->offsetUnset('html');
        $class->offsetUnset('attachments');
        $class->offsetUnset('mimeVersion');
        $class->offsetUnset('contentType');
        $class->offsetUnset('contentTransferEncoding');

        $this->assertEquals(false, $class->offsetGet('plain'));
        $this->assertEquals(false, $class->offsetGet('html'));
        $this->assertEquals(false, $class->offsetGet('attachments'));
        $this->assertEquals('', $class->offsetGet('mimeVersion'));
        $this->assertEquals('', $class->offsetGet('contentType'));
        $this->assertEquals('', $class->offsetGet('contentTransferEncoding'));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageBodyStructure();
                $class->offsetUnset('test');
            }
        );
    }

    public function testHtmlBodyExists()
    {
        $class = new MessageBodyStructure();
        $this->assertFalse($class->htmlBodyExists());
        $class->offsetSet('html', true);
        $this->assertTrue($class->htmlBodyExists());
    }

    public function testPlainTextBodyExists()
    {
        $class = new MessageBodyStructure();
        $this->assertFalse($class->plainTextBodyExists());
        $class->offsetSet('plain', true);
        $this->assertTrue($class->plainTextBodyExists());
    }

    public function testAttachmentsExists()
    {
        $class = new MessageBodyStructure();
        $this->assertFalse($class->attachmentsExists());
        $class->offsetSet('attachments', true);
        $this->assertTrue($class->attachmentsExists());
    }
}
