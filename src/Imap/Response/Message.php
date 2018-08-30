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


/**
 * Class Message
 * @package SalesAgility\Imap\Response
 */
class Message implements \ArrayAccess
{
    /** @var MessageHeaderInterface|null */
    private $header;

    /** @var  MessageBodyInterface|null */
    private $body;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $uid;

    /**
     * @var MessageFlagsInterface
     */
    private $flags;

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        if ($offset === 'header') {
            return true;
        } elseif ($offset === 'body') {
            return true;
        } elseif ($offset === 'number') {
            return true;
        } elseif ($offset === 'uid') {
            return true;
        } elseif ($offset === 'flags') {
            return true;
        }

        return false;
    }

    /**
     * @param string $offset header|body
     * @return null|MessageBodyInterface|MessageHeaderInterface|string
     */
    public function offsetGet($offset)
    {
        if ($offset === 'header') {
            return $this->header;
        } elseif ($offset === 'body') {
            return $this->body;
        } elseif ($offset === 'number') {
            return $this->number;
        } elseif ($offset === 'uid') {
            return $this->uid;
        } elseif ($offset === 'flags') {
            return $this->flags;
        } else {
            throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * @param string $offset header|body
     * @param MessageHeaderInterface|MessageBodyInterface|mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === 'header') {
            if ($value instanceof MessageHeaderInterface) {
                $this->header = $value;
            } else {
                throw new \InvalidArgumentException('header $value must implement: MessageHeaderInterface');
            }
        } elseif ($offset === 'body') {
            if ($value instanceof MessageBodyInterface) {
                $this->body = $value;
            } else {
                throw new \InvalidArgumentException('body $value must implement: MessageBodyInterface');
            }
        } elseif ($offset === 'number') {
            if (is_string($value)) {
                $this->number = $value;
            } else {
                throw new \InvalidArgumentException('number $value must be a string');
            }
        } elseif ($offset === 'uid') {
            if (is_string($value)) {
                $this->uid = $value;
            } else {
                throw new \InvalidArgumentException('uid $value must be a string');
            }
        } elseif ($offset === 'flags') {
            if ($value instanceof MessageFlagsInterface) {
                $this->flags = $value;
            } else {
                throw new \InvalidArgumentException('flags $value must implement: MessageFlagsInterface');
            }
        } else {
            throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * @param string $offset header|body
     */
    public function offsetUnset($offset)
    {
        if ($offset === 'header') {
            $this->header = null;
        } elseif ($offset === 'body') {
            $this->body = null;
        } elseif ($offset === 'number') {
            $this->number = null;
        } elseif ($offset === 'uid') {
            $this->uid = null;
        } elseif ($offset === 'flags') {
            $this->flags = null;
        } else {
            throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * has the header been included in the message
     * @return bool
     */
    public function hasHeader()
    {
        return $this->header !== null;
    }

    /**
     * @return MessageHeaderInterface
     */
    public function header()
    {
        return $this->header;
    }

    /**
     * has body been included in the message
     * @return bool
     */
    public function hasBody()
    {
        return $this->body !== null;
    }

    /**
     * @return MessageBodyInterface
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function number()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function uid()
    {
        return $this->uid;
    }

    /**
     * @return array|MessageFlagsInterface
     */
    public function flags()
    {
        return $this->flags;
    }
}
