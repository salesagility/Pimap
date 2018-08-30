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

use SalesAgility\Imap\Interpreter\SearchInterpreter;
use \SalesAgility\Imap\Response\MessageFactory;
class SearchInterpreterTest extends \Codeception\Test\Unit
{

    public function testParse()
    {
        $object= new SearchInterpreter();
        $response = \SalesAgility\Iteration\StringIterator::withLiteral('* SEARCH 2677 2678 2679'."\r\n");
        $messageList= new \SalesAgility\Imap\Response\MessageList();

        $message = MessageFactory::instance();
        $message->offsetSet('number', '2677');
        $messageList[] = $message;

        $message = MessageFactory::instance();
        $message->offsetSet('number', '2678');
        $messageList[] = $message;

        $message = MessageFactory::instance();
        $message->offsetSet('number', '2679');
        $messageList[] = $message;

        $actual = $object->parse($response);
        $this->assertEquals($messageList, $actual);
    }
}
