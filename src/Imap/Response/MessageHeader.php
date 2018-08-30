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


use SalesAgility\Utility\Assert;

/**
 * Class MessageHeader
 * @package SalesAgility\Imap\Response
 */
class MessageHeader implements MessageHeaderInterface
{
    /** @var \DateTimeImmutable $date */
    private $date = null;

    /** @var string[] $to */
    private $to = array();

    /** @var string[] $from */
    private $from = array();

    /** @var string[] $replyTo */
    private $replyTo = array();

    /** @var string[] $cc */
    private $cc = array();

    /** @var string[] bcc */
    private $bcc = array();

    /** @var string $subject */
    private $subject = '';

    /** @var string $messageId */
    private $messageId = '';

    /**
     * @param string $offset to|from|replyTo|cc|bcc|subject
     * @return bool
     */
    public function offsetExists($offset)
    {
        switch ($offset) {
            case "date":
                return true;
            case "to":
                return true;
            case "from":
                return true;
            case "replyTo":
                return true;
            case "cc":
                return true;
            case "bcc":
                return true;
            case "subject":
                return true;
            case "messageId":
                return true;
            default:
                return false;
        }
    }

    /**
     * @param string $offset to|from|replyTo|cc|bcc|subject
     * @return string|string[]
     */
    public function offsetGet($offset)
    {
        switch ($offset) {
            case "date":
                return $this->date;
            case "to":
                return $this->to;
            case "from":
                return $this->from;
            case "replyTo":
                return $this->replyTo;
            case "cc":
                return $this->cc;
            case "bcc":
                return $this->bcc;
            case "subject":
                return $this->subject;
            case "messageId":
                return $this->messageId;
            default:
                throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * @param string $offset to|from|replyTo|cc|bcc|subject
     * @param string $value Note: setting the value will append the array
     * @return void
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        switch ($offset) {
            case "date":
                Assert::is($value instanceof \DateTimeImmutable, '"date" must be a \DateTimeImmutable');
                $this->date = $value;
                break;
            case "to":
                Assert::is(is_string($value), '"to" must be a string');
                $this->to[] = $value;
                break;
            case "from":
                Assert::is(is_string($value), '"from" must be a string');
                $this->from[] = $value;
                break;
            case "replyTo":
                Assert::is(is_string($value), '"replyTo" must be a string');
                $this->replyTo[] = $value;
                break;
            case "cc":
                Assert::is(is_string($value), '"cc" must be a string');
                $this->cc[] = $value;
                break;
            case "bcc":
                Assert::is(is_string($value), '"bcc" must be a string');
                $this->bcc[] = $value;
                break;
            case "subject":
                Assert::is(is_string($value), '"subject" must be a string');
                $this->subject = $value;
                break;
            case "messageId":
                Assert::is(is_string($value), '"messageId" must be a string');
                $this->messageId = trim($value);
                break;
            default:
                throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * @param  string $offset to|from|replyTo|cc|bcc|subject
     * @return void
     */
    public function offsetUnset($offset)
    {
        switch ($offset) {
            case "date":
                $this->date = null;
                break;
            case "to":
                $this->to = array();
                break;
            case "from":
                $this->from = array();
                break;
            case "replyTo":
                $this->replyTo = array();
                break;
            case "cc":
                $this->cc = array();
                break;
            case "bcc":
                $this->bcc = array();
                break;
            case "subject":
                $this->subject = '';
                break;
            case "messageId":
                $this->messageId = '';
                break;
            default:
                throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * The origination date specifies the date and time at which the creator of the message indicated
     * that the message was complete and ready to enter the mail delivery system.
     * @return \DateTimeImmutable
     */
    public function date()
    {
        return $this->date;
    }

    /**
     * contains the address(es) of the primary recipient(s) of the message.
     * @return string[]
     */
    public function to()
    {
        return $this->to;
    }

    /**
     * where the email originates from.
     * @return string[]
     */
    public function from()
    {
        return $this->from;
    }

    /**
     *  where the email originates from / an email to reply to.
     * @return string[]
     */
    public function replyTo()
    {
        if (empty($this->replyTo)) {
            return $this->from();
        }

        return $this->replyTo;
    }

    /**
     *  "carbon copy" - contains the addresses of the primary recipient(s) of the message.
     * @return string[]
     */
    public function cc()
    {
        return $this->cc;
    }

    /**
     * "blind carbon copy" - contains addresses of recipients of the message whose addresses are not to be revealed to other recipients of the message.
     * @return string[]
     */
    public function bcc()
    {
        return $this->bcc;
    }

    /**
     * brief description of what the email is about.
     * @return string
     */
    public function subject()
    {
        return $this->subject;
    }

    /**
     * contains a single unique message identifier.
     * @return string
     */
    public function messageId()
    {
        return $this->messageId;
    }
}