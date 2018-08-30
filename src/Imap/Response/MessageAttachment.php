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
 * Class MessageAttachment
 * @package SalesAgility\Imap\Response
 */
class MessageAttachment implements MessageAttachmentInterface
{
    /** @var MessageAttachmentStructureInterface $structure */
    private $structure;

    /** @var bool $isInline */
    private $isInline = false;

    /** @var bool $hasContent */
    private $hasContent = false;

    /** @var string $content */
    private $content = '';

    /** @var string $contentId */
    private $contentId = '';

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        switch ($offset) {
            case 'structure':
                return true;
            case 'hasContent':
                return true;
            case 'content':
                return true;
            case 'isInline':
                return true;
            case 'contentId':
                return true;
            default:
                return false;
        }
    }

    /**
     * @param string $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        switch ($offset) {
            case 'structure':
                return $this->structure;
            case 'hasContent':
                return $this->hasContent;
            case 'content':
                return $this->content;
            case 'isInline':
                return $this->isInline;
            case 'contentId':
                return $this->contentId;
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
                $this->structure = $value;
                break;
            case 'hasContent':
                Assert::is(gettype($value) === 'boolean', 'hasContent must be a boolean');
                $this->hasContent = $value;
                break;
            case 'content':
                $this->content = $value;
                break;
            case 'isInline':
                Assert::is(gettype($value) === 'boolean', 'isInline must be a boolean');
                return $this->isInline = $value;
            case 'contentId':
                $this->contentId = $value;
                break;
            default:
                throw new \InvalidArgumentException('$offset does not exist: test');
        }
    }

    /**
     * @param string $offset
     * @return bool|null|string
     * @throws \InvalidArgumentException
     */
    public function offsetUnset($offset)
    {
        switch ($offset) {
            case 'structure':
                return $this->structure = null;
            case 'hasContent':
                return $this->hasContent = false;
            case 'content':
                return $this->content = '';
            case 'isInline':
                return $this->isInline = false;
            case 'contentId':
                return $this->contentId = '';
            default:
                throw new \InvalidArgumentException('$offset does not exist: test');
        }
    }

    /**
     * get the information about the attachment.
     * @return MessageAttachmentStructureInterface
     */
    public function structure()
    {
        return $this->structure;
    }

    /**
     * has the attachments content been included?
     * @return bool
     */
    public function hasContent()
    {
        return $this->hasContent;
    }

    /**
     * get attachments content been included.
     * @return string|null
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * is the attachment embedded content?
     * @return bool
     */
    public function isInline()
    {
        return $this->isInline;
    }

    /**
     * get the content id (cid:) which is referenced in the text or html of the body
     * @return string used to recognize inline attachments
     */
    public function contentId()
    {
        return $this->contentId;
    }
}
