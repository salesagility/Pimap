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

use SalesAgility\Imap\Response\Message;

class MessageTest extends \Codeception\Test\Unit
{
    /** @var UnitTester $tester */
    protected $tester;

    public function testOffsetExists()
    {
        $class = new Message();
        $this->assertTrue($class->offsetExists('header'));
        $this->assertTrue($class->offsetExists('body'));
        $this->assertTrue($class->offsetExists('number'));
        $this->assertTrue($class->offsetExists('flags'));
        $this->assertTrue($class->offsetExists('uid'));
        $this->assertFalse($class->offsetExists('test'));
    }

    public function testOffsetGet()
    {
        $class = new Message();
        $this->assertEquals(null, $class->offsetGet('header'));
        $this->assertEquals(null, $class->offsetGet('body'));
        $this->assertEquals(null, $class->offsetGet('number'));
        $this->assertEquals(null, $class->offsetGet('uid'));
        $this->assertEquals(null, $class->offsetGet('flags'));
        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new Message();
                $class->offsetGet('test');
            }
        );
    }

    public function testOffsetSet()
    {
        $class = new Message();
        $reflection = new ReflectionClass(Message::class);

        $propertyHeader = $reflection->getProperty('header');
        $propertyHeader->setAccessible(true);

        $propertyBody = $reflection->getProperty('body');
        $propertyBody->setAccessible(true);

        $propertyNumber = $reflection->getProperty('number');
        $propertyNumber->setAccessible(true);

        $propertyUid = $reflection->getProperty('uid');
        $propertyUid->setAccessible(true);

        $header = new \SalesAgility\Imap\Response\MessageHeader();
        $class['header'] = $header;
        $this->assertEquals($header, $propertyHeader->getValue($class));

        $body = new \SalesAgility\Imap\Response\MessageBody();
        $class['body'] = $body;
        $this->assertEquals($body, $propertyBody->getValue($class));

        $class['number'] = '1';
        $this->assertEquals('1', $propertyNumber->getValue($class));

        $class['uid'] = '1';
        $this->assertEquals('1', $propertyUid->getValue($class));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new Message();
                $class::offsetSet('test', 1);
            }
        );

        $this->tester->expectException(
            new \InvalidArgumentException('header $value must implement: MessageHeaderInterface'),
            function () {
                $class = new Message();
                $class['header'] = 1;
            }
        );

        $this->tester->expectException(
            new \InvalidArgumentException('body $value must implement: MessageBodyInterface'),
            function () {
                $class = new Message();
                $class['body'] = 1;
            }
        );

        $this->tester->expectException(
            new \InvalidArgumentException('number $value must be a string'),
            function () {
                $class = new Message();
                $class['number'] = 1;
            }
        );

        $this->tester->expectException(
            new \InvalidArgumentException('uid $value must be a string'),
            function () {
                $class = new Message();
                $class['uid'] = 1;
            }
        );

        $this->tester->expectException(
            new \InvalidArgumentException('flags $value must implement: MessageFlagsInterface'),
            function () {
                $class = new Message();
                $class['flags'] = 1;
            }
        );
    }

    public function testOffsetUnset()
    {
        $class = new Message();
        $reflection = new ReflectionClass(Message::class);

        $propertyHeader = $reflection->getProperty('header');
        $propertyHeader->setAccessible(true);

        $propertyBody = $reflection->getProperty('body');
        $propertyBody->setAccessible(true);

        $propertyNumber = $reflection->getProperty('number');
        $propertyNumber->setAccessible(true);

        $propertyUid = $reflection->getProperty('uid');
        $propertyUid->setAccessible(true);

        $propertyFlags = $reflection->getProperty('flags');
        $propertyFlags->setAccessible(true);


        $header = new \SalesAgility\Imap\Response\MessageHeader();
        $class['header'] = $header;
        $class->offsetUnset('header');
        $this->assertEquals(null, $propertyHeader->getValue($class));

        $body = new \SalesAgility\Imap\Response\MessageBody();
        $class['body'] = $body;
        $class->offsetUnset('body');
        $this->assertEquals(null, $propertyBody->getValue($class));

        $body = new \SalesAgility\Imap\Response\MessageBody();
        $class['number'] = '1';
        $class->offsetUnset('number');
        $this->assertEquals(null, $propertyNumber->getValue($class));

        $body = new \SalesAgility\Imap\Response\MessageBody();
        $class['uid'] = '1';
        $class->offsetUnset('uid');
        $this->assertEquals(null, $propertyUid->getValue($class));

        $body = new \SalesAgility\Imap\Response\MessageBody();
        $class['flags'] = new \SalesAgility\Imap\Response\MessageFlags();
        $class->offsetUnset('flags');
        $this->assertEquals(null, $propertyFlags->getValue($class));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new Message();
                $class->offsetUnset('test');
            }
        );
    }

    public function testHasHeader()
    {
        $class = new Message();
        $this->assertFalse($class->hasHeader());
        $header = new \SalesAgility\Imap\Response\MessageHeader();
        $class['header'] = $header;
        $this->assertTrue($class->hasHeader());
    }

    public function testHeader()
    {
        $class = new Message();
        $header = new \SalesAgility\Imap\Response\MessageHeader();
        $class['header'] = $header;
        $this->assertEquals($header, $class->header());
    }

    public function testHasBody()
    {
        $class = new Message();
        $this->assertFalse($class->hasBody());
        $body = new \SalesAgility\Imap\Response\MessageBody();
        $class['body'] = $body;
        $this->assertTrue($class->hasBody());
    }

    public function testBody()
    {
        $class = new Message();
        $body = new \SalesAgility\Imap\Response\MessageBody();
        $class['body'] = $body;
        $this->assertEquals($body, $class->body());
    }

    public function testNumber()
    {
        $class = new Message();
        $class['number'] = '1';
        $this->assertEquals('1', $class->number());
    }

    public function testUid()
    {
        $class = new Message();
        $class['uid'] = '1';
        $this->assertEquals('1', $class->uid());
    }

    public function testFlags()
    {
        $class = new Message();
        $flags = new \SalesAgility\Imap\Response\MessageFlags();
        $class['flags'] = $flags;
        $this->assertEquals($flags, $class->flags());
    }
}
