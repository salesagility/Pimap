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

use SalesAgility\Imap\ImapException;
use SalesAgility\Imap\Enumerator\ResponseCode;

class ImapExceptionTest extends \Codeception\Test\Unit
{
    public function testNoResponse()
    {
        $object = ImapException::NoResponse('');
        $this->assertEquals(ResponseCode::NO_RESPONSE, $object->getCode());
        $this->assertEquals('No response: ', $object->getMessage());
    }

    public function testMissingCommandPrefix()
    {
        $object = ImapException::MissingCommandPrefix('');
        $this->assertEquals(ImapException::COMMAND_MISSING_PREFIX, $object->getCode());
        $this->assertEquals('Missing Command Prefix: ', $object->getMessage());
    }

    public function testBadResponse()
    {
        $object = ImapException::BadResponse('');
        $this->assertEquals(ResponseCode::BAD_RESPONSE, $object->getCode());
        $this->assertEquals('Bad response: ', $object->getMessage());
    }
}
