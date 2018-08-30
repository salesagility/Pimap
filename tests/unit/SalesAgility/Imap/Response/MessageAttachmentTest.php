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

use SalesAgility\Imap\Response\MessageAttachment;

class MessageAttachmentTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testOffsetExists()
    {
        $class = new MessageAttachment();
        $this->assertTrue($class->offsetExists('structure'));
        $this->assertTrue($class->offsetExists('hasContent'));
        $this->assertTrue($class->offsetExists('content'));
        $this->assertTrue($class->offsetExists('isInline'));
        $this->assertTrue($class->offsetExists('contentId'));
        $this->assertFalse($class->offsetExists('test'));
    }

    public function testOffsetGet()
    {
        $class = new MessageAttachment();

        $this->assertEquals(null, $class->offsetGet('structure'));
        $this->assertEquals(false, $class->offsetGet('hasContent'));
        $this->assertEquals('', $class->offsetGet('content'));
        $this->assertEquals(false, $class->offsetGet('isInline'));
        $this->assertEquals('', $class->offsetGet('contentId'));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageAttachment();
                $class->offsetGet('test');
            }
        );
    }

    public function testOffsetSet()
    {
        $class = new MessageAttachment();

        $value = new \SalesAgility\Imap\Response\MessageAttachmentStructure();
        $class->offsetSet('structure', $value);
        $this->assertEquals($value, $class->offsetGet('structure'));

        $class->offsetSet('hasContent', true);
        $this->assertEquals(true, $class->offsetGet('hasContent'));

        $class->offsetSet('content', 'plain');
        $this->assertEquals('plain', $class->offsetGet('content'));

        $class->offsetSet('isInline', true);
        $this->assertEquals(true, $class->offsetGet('isInline'));

        $class->offsetSet('contentId', '118d0d188bc73ba6dbbd265f1980a359');
        $this->assertEquals('118d0d188bc73ba6dbbd265f1980a359', $class->offsetGet('contentId'));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageAttachment();
                $class->offsetSet('test', 1);
            }
        );
    }

    public function testOffsetUnset()
    {
        $class = new MessageAttachment();

        $value = new \SalesAgility\Imap\Response\MessageAttachmentStructure();
        $class->offsetSet('structure', $value);
        $class->offsetSet('hasContent', true);
        $class->offsetSet('content', 'plain');
        $class->offsetSet('isInline', true);
        $class->offsetSet('contentId', '118d0d188bc73ba6dbbd265f1980a359');


        $class->offsetUnset('structure');
        $class->offsetUnset('hasContent');
        $class->offsetUnset('content');
        $class->offsetUnset('isInline');
        $class->offsetUnset('contentId');

        $this->assertEquals(null, $class->offsetGet('structure'));
        $this->assertEquals(false, $class->offsetGet('hasContent'));
        $this->assertEquals('', $class->offsetGet('content'));
        $this->assertEquals(false, $class->offsetGet('isInline'));
        $this->assertEquals('', $class->offsetGet('contentId'));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageAttachment();
                $class->offsetUnset('test');
            }
        );
    }

    public function testStructure()
    {
        $class = new MessageAttachment();
        $this->assertEquals(null, $class->structure());
    }

    public function testHasContent()
    {
        $class = new MessageAttachment();
        $this->assertEquals(false, $class->hasContent());
    }

    public function testContent()
    {
        $class = new MessageAttachment();
        $this->assertEquals('', $class->content());
    }

    public function testIsInline()
    {
        $class = new MessageAttachment();
        $this->assertEquals(false, $class->isInline());
    }

    public function testConentId()
    {
        $class = new MessageAttachment();
        $this->assertEquals('', $class->contentId());
    }
}
