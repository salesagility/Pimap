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

use SalesAgility\Imap\Response\MessageHeader;

class MessageHeaderTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testOffsetExists()
    {
        $class = new MessageHeader();

        $this->assertTrue($class->offsetExists('date'));
        $this->assertTrue($class->offsetExists('to'));
        $this->assertTrue($class->offsetExists('from'));
        $this->assertTrue($class->offsetExists('replyTo'));
        $this->assertTrue($class->offsetExists('cc'));
        $this->assertTrue($class->offsetExists('bcc'));
        $this->assertTrue($class->offsetExists('subject'));
        $this->assertTrue($class->offsetExists('messageId'));
        $this->assertFalse($class->offsetExists('test'));
    }

    public function testOffsetGet()
    {
        $class = new MessageHeader();
        $this->assertEquals(null, $class->offsetGet('date'));
        $this->assertEquals(array(), $class->offsetGet('to'));
        $this->assertEquals(array(), $class->offsetGet('from'));
        $this->assertEquals(array(), $class->offsetGet('replyTo'));
        $this->assertEquals(array(), $class->offsetGet('cc'));
        $this->assertEquals(array(), $class->offsetGet('bcc'));
        $this->assertEquals('', $class->offsetGet('subject'));
        $this->assertEquals('', $class->offsetGet('messageId'));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageHeader();
                $class->offsetGet('test');
            }
        );
    }

    public function testOffsetSet()
    {
        $class = new MessageHeader();
        $date = new \DateTimeImmutable();
        $class->offsetSet('date', $date);
        $this->assertSame($date, $class->offsetGet('date'));
        $class->offsetSet('to', 'name@example.com');
        $this->assertSame(array('name@example.com'), $class->offsetGet('to'));
        $class->offsetSet('from', 'name@example.com');
        $this->assertSame(array('name@example.com'), $class->offsetGet('from'));
        $class->offsetSet('replyTo', 'name@example.com');
        $this->assertSame(array('name@example.com'), $class->offsetGet('replyTo'));
        $class->offsetSet('cc', 'name@example.com');
        $this->assertSame(array('name@example.com'), $class->offsetGet('cc'));
        $class->offsetSet('bcc', 'name@example.com');
        $this->assertSame(array('name@example.com'), $class->offsetGet('bcc'));
        $class->offsetSet('subject', 'Re: Topic');
        $this->assertSame('Re: Topic', $class->offsetGet('subject'));
        $class->offsetSet('messageId', 'd1fc274555f34745a47430fb3e9cdf56');
        $this->assertSame('d1fc274555f34745a47430fb3e9cdf56', $class->offsetGet('messageId'));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageHeader();
                $class->offsetSet('test', 1);
            }
        );


        $this->tester->expectException(
            new \Exception('"date" must be a \DateTimeImmutable'),
            function () {
                $class = new MessageHeader();
                $class->offsetSet('date', 1);
            }
        );

        $this->tester->expectException(
            new \Exception('"to" must be a string'),
            function () {
                $class = new MessageHeader();
                $class->offsetSet('to', 1);
            }
        );

        $this->tester->expectException(
            new \Exception('"from" must be a string'),
            function () {
                $class = new MessageHeader();
                $class->offsetSet('from', 1);
            }
        );

        $this->tester->expectException(
            new \Exception('"replyTo" must be a string'),
            function () {
                $class = new MessageHeader();
                $class->offsetSet('replyTo', 1);
            }
        );

        $this->tester->expectException(
            new \Exception('"cc" must be a string'),
            function () {
                $class = new MessageHeader();
                $class->offsetSet('cc', 1);
            }
        );

        $this->tester->expectException(
            new \Exception('"bcc" must be a string'),
            function () {
                $class = new MessageHeader();
                $class->offsetSet('bcc', 1);
            }
        );

        $this->tester->expectException(
            new \Exception('"subject" must be a string'),
            function () {
                $class = new MessageHeader();
                $class->offsetSet('subject', 1);
            }
        );


        $this->tester->expectException(
            new \Exception('"messageId" must be a string'),
            function () {
                $class = new MessageHeader();
                $class->offsetSet('messageId', 1);
            }
        );
    }

    public function testOffsetUnset()
    {
        $class = new MessageHeader();
        $date = new \DateTimeImmutable();
        $class->offsetSet('date', $date);
        $class->offsetSet('to', 'name@example.com');
        $class->offsetSet('from', 'name@example.com');
        $class->offsetSet('replyTo', 'name@example.com');
        $class->offsetSet('cc', 'name@example.com');
        $class->offsetSet('bcc', 'name@example.com');
        $class->offsetSet('subject', 'Re: Topic');
        $class->offsetSet('messageId', 'd1fc274555f34745a47430fb3e9cdf56');


        $reflection = new ReflectionClass(MessageHeader::class);
        $date = $reflection->getProperty('date');
        $date->setAccessible(true);
        $to = $reflection->getProperty('to');
        $to->setAccessible(true);
        $from = $reflection->getProperty('from');
        $from->setAccessible(true);
        $replyTo = $reflection->getProperty('replyTo');
        $replyTo->setAccessible(true);
        $cc = $reflection->getProperty('cc');
        $cc->setAccessible(true);
        $bcc = $reflection->getProperty('bcc');
        $bcc->setAccessible(true);
        $subject = $reflection->getProperty('subject');
        $subject->setAccessible(true);
        $messageId = $reflection->getProperty('messageId');
        $messageId->setAccessible(true);

        $class->offsetUnset('date');
        $class->offsetUnset('to');
        $class->offsetUnset('from');
        $class->offsetUnset('replyTo');
        $class->offsetUnset('cc');
        $class->offsetUnset('bcc');
        $class->offsetUnset('subject');
        $class->offsetUnset('messageId');

        $this->assertEquals(null, $date->getValue($class));
        $this->assertEquals(array(), $to->getValue($class));
        $this->assertEquals(array(), $from->getValue($class));
        $this->assertEquals(array(), $replyTo->getValue($class));
        $this->assertEquals(array(), $cc->getValue($class));
        $this->assertEquals(array(), $bcc->getValue($class));
        $this->assertEquals('', $subject->getValue($class));
        $this->assertEquals('', $messageId->getValue($class));

        $this->tester->expectException(
            new \InvalidArgumentException('$offset does not exist: test'),
            function () {
                $class = new MessageHeader();
                $class->offsetUnset('test');
            }
        );


    }

    public function testCc()
    {
        $class = new MessageHeader();
        $this->assertEquals(array(), $class->cc());
    }

    public function testTo()
    {
        $class = new MessageHeader();
        $this->assertEquals(array(), $class->to());
    }

    public function testDate()
    {
        $class = new MessageHeader();
        $this->assertEquals(null, $class->date());
    }

    public function testSubject()
    {
        $class = new MessageHeader();
        $this->assertEquals('', $class->subject());
    }

    public function testBcc()
    {
        $class = new MessageHeader();
        $this->assertEquals(array(), $class->bcc());
    }

    public function testFrom()
    {
        $class = new MessageHeader();
        $this->assertEquals(array(), $class->from());
    }


    public function testReplyTo()
    {
        $class = new MessageHeader();
        $this->assertEquals(array(), $class->replyTo());
    }

    public function testMessageId()
    {
        $class = new MessageHeader();
        $this->assertEquals('', $class->messageId());
    }
}
