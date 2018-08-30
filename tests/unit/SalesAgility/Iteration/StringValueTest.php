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

use SalesAgility\Utility\StringValue;

class StringValueTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testStartsWith()
    {
        $testString = 'foobarbaz';
        $this->assertTrue(StringValue::startsWith($testString, 'foo'));
        $this->assertFalse(StringValue::startsWith($testString, 'bar'));

        // Test negative cases
        $this->tester->expectException(
            new \Exception('StringValue::startsWith $haystack must be a string'),
            function () {
                StringValue::startsWith(1, 'foo');
            }
        );

        $this->tester->expectException(
            new \Exception('StringValue::startsWith $needle must be a string'),
            function () {
                StringValue::startsWith('bar', 1);
            }
        );
    }

    public function testEndsWith()
    {
        $testString = 'foobarbaz';
        $this->assertTrue(StringValue::endsWith($testString, 'baz'));
        $this->assertFalse(StringValue::endsWith($testString, 'bar'));

        // Test negative cases
        $this->tester->expectException(
            new \Exception('StringValue::endsWith $haystack must be a string'),
            function () {
                StringValue::endsWith(1, 'foo');
            }
        );

        $this->tester->expectException(
            new \Exception('StringValue::endsWith $needle must be a string'),
            function () {
                StringValue::endsWith('bar', 1);
            }
        );
    }
}
