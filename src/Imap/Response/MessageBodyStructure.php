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
 * Class MessageBodyStructure
 * @package SalesAgility\Imap\Response
 */
class MessageBodyStructure implements MessageBodyStructureInterface
{
    /** @var bool $text */
    public $plain = false;

    /** @var bool $html */
    public $html = false;

    /** @var bool */
    public $attachments = false;

    //
    // Hidden implementation details
    //
    /** @var string $contentTransferEncoding */
    private $contentTransferEncoding;
    /** @var string $contentType */
    private $contentType;
    /** @var string $mimeVersion */
    private $mimeVersion;

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        switch ($offset) {
            case 'html':
                return true;
            case 'plain':
                return true;
            case 'attachments':
                return true;
            case 'mimeVersion':
                return true;
            case 'contentType':
                return true;
            case 'contentTransferEncoding':
                return true;
            default:
                return false;
        }
    }

    /**
     * @param string $offset
     * @return bool|\InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        switch ($offset) {
            case 'html':
                return $this->html;
            case 'plain':
                return $this->plain;
            case 'attachments':
                return $this->attachments;
            case 'mimeVersion':
                return $this->mimeVersion;
            case 'contentType':
                return $this->contentType;
            case 'contentTransferEncoding':
                return $this->contentTransferEncoding;
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
            case 'html':
                Assert::is(is_bool($value), '"html" must be a bool');
                return $this->html = $value;
            case 'plain':
                Assert::is(is_bool($value), '"plain" must be a bool');
                return $this->plain = $value;
            case 'attachments':
                Assert::is(is_bool($value), '"attachments" must be a bool');
                return $this->attachments = $value;
            case 'mimeVersion':
                return $this->mimeVersion = $value;
            case 'contentType':
                return $this->contentType = $value;
            case 'contentTransferEncoding':
                return $this->contentTransferEncoding = $value;
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
            case 'html':
                $this->html = false;
                break;
            case 'plain':
                $this->plain = false;
                break;
            case 'attachments':
                $this->attachments = false;
                break;
            case 'mimeVersion':
                $this->mimeVersion = '';
                break;
            case 'contentType':
                $this->contentType = '';
                break;
            case 'contentTransferEncoding':
                $this->contentTransferEncoding = '';
                break;
            default:
                throw new \InvalidArgumentException('$offset does not exist: ' . $offset);
        }
    }

    /**
     * does message contains a html part?
     * @return bool
     */
    public function htmlBodyExists()
    {
        return $this->html;
    }

    /**
     * does message contain a plain part?
     * @return bool
     */
    public function plainTextBodyExists()
    {
        return $this->plain;
    }

    /**
     * does message contain attachments or embedded content?
     * @return bool
     */
    public function attachmentsExists()
    {
        return $this->attachments;
    }
}
