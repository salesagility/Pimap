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

use SalesAgility\Iteration\StringIterator;

class StringIteratorTest extends \Codeception\Test\Unit
{
    /** @var UnitTester */
    protected $tester;

    public function test__construct()
    {
        $string = '';
        $class = new StringIterator($string);
        $this->assertEquals(0, $class->key());

        $string = 'bar';
        $class = new StringIterator($string, 0, 1);
        $this->assertEquals(0, $class->key());
        $this->assertEquals('b', $class->current());
        $class->next();
        $this->assertEquals(false, $class->valid());

        $string = 'bar';
        $class = new StringIterator($string, 1, 2);
        $this->assertEquals(1, $class->key());
        $this->assertEquals('a', $class->current());
        $class->next();
        $this->assertEquals('r', $class->current());
        $class->next();
        $this->assertEquals(false, $class->valid());

        // Negative Test
        $this->tester->expectException(
            new \InvalidArgumentException('$string must be a string'),
            function () {
                $string = array();
                new StringIterator($string);
            });
        
        $this->tester->expectException(
            new \InvalidArgumentException('$offset must be a integer'),
            function () {
                $string = '';
                new StringIterator($string, true);
            });

        $this->tester->expectException(
            new \InvalidArgumentException('$count must be a integer'),
            function () {
                $string = '';
                new StringIterator($string, 0, true);
            });
    }

    public function testWithLiteral()
    {
        $class = StringIterator::withLiteral('foo');
        $this->assertEquals('f', $class->current());
    }

    public function testCurrent()
    {
        $string = 'bar';
        $class = new StringIterator($string);
        $this->assertEquals('b', $class->current());
    }

    public function testNext()
    {
        $string = 'bar';
        $class = new StringIterator($string);
        $class->next();
        $this->assertEquals('a', $class->current());
        $this->assertEquals(1, $class->key());
    }

    public function testRewind()
    {
        $string = 'bar';
        $class = new StringIterator($string);
        $class->next();
        $class->rewind();
        $this->assertEquals(0, $class->key());
    }

    public function testValid()
    {
        $string = '';
        $class = new StringIterator($string);
        $this->assertEquals(false, $class->valid());

        $string = 'bar';
        $class = new StringIterator($string);
        $this->assertEquals(true, $class->valid());

        $class->next();
        $class->next();
        $class->next();
        $this->assertEquals(false, $class->valid());
    }

    public function testKey()
    {
        $string = 'bar';
        $class = new StringIterator($string);
        $class->next();
        $this->assertEquals(1, $class->key());
    }

    public function testFastForward()
    {
        $string = 'bar';
        $class = new StringIterator($string);
        $class->fastForward();
        $this->assertEquals(2, $class->key());
    }

    public function testGetInnerString()
    {
        $string = 'bar';
        $class = new StringIterator($string);
        $this->assertEquals('bar', $class->getInnerString());
    }

    public function testSeek()
    {
        $string = 'bar';
        $class = new StringIterator($string);
        $return = $class->seek(2);
        $this->assertEquals(2, $class->key());
        $this->assertEquals(2, $return);

        // negative
        $return = $class->seek(10000);
        $this->assertEquals(2, $class->key());
        $this->assertFalse($return);
    }
}
