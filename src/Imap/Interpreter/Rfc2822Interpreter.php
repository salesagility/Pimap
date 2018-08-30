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

namespace SalesAgility\Imap\Interpreter;


use SalesAgility\Imap\Lexeme\LexemeList;
use SalesAgility\Imap\Lexeme\LexemeType;
use SalesAgility\Imap\Response\Message;
use SalesAgility\Imap\Response\MessageAttachment;
use SalesAgility\Imap\Response\MessageAttachmentStructure;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Utility\Assert;

/**
 * Class Rfc2822Interpreter
 * @package SalesAgility\Imap\Interpreter
 */
class Rfc2822Interpreter
{

    /** @var LexemeInterpreter $lexeme */
    private $lexeme;

    /** @var EncodingInterpreter */
    private $encoding;

    /** @var OctetInterpreter $octetInterpreter */
    private $octetInterpreter;

    /**
     * Rfc2822Interpreter constructor.
     */
    public function __construct()
    {
        $this->lexeme = new LexemeInterpreter();
        $this->encoding = new EncodingInterpreter();
        $this->octetInterpreter = new OctetInterpreter();
    }

    /**
     * @param LexemeList $lexemes
     * @param Message $message
     * @return bool|null
     * @throws \Exception
     */
    public function parseHeader(LexemeList &$lexemes, Message &$message)
    {
        $octet = new OctetInterpreter();
        while ($lexemes->valid()) {
            $this->seekNextLexeme($lexemes);
            $this->seekWhitespace($lexemes);

            $limit = $octet->parse($lexemes);
            $this->seekNextLexeme($lexemes);

            if ($lexemes->current()->hasType(LexemeType::newLine())) {
                $this->seekNextLexeme($lexemes, $octet);
            }
            $lexemes->current()->rewind();
            $offset = $lexemes->current()->current()->first();

            while ($lexemes->valid()) {
                if ($lexemes->current()->hasType(LexemeType::fieldHeader())) {
                    $fieldName = $lexemes->current()->toString();
                    $this->seekNextLexeme($lexemes);
                    Assert::is($lexemes->current()->hasType(LexemeType::fieldBody()), 'Message Interpreter: expected a field value');

                    $fieldValue = $lexemes->current()->toString();
                    $this->seekNextLexeme($lexemes);

                    $this->parseHeaderField($fieldName, $fieldValue, $message);
                } elseif ($octet->isEndOfOctetBoundary($lexemes, $offset, $limit)) {
                    // End of header
                    return true;
                }

                $this->seekNextLexeme($lexemes);
            }

            // use this to parse only the section
            $this->seekNextLexeme($lexemes);
        }

        return null;
    }

    /**
     * @param LexemeList $lexemes
     * @param Message $message
     * @return bool|null|Message
     * @throws \Exception
     */
    public function parseBody(LexemeList &$lexemes, Message &$message)
    {
        while ($lexemes->valid()) {
            $this->seekNextLexeme($lexemes);
            $this->seekWhitespace($lexemes);

            $limit = $this->octetInterpreter->parse($lexemes);
            $this->seekNextLexeme($lexemes);

            if ($lexemes->current()->hasType(LexemeType::newLine())) {
                $this->seekNextLexeme($lexemes);
            }

            $startPosition = $lexemes->key();
            $startOffset = $lexemes->current()->current()->first();

            $boundaries = $this->seekMimeBoundaries($lexemes, $startOffset, $limit);

            // is plain text email with no attachments? (is there a boundary marker? NO?)
            if (empty($boundaries)) {
                // just plain text email with no attachments
                //// use a new iterator to create a sub string
                $lexemes->offsetGet($startPosition)->rewind();
                $substring = $lexemes->offsetGet($startPosition)->current()->getInnerIterator();
                $plainTextBody = StringIterator::withStringIterator($substring, $startOffset, $limit);
                $message->body()->offsetSet('text', $plainTextBody->toString());
                $message->body()->structure()->offsetSet('html', false);
                $message->body()->structure()->offsetSet('attachments', false);
                return $message;
            } else {
                // parse multiResponse email
                $this->parseMultiResponseMime($lexemes, $message, $boundaries);
                $lexemes->seek(end($boundaries)['lexemeLastKey'] + 1);
                return $message;
            }

            // use this to parse only the section
            $this->seekNextLexeme($lexemes);
        }
        return null;
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     * @param Message $message
     * @throws \Exception
     */
    public function parseHeaderField(&$fieldName, &$fieldValue, Message &$message)
    {
        switch ($fieldName) {
            case "Date":
                $this->parseDate('date', $fieldValue, $message);
                break;
            case "Subject":
                $this->parseSubject($fieldValue, $message);
                break;
            case "To":
                $this->parseEmailAddresses('to', $fieldValue, $message);
                break;
            case "From":
                $this->parseEmailAddresses('from', $fieldValue, $message);
                break;
            case "Reply-To":
                $this->parseEmailAddresses('replyTo', $fieldValue, $message);
                break;
            case "Cc":
                $this->parseEmailAddresses('cc', $fieldValue, $message);
                break;
            case "Bcc":
                $this->parseEmailAddresses('bcc', $fieldValue, $message);
                break;
            case "Message-Id":
                $this->parseEmailAddresses('messageId', $fieldValue, $message);
                break;
            case "MIME-Version":
                $message->body()->structure()->offsetSet('mimeVersion', $fieldValue);
                break;
            case "Content-Type":
                $message->body()->structure()->offsetSet('contentType', $fieldValue);
                break;
            case "Content-Transfer-Encoding":
                $message->body()->structure()->offsetSet('contentTransferEncoding', $fieldValue);
                break;
            default:
                // not yet supported but it's ok
                // there is no need to throw exception
                break;
        }
    }

    /**
     * @param $field
     * @param $value
     * @param Message $message
     * @throws \Exception
     */
    private function parseDate($field, &$value, Message &$message)
    {
        $value = trim($value);
        $possibleImaps = [
            \DateTime::RFC2822 . '+',
            str_replace(['D, '], '', \DateTime::RFC2822), // day-of-week is optional
            str_replace([':s'], '', \DateTime::RFC2822), // seconds are optional
            str_replace(['D, ', ':s'], '', \DateTime::RFC2822), // day-of-week is optional, seconds are optional
            \DateTime::RFC822,
            str_replace(['D, '], '', \DateTime::RFC822), // day is optional
            str_replace([':s'], '', \DateTime::RFC822), // seconds are optional
            str_replace(['D, ', ':s'], '', \DateTime::RFC822), // day is optional, seconds are optional
        ];

        $dateTime = false;
        // All IMAP servers respond with different data Imaps.
        // The iteration attempt to use each possible Imap to decode the detail.
        // The if ($dateTime !== false) means that when the DateTime class successfully
        // decodes the date field it will exit the loop.
        // As we no longer need to continue trying to decode the datetime Imap.
        foreach ($possibleImaps as $possibleImap) {
            $dateTime = \DateTimeImmutable::createFromFormat($possibleImap, $value);
            if ($dateTime !== false) {
                break;
            }
        }

        Assert::is($dateTime !== false, 'Expected header Date to comply with RFC2882');
        $message->header()->offsetSet($field, $dateTime);
    }

    /**
     * @param $field
     * @param $value
     * @param Message $message
     * @throws \Exception
     */
    private function parseEmailAddresses($field, &$value, Message &$message)
    {
        Assert::is(!empty($value), "expected email address to no be empty");
        // Test if there are more than one address
        if (strpos($value, ',') !== false) {
            $addresses = explode(',', $value);
            foreach ($addresses as $address) {
                $message->header()->offsetSet($field, $this->encoding->decode($address));
            }
        } else {
            $message->header()->offsetSet($field, $this->encoding->decode($value));
        }
    }

    /**
     * @param $value
     * @param Message $message
     */
    private function parseSubject(&$value, Message &$message)
    {
        $message->header()->offsetSet('subject', $this->encoding->decode($value));
    }

    /**
     * Provides offset protection
     * Calculates the elapsed $octetCount
     * @param LexemeList $lexemes
     */
    private function seekNextLexeme(LexemeList &$lexemes)
    {
        $lexemes->next();
    }

    /**
     * skip next whitespace character
     * @param LexemeList $lexemes
     * @return bool
     */
    private function seekWhitespace(LexemeList &$lexemes)
    {
        if ($this->lexeme->isWhitespace($lexemes)) {
            $this->seekNextLexeme($lexemes);
        }

        return false;
    }

    /**
     * skip next whitespace character
     * @param LexemeList $lexemes
     * @return bool
     */
    private function seekToNewline(LexemeList &$lexemes)
    {
        $key = $lexemes->key();
        while ($lexemes->valid()) {
            if ($this->lexeme->isNewLine($lexemes)) {
                return true;
            }
            $this->seekNextLexeme($lexemes);
        }

        $lexemes->seek($key);
        return false;
    }


    /**
     * @param LexemeList $lexemes
     * @param string|integer $offset
     * @param string|integer $limit
     * @return array
     * @throws \Exception
     */
    private function seekMimeBoundaries(LexemeList &$lexemes, $offset, $limit)
    {
        $boundaries = array();
        while ($lexemes->valid()) {
            if ($this->isBoundary($lexemes)) {
                $boundary = $this->decodeBoundary($lexemes, $boundaries);
                $boundaries[] = $boundary;
            }

            if ($this->octetInterpreter->isEndOfOctetBoundary($lexemes, $offset, $limit)) {
                // TODO: detect if  boundary to scope is outside?
                break;
            }

            $lexemes->next();
        }

        if (!empty($boundaries)) {
            Assert::is(end($boundaries)['type'] === 'close', 'failed to detect the last mime boundary check');
        }
        return $boundaries;
    }

    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    /**
     * @param LexemeList $lexemes
     * @return bool
     */
    private function isBoundary(LexemeList &$lexemes)
    {
        $key = $lexemes->key();

        $previous1Lexeme = $lexemes->offsetGet($key - 1);
        $startWithNewLine = $previous1Lexeme->hasType(LexemeType::newLine());

        // Detect length, It must include a hash
        // This prevent the edge case where a message may contain a --
        // eg -----Original message----- or -- followed by a signature
        $isLongEnough = strlen($lexemes->current()->toString()) >= 28;

        // Rewind the token iterator (Just in case the key is not set to 0)
        $lexemes->current()->rewind();
        $lexemes->current()->current()->rewind();
        $boundaryString = $lexemes->current()->toString();

        // Detect starting characters
        $startWithDoubleDashes = false;
        if ($isLongEnough) {
            $firstChar = $boundaryString[0];
            $secondChar = $boundaryString[1];
            $startWithDoubleDashes = $firstChar === '-' && $secondChar === '-';
        }


        // The next character must be a new line (CRLF)
        $followBy = false;
        if ($lexemes->offsetExists($key + 1)) {
            $nextLexeme = $lexemes->offsetGet($key + 1);
            $followBy = $nextLexeme->hasType(LexemeType::newLine())
                || $nextLexeme->hasType(LexemeType::whitespace())
                // Folding space cannot be detected so we need to look for a keyword
                || $nextLexeme->hasType(LexemeType::capitalsNumbers())
                || $nextLexeme->hasType(LexemeType::allCapitals())
                || $nextLexeme->toString() === '.'
                //
                || $nextLexeme->toString() === '*';
        }


        return $startWithNewLine
            && $isLongEnough
            && $startWithDoubleDashes
            && $followBy;
    }

    /**
     * @param LexemeList $lexemes
     * @param array $boundaries
     * @return array
     * @throws \Exception
     */
    private function decodeBoundary(LexemeList &$lexemes, array $boundaries)
    {
        // $boundary structure
        // lexemeKey is lexeme key where the boundary was found
        // lexemeLastKey is lexeme key where the end of the boundary key was found
        // type can be invalid | open | separator | close
        //      "invalid" marks an error has occurred
        //      "open" marks the start of a boundary
        //      "separator" marks a new boundary (it is likely to be the html Response)
        //      "close" marks the end of a boundary
        //
        // scope can be none | outside | inside
        //      "none" marks an error has occurred
        //      "outside" marks the boundary of the entire message
        //      "inside" marks the boundary of a mime Response eg attachment or html Response
        $boundary = array(
            'lexemeKey' => -1,
            'lexemeLastKey' => -1,
            'type' => 'invalid',
            'scope' => 'invalid',
        );

        $key = $lexemes->key();

        // Set lexemeKey
        $boundary['lexemeKey'] = $lexemes->key();

        // Rewind the token iterator (Just in case the key is not set to 0)
        $lexemes->current()->rewind();
        $lexemes->current()->current()->rewind();
        $boundaryString = $lexemes->current()->toString();

        // When boundary contains characters which typically are used as separators
        // eg. "." it means the the boundary will be split across multiple lexemes
        // So we need to handle this case
        if ($lexemes->offsetExists($key + 1)) {
            $nextLexeme = $lexemes->offsetGet($key + 1);
            if ($nextLexeme->toString() === '.') {
                // seek new line
                if ($this->seekToNewline($lexemes)) {
                    // if new line found then add lexemes to boundaryString
                    $endkey = $lexemes->key();
                    for ($i = $key + 1; $i < $endkey; $i++) {
                        $boundaryString .= $lexemes->offsetGet($i)->toString();
                        $boundary['lexemeLastKey'] = $lexemes->key();
                    }
                }
            }
        }

        if ($boundary['lexemeLastKey'] === -1) {
            $boundary['lexemeLastKey'] = $lexemes->key();
        }

        // Detect starting characters
        $firstChar = $boundaryString[0];
        $secondChar = $boundaryString[1];
        $startWithDoubleDashes = $firstChar === '-' && $secondChar === '-';

        // Detect ending characters
        $last2Char = substr($boundaryString, -2);
        $lastChar = $last2Char[1];
        $secondLastChar = $last2Char[0];
        $endsWithDoubleDashes = $lastChar === '-' && $secondLastChar === '-';

        // Detect boundary type
        // not all boundaries end with a -- so we need to check if it is the same as the first boundary
        // sometimes the outer boundary is repeated as a separator for html or an attachment
        if (!empty($boundaries) && $lexemes->offsetGet(end($boundaries)['lexemeKey'])->toString() === $lexemes->current()->toString()) {
            $boundary['type'] = 'separator';
        } elseif ($startWithDoubleDashes && !$endsWithDoubleDashes) {
            $boundary['type'] = 'open';
        } elseif ($startWithDoubleDashes && $endsWithDoubleDashes) {
            $boundary['type'] = 'close';
        } else {
            $boundary['type'] = 'invalid';
        }

        // Detect boundary scope
        if (empty($boundaries)) {
            // is this the first boundary detected
            $boundary['scope'] = 'outside';
        } elseif (trim($lexemes->offsetGet($boundaries[0]['lexemeKey'])->toString(), '-') === trim($lexemes->current()->toString(), '-')) {
            // is first boundary === to the current boundary
            $boundary['scope'] = 'outside';
        } else {
            // Is this second or third etc. boundary detected
            $boundary['scope'] = 'inside';
        }

        Assert::is($boundary['scope'] !== 'invalid', 'unable to determine the scope of mime boundary');
        Assert::is($boundary['type'] !== 'invalid', 'unable to determine the type of mime boundary');
        return $boundary;
    }


    /**
     * @param LexemeList $lexemes
     * @param Message $message
     * @param array $boundaries
     * @throws \Exception
     */
    /**
     * @param LexemeList $lexemes
     * @param Message $message
     * @param array $boundaries
     * @throws \Exception
     */
    private function parseMultiResponseMime(LexemeList &$lexemes, Message &$message, array $boundaries)
    {
        // is plain text email with attachments? (is multiResponse? is html section missing?  is attachment found?)
        // is html/plain email? ( is multiResponse? is html section found?)
        // is html/plain email with attachment? (is multiResponse? is html section found? is attachment found?)
        // is 2 line ending found? (it signifies the end of a boundary for plain text emails, look for a end of group or keyword after it)
        //    it should not have any atoms after its.
        // is boundary detected? is it an inside or outside boundary? Out side boundary marks the end of the message

        $firstBoundary = $boundaries[0];
        $firstLexemeKey = $firstBoundary['lexemeKey'] + 1;
        $lastBoundary = end($boundaries);
        $lastBoundaryKey = count($boundaries) - 1;
        $lastLexemeKey = $lastBoundary['lexemeKey'];

        // Start from first boundary
        $lexemes->seek($firstLexemeKey);

        $boundaryIndex = 0;
        while ($boundaryIndex < $lastBoundaryKey && $lexemes->key() < $lastLexemeKey) {
            $endOfBoundary = $boundaries[$boundaryIndex + 1]['lexemeKey'] - 1;
            $lexemes->seek($boundaries[$boundaryIndex]['lexemeLastKey'] + 1);
            while ($lexemes->current()->hasType(LexemeType::newLine())) {
                $this->seekNextLexeme($lexemes);
            }

            $this->extractMimeResponse($lexemes, $message, $endOfBoundary, $boundaries[$boundaryIndex]);


            // seek to endOfBoundary
            ++$boundaryIndex;
        }

        // handle last boundary
        $lexemes->next();
    }

    /**
     * @param $haystack
     * @param $fieldValue
     * @param $attributes
     */
    /**
     * @param $haystack
     * @param $fieldValue
     * @param $attributes
     */
    private function extractFieldAttributes(&$haystack, &$fieldValue, &$attributes)
    {
        $commentPosition = strpos($haystack, ';');
        if ($commentPosition !== false) {
            // has attributes
            $fieldValue = trim(substr($haystack, 1, $commentPosition - 1), "\x20\x09\"'");
            $commentString = substr($haystack, $commentPosition);
            $comments = explode(' ', trim($commentString, "\x20"));
            foreach ($comments as $comment) {
                $equalPosition = strpos($comment, '=');

                if ($equalPosition !== false) {
                    $attribute = explode('=', $comment);
                    $value = trim($attribute[1], "\x20\x09\"';");
                    // fix for '"utf-8";'
                    $value = trim($value, "\"'<>");
                    $attributes[$attribute[0]] = $value;
                }
            }
        } else {
            // no attributes available
            $fieldValue = trim($haystack, "\x20\x09\"';");
        }
    }

    /**
     * @param LexemeList $lexemes
     * @param Message $message
     * @param $endOfBoundary
     * @param $boundary
     * @return LexemeList|void
     * @throws \Exception
     */
    private function extractMimeResponse(LexemeList &$lexemes, Message &$message, $endOfBoundary, $boundary)
    {
        // mime headers inImapion
        $contentTransferEncoding = '8bit';
        $contentTransferEncodingAttributes = array();
        $contentDisposition = false;
        $contentDispositionAttributes = array();
        $contentType = '';
        $contentTypeAttributes = array();
        $contentId = '';
        $charset = 'ASCII';


        while ($lexemes->valid() && $lexemes->key() < $endOfBoundary) {

            if ($lexemes->current()->hasType(LexemeType::fieldHeader())) {
                $fieldName = $lexemes->current()->toString();
                $this->seekNextLexeme($lexemes);
                Assert::is($lexemes->current()->hasType(LexemeType::fieldBody()), 'Message Interpreter: expected a field value');
                $fieldValue = $lexemes->current()->toString();
                // detect Content-Transfer-Encoding header
                // detect Content-Disposition: attachment for attachments and inline attachments
                if ($fieldName === 'Content-Type') {
                    $this->extractFieldAttributes($fieldValue, $contentType, $contentTypeAttributes);
                } elseif ($fieldName === 'Content-Disposition') {
                    $this->extractFieldAttributes($fieldValue, $contentDisposition, $contentDispositionAttributes);
                } elseif ($fieldName === 'Content-Transfer-Encoding') {
                    $this->extractFieldAttributes($fieldValue, $contentTransferEncoding, $contentTransferEncodingAttributes);
                } elseif ($fieldName === 'Content-ID') {
                    $this->extractFieldAttributes($fieldValue, $contentId, $contentTransferEncodingAttributes);
                    $contentId = trim($contentId, '<>');
                }

                // decode/convert Content-Type charset
                if (!empty($contentTypeAttributes) && isset($contentTypeAttributes['charset'])) {
                    $charset = $contentTypeAttributes['charset'];

                    // fix lower case Imaps to be upper case
                    if ($charset === 'utf-8') {
                        $charset = $contentTypeAttributes['charset'] = 'UTF-8';
                    }

                    // fix ascii
                    if ($charset === 'us-ascii') {
                        $charset = $contentTypeAttributes['charset'] = 'ASCII';
                    }
                } elseif (!empty($contentDispositionAttributes) && isset($contentDispositionAttributes['charset'])) {
                    $charset = $contentTypeAttributes['charset'];

                    // fix lower case Imaps to be upper case
                    if ($charset === 'utf-8') {
                        $charset = $contentTypeAttributes['charset'] = 'UTF-8';
                    }

                    // fix ascii
                    if ($charset === 'us-ascii') {
                        $charset = $contentTypeAttributes['charset'] = 'ASCII';
                    }
                } else {
                    $charset = 'ASCII';
                }
                $this->seekNextLexeme($lexemes);
            } else {
                // select mime Response content
                $first = $lexemes->current()->offsetGet(0)->first();
                $last = $lexemes->offsetGet($endOfBoundary)->offsetGet(0)->first();
                $lexemeIterator = $lexemes->current()->offsetGet(0)->getInnerIterator();
                $contentIterator = StringIterator::withStringIterator($lexemeIterator, $first, $last - $first);
                $content = $contentIterator->toString();

                // decode Content-Transfer-Encoding
                if (trim($contentTransferEncoding) === '7bit') {
                    $content = imap_utf7_decode($contentTransferEncoding);
                } elseif (trim($contentTransferEncoding) === '8bit') {
                    // nothing to do
                } elseif (trim($contentTransferEncoding) === 'binary') {
                    // nothing to do
                } elseif (trim($contentTransferEncoding) === 'quoted-printable') {
                    $content = quoted_printable_decode($content);
                } elseif (trim($contentTransferEncoding) === 'base64') {
                    $content = imap_base64($content);
                } else {
                    throw new \Exception('Content-Transfer-Encoding: "' . $contentTransferEncoding . '" is not supported');
                }

                Assert::is(in_array($charset, mb_list_encodings()), 'Charset: "' . $charset . '" is not supported. ' . 'supported: ' . implode(' ', mb_list_encodings()));
                $content = mb_convert_encoding($content, 'UTF-8', $charset);

                // assign to body Response of the message class
                // decode Content-Disposition: attachment; filename="example.html"
                if ($contentDisposition === false) {
                    // text/html body
                    if ($boundary['type'] === 'open' && $boundary['scope'] === 'outside' & $contentType === 'text/plain') {
                        // plain text body
                        $message->body()->offsetSet('text', $content);
                    } else {
                        // html body
                        $message->body()->offsetSet('html', $content);
                    }
                } elseif ($contentDisposition === 'inline') {
                    // inline attachment
                    $attachment = $this->buildAttachment();
                    $attachment->offsetSet('content', $content);
                    $attachment->offsetSet('hasContent', true);
                    $attachment->offsetSet('isInline', true);
                    $attachment->offsetSet('contentId', $contentId);
                    $attachment->structure()->offsetSet('type', $contentType);
                    $attachment->structure()->offsetSet('size', strlen($content));
                    $message->body()->offsetSet('attachments', $attachment);
                } elseif ($contentDisposition === 'attachment') {
                    // attachment
                    $attachment = $this->buildAttachment();
                    $attachment->offsetSet('content', $content);
                    $attachment->offsetSet('hasContent', true);
                    $attachment->offsetSet('isInline', false);

                    if (array_key_exists('filename', $contentDispositionAttributes)) {
                        $attachment->structure()->offsetSet('name', $contentDispositionAttributes['filename']);
                    } elseif (array_key_exists('name', $contentTypeAttributes)) {
                        $attachment->structure()->offsetSet('name', $contentTypeAttributes['name']);
                    }

                    $attachment->structure()->offsetSet('type', $contentType);
                    $attachment->structure()->offsetSet('size', strlen($content));
                    $attachment->offsetSet('contentId', $contentId);
                    $message->body()->offsetSet('attachments', $attachment);
                } else {
                    throw new \Exception('Content-Disposition: ' . $contentDisposition . ' is not supported');
                }
                return;

            }

            $this->seekNextLexeme($lexemes);
        }
    }

    /**
     * @return MessageAttachment
     * @throws \Exception
     */
    private function buildAttachment()
    {
        $attachment = new MessageAttachment();
        $attachment->offsetSet('structure', new MessageAttachmentStructure());
        return $attachment;
    }
}
