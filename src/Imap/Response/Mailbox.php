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
 * Class Mailbox
 * @package SalesAgility\Imap\ImapResponse
 */
class Mailbox implements \ArrayAccess
{
    /** @var array $flags */
    private $flags = array();

    /** @var array $attributes */
    private $attributes = array();

    /** @var string $hierarchy */
    private $hierarchy = '';

    /** @var string $name */
    private $name = '';

    /** @var string $exists */
    private $exists = '';

    /** @var string $recent */
    private $recent = '';

    /** @var string $unseen */
    private $unseen = '';

    /** @var string $uidValidity */
    private $uidvalidity = '';

    /** @var string $uidNext */
    private $uidnext = '';

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        if (in_array($offset, array(
            "flags",
            "attributes",
            "hierarchy",
            "name",
            "exists",
            "recent",
            "unseen",
            "uidvalidity",
            "uidnext"
        ))) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        Assert::is(gettype($value) === 'string', '$value must be a string');

        if ($offset === 'flags') {
            $this->flags[] = $value;
        } elseif ($offset === 'attributes') {
            $this->attributes[] = $value;
        } else {
            $this->{$offset} = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if ($offset === 'flags') {
            $this->flags = array();
        } elseif ($offset === 'attributes') {
            $this->attributes = array();
        } elseif ($offset === 'hierarchy') {
            $this->hierarchy = '';
        } elseif ($offset === 'name') {
            $this->name = '';
        } elseif ($offset === 'exists') {
            $this->exists = '';
        } elseif ($offset === 'recent') {
            $this->recent = '';
        } elseif ($offset === 'unseen') {
            $this->unseen = '';
        } elseif ($offset === 'uidvalidity') {
            $this->uidvalidity = '';
        } elseif ($offset === 'uidnext') {
            $this->uidnext = '';
        }
    }

    /**
     * The hierarchy delimiter is a character used to delimit levels of hierarchy in a mailbox name.
     * A client can use it to create child mailboxes, and to search higher or lower levels of naming hierarchy.
     * All children of a top-level hierarchy node MUST use the same separator character. A NIL hierarchy delimiter
     * means that no hierarchy exists; the name is a "flat" name.
     * @return string
     */
    public function hierarchy()
    {
        return $this->hierarchy;
    }

    /**
     * The name of the mailbox
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * - Noinferiors - It is not possible for any child levels of hierarchy to exist under this name; no child
     *                   levels exist now and none can be created in the future.
     * - Noselect - It is not possible to use this name as a selectable mailbox.
     * - Marked - The mailbox has been marked "interesting" by the server; the mailbox probably contains
     *              messages that have been added since the last time the mailbox was selected.
     * - Unmarked - The mailbox does not contain any additional messages since the last time the mailbox was selected.
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * Defined flags in the mailbox.
     * @return array
     */
    public function flags()
    {
        return $this->flags;
    }

    /**
     * The number of messages in the mailbox.
     * @return string
     */
    public function exists()
    {
        return $this->exists;
    }

    /**
     * The number of messages with the Recent flag set.
     * @return string
     */
    public function recent()
    {
        return $this->recent;
    }

    /**
     * The message sequence number of the first unseen message in the mailbox.
     * @return string
     */
    public function unseen()
    {
        return $this->unseen;
    }

    /**
     * The unique identifier validity value.
     * @return string
     */
    public function uidValidity()
    {
        return $this->uidvalidity;
    }

    /**
     * The next unique identifier value.
     * @return string
     */
    public function uidNext()
    {
        return $this->uidnext;
    }
}