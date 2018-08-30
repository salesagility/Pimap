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

namespace SalesAgility\Imap\Response;

use SalesAgility\Imap\Response\Message;
use SalesAgility\Imap\Response\MessageBody;
use SalesAgility\Imap\Response\MessageBodyStructure;
use SalesAgility\Imap\Response\MessageFlags;
use SalesAgility\Imap\Response\MessageHeader;

/**
 * Class MessageFactory
 * @package SalesAgility\Imap
 */
class MessageFactory
{
    /**
     * @return Message
     */
    public static function instance()
    {
        $message = new Message();
        $message['header'] = new MessageHeader();
        $message['body'] = new MessageBody();
        $message['flags'] = new MessageFlags();
        $message['body']['structure'] = new MessageBodyStructure();

        return $message;
    }
}