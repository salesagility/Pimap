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

use SalesAgility\Imap\Interpreter\MailboxListInterpreter;

class MailboxListInterpreterTest extends \Codeception\Test\Unit
{

    public function testParse()
    {
        $object = new MailboxListInterpreter();
        $response = \SalesAgility\Iteration\StringIterator::withLiteral(file_get_contents(codecept_data_dir('LIST.txt')));
        $actual = $object->parse($response);
        $this->assertCount(9, $actual);
        $this->assertEquals('INBOX', $actual->offsetGet(0)->name());
        $this->assertEquals('[Gmail]', $actual->offsetGet(1)->name());
        $this->assertEquals('[Gmail]/All Mail', $actual->offsetGet(2)->name());
        $this->assertEquals('[Gmail]/Drafts', $actual->offsetGet(3)->name());
        $this->assertEquals('[Gmail]/Important', $actual->offsetGet(4)->name());
        $this->assertEquals('[Gmail]/Sent Mail', $actual->offsetGet(5)->name());
        $this->assertEquals('[Gmail]/Spam', $actual->offsetGet(6)->name());
        $this->assertEquals('[Gmail]/Sent Mail', $actual->offsetGet(7)->name());
        $this->assertEquals('[Gmail]/Trash', $actual->offsetGet(8)->name());
    }
}
