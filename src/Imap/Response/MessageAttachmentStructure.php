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
 * Class MessageAttachmentStructure
 * @package SalesAgility\Imap\Response
 */
class MessageAttachmentStructure implements MessageAttachmentStructureInterface
{
    /** @var string $type */
    private $type = '';

    /** @var string $type */
    private $name = '';

    /** @var string $size */
    private $size = '';

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        switch ($offset) {
            case 'type':
                return true;
            case 'name':
                return true;
            case 'size':
                return true;
            default:
                return false;
        }
    }

    /**
     * @param string $offset
     * @return string $mixed
     */
    public function offsetGet($offset)
    {
        switch ($offset) {
            case 'type':
                return $this->type;
            case 'name':
                return $this->name;
            case 'size':
                return $this->size;
            default:
                throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        switch ($offset) {
            case 'type':
                Assert::is(is_string($value), 'type must be a string');
                $this->type = $value;
                break;
            case 'name':
                Assert::is(is_string($value), 'name must be a string');
                $this->name = $value;
                break;
            case 'size':
                Assert::is(is_int($value), 'size must be a integer in bytes');
                $this->size = $value;
                break;
            default:
                throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * @param string $offset
     * @throws \InvalidArgumentException
     */
    public function offsetUnset($offset)
    {
        switch ($offset) {
            case 'type':
                $this->type = null;
                break;
            case 'name':
                $this->name = '';
                break;
            case 'size':
                $this->size = '';
                break;
            default:
                throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * get the mime type of the content. Eg text/plain, text/html, image/jpeg etc...
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * get the file name of the content
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * get the size (in bytes) of the content
     * @return string
     */
    public function size()
    {
        return $this->size;
    }
}
