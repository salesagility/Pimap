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
 * Class MessageList
 * @package SalesAgility\Imap
 * A list of @see \SalesAgility\Imap\Response\Message
 */
class MessageList implements \Iterator, \ArrayAccess
{
    private $messageList = array();
    private $currentKey = 0;
    private $direction = 1;

    /**
     * @return Message
     */
    public function current()
    {
        return $this->messageList[$this->currentKey];
    }

    public function next()
    {
        ++$this->currentKey;
    }

    /**
     * @return int|mixed
     */
    public function key()
    {
        return $this->currentKey;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->currentKey < count($this->messageList);
    }

    public function rewind()
    {
        $this->currentKey = 0;
    }

    /**
     * @param $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->messageList);
    }

    /**
     * @param mixed $offset
     * @return message
     */
    public function offsetGet($offset)
    {
        return $this->messageList[$offset];
    }

    /**
     * @param int $offset
     * @param Message $value
     */
    public function offsetSet($offset, $value)
    {
        $newOffset = false;
        if ($offset === null) {
            $newOffset = true;
        } elseif (gettype($offset) !== "integer") {
            throw new \InvalidArgumentException('Message List can only store integer key values');
        }

        if ($value instanceof Message) {
            if (!$newOffset) {
                $this->messageList[$offset] = $value;
            } else {
                $count = count($this->messageList);
                if ($count === 0) {
                    $this->messageList[0] = $value;
                } else {
                    $this->messageList[$count] = $value;
                }
            }
        } else {
            throw new \InvalidArgumentException('Message List can only store values which derive from a Message');
        }
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->messageList[$offset]);
    }

    /**
     * Reverses the order of the Message List
     * @see MessageList::direction()
     */
    public function reverseOrder()
    {
        $this->direction *= -1;
        $this->messageList = array_reverse($this->messageList);
    }

    /**
     * @return int positive integer is ascending order, negative integer is descending order
     * @see MessageList::reverseOrder()
     */
    public function direction()
    {
        return $this->direction;
    }


    /**
     * @param integer $offset which page to display
     * @param integer $size total Messages per page
     * @return MessageList
     */
    public function page($offset, $size)
    {
        $messageList = new MessageList();
        if ($size < 1) {
            return $messageList;
        }

        if ($this->valid()) {
            $paginated = array_chunk($this->messageList, $size);
            if ($paginated !== null) {
                $messageList->messageList = $paginated[$offset];
            }
        }

        return $messageList;
    }

    public function count()
    {
        return count($this->messageList);
    }
}