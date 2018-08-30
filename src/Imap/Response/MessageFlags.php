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
 * Class MessageFlags
 * @package SalesAgility\Imap\Response
 */
class MessageFlags implements MessageFlagsInterface
{
    private $flags = array();

    /**
     *  MessageFlags Constructor
     */
    public function __construct()
    {
        $this->flags = array(
            'Answered' => false,
            'Deleted' => false,
            'Draft' => false,
            'Flagged' => false,
            'Recent' => false,
            'Seen' => false,
        );
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->flags);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetGet($offset)
    {
        return $this->flags[$offset];
    }

    /**
     * @param string $offset
     * @param bool $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        Assert::is(is_string($offset), '$offset must be a string');
        Assert::is(is_bool($value), '$value must be a boolean');
        $this->flags[$offset] = $value;
    }

    /**
     * @param $offset
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        throw new \Exception('Unset is not supported');
    }

    /**
     * has been answered?
     * @return bool
     */
    public function isAnswered()
    {
        return $this->flags['Answered'];
    }

    /**
     *  is "deleted" for removal by later EXPUNGE?
     * @return bool
     */
    public function isDeleted()
    {
        return $this->flags['Deleted'];
    }

    /**
     *
     *  has not completed composition (marked as a draft)
     * @return bool
     */
    public function isDraft()
    {
        return $this->flags['Draft'];
    }

    /**
     *  is "flagged" for urgent/special attention?
     * @return bool
     */
    public function isFlagged()
    {
        return $this->flags['Flagged'];
    }

    /**
     * is "recently" arrived in this mailbox?
     * @return bool
     */
    public function isRecent()
    {
        return $this->flags['Recent'];
    }

    /**
     * has Message been read?
     * @return bool
     */
    public function isSeen()
    {
        return $this->flags['Seen'];
    }
}
