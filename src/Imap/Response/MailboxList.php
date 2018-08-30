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
 * Class MailboxList
 * @package SalesAgility\Imap
 * A list of @see \SalesAgility\Imap\Response\Mailbox
 */
class MailboxList implements \Iterator, \ArrayAccess
{
    private $mailboxList = array();
    private $currentKey = 0;

    /**
     * @return Mailbox
     */
    public function current()
    {
        return $this->mailboxList[$this->currentKey];
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
        return $this->currentKey < count($this->mailboxList);
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
        return array_key_exists($offset, $this->mailboxList);
    }

    /**
     * @param mixed $offset
     * @return Mailbox
     */
    public function offsetGet($offset)
    {
        return $this->mailboxList[$offset];
    }

    /**
     * @param int $offset
     * @param Mailbox $value
     */
    public function offsetSet($offset, $value)
    {
        $newOffset = false;
        if ($offset === null) {
            $newOffset = true;
        } elseif (gettype($offset) !== "integer") {
            throw new \InvalidArgumentException('Mailbox List can only store integer key values');
        }

        if ($value instanceof Mailbox) {
            if (!$newOffset) {
                $this->mailboxList[$offset] = $value;
            } else {
                $count = count($this->mailboxList);
                if ($count === 0) {
                    $this->mailboxList[0] = $value;
                } else {
                    $this->mailboxList[$count] = $value;
                }
            }
        } else {
            throw new \InvalidArgumentException('Mailbox List can only store values which derive from a Mailbox');
        }
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        array_splice($this->mailboxList, $offset, 1);
    }
}