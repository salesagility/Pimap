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
 * Class MessageBody
 * @package SalesAgility\Imap\Response
 */
class MessageBody implements MessageBodyInterface
{
    /** @var MessageBodyStructureInterface */
    private $structure;

    /** @var string $html */
    private $html = '';

    /** @var string $text */
    private $text = '';

    /** @var MessageAttachmentInterface[] */
    private $attachments = array();

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        switch ($offset) {
            case 'structure':
                return true;
            case 'html':
                return true;
            case 'text':
                return true;
            case 'attachments':
                return true;
            default:
                return false;
        }
    }

    /**
     * @param string $offset
     * @return MessageAttachmentInterface[]|MessageBodyStructureInterface|string
     * @throws \InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        switch ($offset) {
            case 'structure':
                return $this->structure;
            case 'html':
                return $this->html;
            case 'text':
                return $this->text;
            case 'attachments':
                return $this->attachments;
            default:
                throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        switch ($offset) {
            case 'structure':
                Assert::is($value instanceof MessageBodyStructureInterface, 'attachment must implement MessageAttachmentInterface');
                return $this->structure = $value;
            case 'html':
                Assert::is(is_string($value), 'html must be a string');
                return $this->html = $value;
            case 'text':
                Assert::is(is_string($value), 'text must be a string');
                return $this->text = $value;
            case 'attachments':
                Assert::is($value instanceof MessageAttachmentInterface, 'attachment must implement MessageAttachmentInterface');
                return $this->attachments[] = $value;
            default:
                throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * @param string $offset
     * @return array|null|string
     * @throws \InvalidArgumentException
     */
    public function offsetUnset($offset)
    {
        switch ($offset) {
            case 'structure':
                return $this->structure = null;
            case 'html':
                return $this->html = '';
            case 'text':
                return $this->text = '';
            case 'attachments':
                return $this->attachments = array();
            default:
                throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * @return MessageBodyStructureInterface|null
     */
    public function structure()
    {
        return $this->structure;
    }

    /**
     * @return string|null
     */
    public function html()
    {
        return $this->html;
    }

    /**
     * @return string|null
     */
    public function text()
    {
        return $this->text;
    }

    /**
     * @return MessageAttachment[]|MessageAttachmentInterface[]
     */
    public function attachments()
    {
        return $this->attachments;
    }
}
