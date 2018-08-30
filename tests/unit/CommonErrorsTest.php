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

use SalesAgility\Imap\Interpreter\ResponseInterpreter;
use SalesAgility\Iteration\StringIterator;

class CommonErrorsTest extends \Codeception\Test\Unit
{
    public function testLineLengthDetectedIncorrectlyIssue()
    {
        $rawResponse = file_get_contents(codecept_data_dir('LineLengthDetectedIncorrectly.txt'));
        $response = StringIterator::withLiteral($rawResponse);
        $interpreter = new ResponseInterpreter();
        // Expect Exception
        $interpreter->parse('A3', $response);
    }

    /**
     * Test when mime type boundary indicator has a separator like "."
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    public function testMimeTypeBoundaryIndicator()
    {
        $rawResponse = file_get_contents(codecept_data_dir('BoundaryContainsSeparators.txt'));
        $response = StringIterator::withLiteral($rawResponse);
        $interpreter = new \SalesAgility\Imap\Interpreter\MessageInterpreter();
        $interpreted = $interpreter->parse($response);
        $this->assertFalse(\SalesAgility\Utility\StringValue::startsWith($interpreted->offsetGet(0)->body()->text(),'--'));
    }
}
