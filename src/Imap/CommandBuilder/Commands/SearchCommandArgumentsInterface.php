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


namespace SalesAgility\Imap\CommandBuilder\Commands;

use SalesAgility\Imap\Response\MessageList;

/**
 * Interface SearchCommandArgumentsInterface
 * @package SalesAgility\Imap\CommandBuilder\Commands
 */
interface SearchCommandArgumentsInterface
{
    /**
     * Messages with message sequence numbers corresponding to the
     * specified message sequence number set.
     * @param string $messageFrom 1
     * @param string $messageTo 2
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function withRange($messageFrom, $messageTo);

    /**
     * Messages with message sequence numbers corresponding to the
     * specified message sequence number set.
     * @param MessageList $messages eg array(1, 2, 3, 5, 8)
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function withSequence(MessageList $messages);

    /**
     *  All messages in the mailbox; the default initial key for
     * ANDing.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchAll();

    /**
     * Messages with the \Answered flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchAnswered();

    /**
     * Messages that contain the specified string in the envelope
     * structure's BCC field.
     * @param $string
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchBcc($string);

    /**
     *  Messages whose internal date (disregarding time and timezone)
     * is earlier than the specified date.
     * @param \DateTimeImmutable $dateTime
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchBefore(\DateTimeImmutable $dateTime);

    /**
     *  Messages that contain the specified string in the body of the
     * message.
     * @param $string
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchBody($string);

    /**
     * Messages that contain the specified string in the envelope
     * structure's CC field.
     * @param $string
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchCc($string);

    /**
     * Messages with the \Deleted flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchDeleted();

    /**
     * Messages with the \Draft flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchDraft();

    /**
     * Messages with the \Flagged flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchFlagged();

    /**
     *  Messages that contain the specified string in the envelope
     * structure's FROM field.
     * @param $string
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchFrom($string);

    /**
     * Messages that have a header with the specified field-name (as
     * defined in [RFC-2822]) and that contains the specified string
     * in the text of the header (what comes after the colon).  If the
     * string to search is zero-length, this matches all messages that
     * have a header line with the specified field-name regardless of
     * the contents.
     * @param $name
     * @param $string
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchHeader($name, $string);

    /**
     * Messages with the specified keyword flag set.
     * @param string $keyword
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchKeyword($keyword);

    /**
     *  Messages with an [RFC-2822] size larger than the specified
     * number of octets.
     * @param $n
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchLarger($n);

    /**
     * Messages that have the \Recent flag set but not the \Seen flag.
     * This is functionally equivalent to "(RECENT UNSEEN)".
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchNew();

    /**
     * Messages that do not match the specified search key.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchNot();

    /**
     * Messages that do not have the \Recent flag set.  This is
     * functionally equivalent to "NOT RECENT" (as opposed to "NOT
     * NEW").
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchOld();

    /**
     *  Messages whose internal date (disregarding time and timezone)
     * is within the specified date.
     * @param \DateTimeImmutable $date
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchOn(\DateTimeImmutable $date);

    /**
     * Messages that match either search key.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchOr();

    /**
     * Messages that have the \Recent flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchRecent();

    /**
     *  Messages that have the \Seen flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchSeen();

    /**
     * Messages whose [RFC-2822] Date: header (disregarding time and
     * timezone) is earlier than the specified date.
     * @param \DateTimeImmutable $date
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchSentBefore(\DateTimeImmutable $date);

    /**
     * Messages whose [RFC-2822] Date: header (disregarding time and
     * timezone) is within the specified date.
     * @param \DateTimeImmutable $date
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchSentOn(\DateTimeImmutable $date);

    /**
     * Messages whose [RFC-2822] Date: header (disregarding time and
     * timezone) is within or later than the specified date.
     * @param \DateTimeImmutable $date
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchSentSince(\DateTimeImmutable $date);

    /**
     *  Messages whose internal date (disregarding time and timezone)
     * is within or later than the specified date.
     * @param \DateTimeImmutable $date
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchSince(\DateTimeImmutable $date);

    /**
     *  Messages with an [RFC-2822] size smaller than the specified
     * number of octets.
     * @param $n
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchSmaller($n);

    /**
     *   Messages that contain the specified string in the envelope
     * structure's SUBJECT field.
     * @param $string
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchSubject($string);

    /**
     *  Messages that contain the specified string in the header or
     * body of the message.
     * @param $string
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchText($string);

    /**
     *  Messages that contain the specified string in the envelope
     * structure's TO field.
     * @param $string
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchTo($string);

    /**
     *  Messages with unique identifiers corresponding to the specified
     * unique identifier set.  Sequence set ranges are permitted.
     *
     * @see searchRange()
     * @see searchSequence()
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchUid($string);

    /**
     * Messages that do not have the \Answered flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchUnanswered();

    /**
     * Messages that do not have the \Deleted flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchUndeleted();

    /**
     * Messages that do not have the \Draft flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchUndraft();

    /**
     * Messages that do not have the \Flagged flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchUnflagged();

    /**
     * Messages that do not have the specified keyword flag set.
     * @param string $keyword
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchUnkeyword($keyword);

    /**
     * Messages that do not have the \Seen flag set.
     * @return \SalesAgility\Imap\CommandBuilder\CommandBuildInterface|SearchCommandArgumentsInterface
     */
    public function searchUnseen();

}