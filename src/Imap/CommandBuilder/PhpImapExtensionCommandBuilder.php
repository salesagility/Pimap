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


namespace SalesAgility\Imap\CommandBuilder;


use SalesAgility\Imap\CommandBuilder\CommandValidator\CommandValidationInterface;
use SalesAgility\Imap\CommandBuilder\CommandValidator\CommandValidatorAwareInterface;
use SalesAgility\Imap\Enumerator\Format;
use SalesAgility\Imap\Response\MessageList;
use SalesAgility\Pattern\Singleton;

/**
 * Class PhpImapExtensionCommandBuilder
 * @package SalesAgility\Imap\CommandBuilder
 */
class PhpImapExtensionCommandBuilder implements Singleton, PhpImapExtensionSupportedCommandsInterface, CommandBuildInterface, CommandBuildArgumentsInterface, CommandValidatorAwareInterface
{
    private $commandPrefix = '';
    private $command = '';
    private $arguments = array();
    private $asString = '';
    private $isValidated = false;
    /** @var CommandValidationInterface[] */
    private $validators = array();
    private $isRaw = false;

    /**
     * @return PimapSupportedTopLevelCommandsInterface
     */
    public static function instance()
    {
        return new self();
    }

    // Builder calls for Imap library use only

    /**
     * {@inheritDoc}
     */
    public function build()
    {
        switch ($this->command) {
            case 'UID':
                if (array_key_exists('FETCH', $this->arguments)) {
                    $this->buildFetch();
                } elseif (array_key_exists('SEARCH', $this->arguments)) {
                    $this->buildSearch();
                } elseif (array_key_exists('STORE', $this->arguments)) {
                    $this->buildStore();
                } elseif (array_key_exists('COPY', $this->arguments)) {
                    $this->buildCopy();
                }
                break;
            case 'FETCH':
                $this->buildFetch();
                break;
            case 'SEARCH':
                $this->buildSearch();
                break;
            case 'STORE':
                $this->buildStore();
                break;
            case 'COPY':
                $this->buildCopy();
                break;
            case 'STATUS':
                $this->asString = $this->prefixWhitespace($this->command)
                    . $this->prefixWhitespace($this->arguments['MAILBOX'])
                    . $this->prefixWhitespace($this->buildGroup($this->arguments['INCLUDE']));
                break;
            case 'APPEND':
                $this->asString = $this->prefixWhitespace($this->command);

                if (!empty($this->arguments['FLAGS'])) {
                    $this->asString .= $this->prefixWhitespace($this->buildGroup($this->arguments['FLAGS']));
                }

                if (!empty($this->arguments['DATE'])) {
                    $this->asString .= $this->prefixWhitespace($this->arguments['DATE']);
                }

                $this->asString .= $this->prefixWhitespace($this->arguments['MESSAGE']);
                break;
            case 'LIST':
                $this->asString = $this->prefixWhitespace($this->command)
                    . $this->prefixWhitespace('"' . $this->arguments['REFERENCE_NAME'] . '"')
                    . $this->prefixWhitespace('"' . $this->arguments['MAILBOX'] . '"');
                break;
            case 'LSUB':
                $this->asString = $this->prefixWhitespace($this->command)
                    . $this->prefixWhitespace('"' . $this->arguments['REFERENCE_NAME'] . '"')
                    . $this->prefixWhitespace('"' . $this->arguments['MAILBOX'] . '"');
                break;
            case 'SELECT':
                $this->asString = $this->prefixWhitespace($this->command)
                    . $this->prefixWhitespace('"' . $this->arguments['MAILBOX'] . '"');
                break;
            default:
                break;
        }

        if (array_key_exists($this->command, $this->validators)) {
            $this->isValidated = $this->validators[$this->command]->validate($this);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function command()
    {
        return $this->command;
    }

    /**
     * {@inheritDoc}
     */
    public function commandPrefix()
    {
        return $this->commandPrefix;
    }

    /**
     * {@inheritDoc}
     */
    public function commandArguments()
    {
        return $this->arguments;
    }

    /**
     * {@inheritDoc}
     */
    public function tagged($tag)
    {
        $this->commandPrefix = $tag;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function untagged()
    {
        $this->commandPrefix = '*';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function asString()
    {
        return $this->commandPrefix . $this->asString;
    }

    /**
     * {@inheritDoc}
     */
    public function asArray()
    {
        return array(
            'command' => $this->command,
            'argument' => $this->arguments,
            'validated' => $this->isValidated,
            'raw' => $this->isRaw,
        );
    }

    // Commands For Library Consumer

    /**
     * {@inheritDoc}
     */
    public function raw($command)
    {
        $this->arguments = array();
        $this->command = $command;
        $this->asString = $this->prefixWhitespace($this->command);
        $this->isRaw = true;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function logout()
    {
        $this->command = 'LOGOUT';
        $this->arguments = array();
        $this->isValidated = false;
        $this->asString = $this->prefixWhitespace($this->command);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function noop()
    {
        $this->command = 'NOOP';
        $this->arguments = array();
        $this->isValidated = false;
        $this->asString = $this->prefixWhitespace($this->command);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function login()
    {
        $this->command = 'LOGIN';
        $this->arguments = array(
            'USER' => '',
            'PASSWORD' => '',
        );
        $this->isValidated = false;
        $this->asString .= $this->prefixWhitespace($this->command);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function user($user)
    {
        $this->arguments['USER'] = $user;
        $this->asString .= $this->prefixWhitespace($user);
        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function password($password)
    {
        $this->arguments['PASSWORD'] = $password;
        $this->asString .= $this->prefixWhitespace($password);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function select($mailbox)
    {
        $this->command = 'SELECT';
        $this->arguments = array(
            'MAILBOX' => $mailbox,
        );
        $this->asString .= $this->prefixWhitespace($this->command);
        $this->asString .= $this->prefixWhitespace($mailbox);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function examine($mailbox)
    {
        $this->command = 'EXAMINE';
        $this->arguments = array(
            'MAILBOX' => $mailbox,
        );
        $this->asString .= $this->prefixWhitespace($this->command);
        $this->asString .= $this->prefixWhitespace($mailbox);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function create($mailbox)
    {
        $this->command = 'CREATE';
        $this->arguments = array(
            'MAILBOX' => $mailbox,
        );
        $this->asString .= $this->prefixWhitespace($this->command);
        $this->asString .= $this->prefixWhitespace($mailbox);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($mailbox)
    {
        $this->command = 'DELETE';
        $this->arguments = array(
            'MAILBOX' => $mailbox,
        );
        $this->asString .= $this->prefixWhitespace($this->command);
        $this->asString .= $this->prefixWhitespace($mailbox);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function subscribe($mailbox)
    {
        $this->command = 'SUBSCRIBE';
        $this->arguments = array(
            'MAILBOX' => $mailbox,
        );
        $this->asString .= $this->prefixWhitespace($this->command);
        $this->asString .= $this->prefixWhitespace($mailbox);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function unsubscribe($mailbox)
    {
        $this->command = 'UNSUBSCRIBE';
        $this->arguments = array(
            'MAILBOX' => $mailbox,
        );
        $this->asString .= $this->prefixWhitespace($this->command);
        $this->asString .= $this->prefixWhitespace($mailbox);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function listMailbox($reference = "", $mailbox = "*")
    {
        $this->command = 'LIST';
        $this->arguments = array(
            'REFERENCE_NAME' => $reference,
            'MAILBOX' => $mailbox,
        );
        $this->asString .= $this->prefixWhitespace($this->command);
        $this->asString .= $this->prefixWhitespace($reference);
        $this->asString .= $this->prefixWhitespace($mailbox);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function listSubsetMailbox($reference = "", $mailbox = "*")
    {
        $this->command = 'LSUB';
        $this->arguments = array(
            'REFERENCE_NAME' => $reference,
            'MAILBOX' => $mailbox,
        );
        $this->asString .= $this->prefixWhitespace($this->command);
        $this->asString .= $this->prefixWhitespace($reference);
        $this->asString .= $this->prefixWhitespace($mailbox);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function rename($mailbox, $newMailbox)
    {
        $this->command = 'RENAME';
        $this->arguments = array(
            'MAILBOX' => $mailbox,
            'NEW_MAILBOX' => $newMailbox,
        );
        $this->asString .= $this->prefixWhitespace($this->command);
        $this->asString .= $this->prefixWhitespace($mailbox);
        $this->asString .= $this->prefixWhitespace($newMailbox);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        $command = 'CLOSE';
        $this->command = $command;
        $this->asString .= $this->prefixWhitespace($command);
        $this->arguments = array();
        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function expunge()
    {
        $command = 'EXPUNGE';
        $this->command = $command;
        $this->asString .= $this->prefixWhitespace($command);
        $this->arguments = array();
        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function append($mailbox)
    {
        $command = 'APPEND';
        $this->command = $command;
        $this->asString .= $this->prefixWhitespace($command);
        $this->arguments = array(
            'MAILBOX' => $mailbox,
            'FLAGS' => array(),
            'DATE' => '',
            'MESSAGE' => '{0}' . "\r\n\r\n"
        );
        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withFlag($flag)
    {
        $this->arguments['FLAGS'][] = $flag;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withDateTime(\DateTimeImmutable $datetime)
    {
        $this->arguments['DATE'] = $datetime->format(Format::RFC2822_DATE);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withMessage($message)
    {
        $this->arguments['MESSAGE'] = $message;
        return $this;
    }


    /**
     * {@inheritDoc}
     */
    public function check()
    {
        $this->command = 'CHECK';
        $this->asString .= $this->prefixWhitespace($this->command);
        $this->arguments = array();
        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function copy()
    {
        $command = 'COPY';
        $arguments = array(
            'MESSAGE' => '',
            'MAILBOX' => ''
        );

        if ($this->command === 'UID') {
            $this->arguments[$command] = $arguments;
        } else {
            $this->command = 'COPY';
            $this->arguments = $arguments;
        }

        $this->asString .= $this->prefixWhitespace($command);

        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withRange($messageFrom, $messageTo)
    {

        $arguments = $messageFrom . ':' . $messageTo;

        if ($this->command === 'UID') {
            reset($this->arguments);
            $command = key($this->arguments);
            if ($command === 'SEARCH') {
                $this->arguments[$command][] = $arguments;
            } else {
                $this->arguments[$command]['MESSAGE'] = $arguments;
            }
        } else {
            if ($this->command === 'SEARCH') {
                $this->arguments[] = $arguments;
            } else {
                $this->arguments['MESSAGE'] = $arguments;
            }
        }

        $this->asString .= $this->prefixWhitespace($this->command);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toMailbox($mailbox)
    {
        $command = 'COPY';
        $arguments = $mailbox;

        if ($this->command === 'UID') {
            $this->arguments[$command]['MAILBOX'] = $arguments;
        } else {
            $this->arguments['MAILBOX'] = $arguments;
        }

        $this->asString .= $this->prefixWhitespace($this->command);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function search()
    {
        $command = 'SEARCH';

        $this->asString .= $this->prefixWhitespace($command);
        $this->arguments = array();
        $this->isValidated = false;

        if ($this->command === 'UID') {
            $this->arguments[$command] = array();
        } else {
            $this->command = $command;
        }

        return $this;
    }


    /**
     * {@inheritDoc}
     */
    public function withSequence(MessageList $messages)
    {
        $this->buildSearchArgument('SEARCH', $this->buildSequence($messages, 'SEARCH'));
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchAll()
    {
        $this->buildSearchArgument('SEARCH', 'ALL');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchAnswered()
    {
        $this->buildSearchArgument('SEARCH', 'ANSWERED');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchBcc($string)
    {
        $comparator = '"' . $string . '"';
        $this->buildSearchArgument('SEARCH', 'BCC', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchBefore(\DateTimeImmutable $dateTime)
    {
        $comparator = $dateTime->format(Format::RFC2822_DATE_DAY_MONTH_YEAR);
        $this->buildSearchArgument('SEARCH', 'BEFORE', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchBody($string)
    {
        $comparator = '"' . $string . '"';
        $this->buildSearchArgument('SEARCH', 'BODY', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchCc($string)
    {
        $comparator = '"' . $string . '"';
        $this->buildSearchArgument('SEARCH', 'CC', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchDeleted()
    {
        $this->buildSearchArgument('SEARCH', 'DELETED');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchDraft()
    {
        $this->buildSearchArgument('SEARCH', 'DRAFT');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchFlagged()
    {
        $this->buildSearchArgument('SEARCH', 'FLAGGED');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchFrom($string)
    {
        $comparator = '"' . $string . '"';
        $this->buildSearchArgument('SEARCH', 'FROM', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchHeader($name, $string)
    {
        $comparator = $name . ':' . '"' . $string . '"';
        $this->buildSearchArgument('SEARCH', 'HEADER', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchKeyword($keyword)
    {
        $comparator = '"' . $keyword . '"';
        $this->buildSearchArgument('SEARCH', 'KEYWORD', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchLarger($n)
    {
        $this->buildSearchArgument('SEARCH', 'LARGER', $n);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchNew()
    {
        $this->buildSearchArgument('SEARCH', 'NEW');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchNot()
    {
        $this->buildSearchArgument('SEARCH', 'NOT');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchOld()
    {
        $this->buildSearchArgument('SEARCH', 'OLD');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchOn(\DateTimeImmutable $date)
    {
        $comparator = $date->format(Format::RFC2822_DATE_DAY_MONTH_YEAR);
        $this->buildSearchArgument('SEARCH', 'ON', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchOr()
    {
        $this->buildSearchArgument('SEARCH', 'OR');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchRecent()
    {
        $this->buildSearchArgument('SEARCH', 'RECENT');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchSeen()
    {
        $this->buildSearchArgument('SEARCH', 'SEEN');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchSentBefore(\DateTimeImmutable $date)
    {
        $comparator = $date->format(Format::RFC2822_DATE_DAY_MONTH_YEAR);
        $this->buildSearchArgument('SEARCH', 'SENTBEFORE', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchSentOn(\DateTimeImmutable $date)
    {
        $comparator = $date->format(Format::RFC2822_DATE_DAY_MONTH_YEAR);
        $this->buildSearchArgument('SEARCH', 'SENTON', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchSentSince(\DateTimeImmutable $date)
    {
        $comparator = $date->format(Format::RFC2822_DATE_DAY_MONTH_YEAR);
        $this->buildSearchArgument('SEARCH', 'SENTSINCE', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchSince(\DateTimeImmutable $date)
    {
        $comparator = $date->format(Format::RFC2822_DATE_DAY_MONTH_YEAR);
        $this->buildSearchArgument('SEARCH', 'SINCE', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchSmaller($n)
    {
        $this->buildSearchArgument('SEARCH', 'SMALLER', $n);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchSubject($string)
    {
        $comparator = '"' . $string . '"';
        $this->buildSearchArgument('SEARCH', 'SUBJECT', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchText($string)
    {
        $comparator = '"' . $string . '"';
        $this->buildSearchArgument('SEARCH', 'TEXT', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchTo($string)
    {
        $comparator = '"' . $string . '"';
        $this->buildSearchArgument('SEARCH', 'TEXT', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchUid($string)
    {
        $comparator = $string;
        $this->buildSearchArgument('SEARCH', 'UID', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchUnanswered()
    {
        $this->buildSearchArgument('SEARCH', 'UNANSWERED');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchUndeleted()
    {
        $this->buildSearchArgument('SEARCH', 'UNDELETED');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchUndraft()
    {
        $this->buildSearchArgument('SEARCH', 'UNDRAFT');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchUnflagged()
    {
        $this->buildSearchArgument('SEARCH', 'UNFLAGGED');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchUnkeyword($keyword)
    {
        $comparator = '"' . $keyword . '"';
        $this->buildSearchArgument('SEARCH', 'UNKEYWORD', $comparator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function searchUnseen()
    {
        $this->buildSearchArgument('SEARCH', 'UNSEEN');
        return $this;

    }

    /**
     * {@inheritDoc}
     */
    public function status($mailbox)
    {
        $command = 'STATUS';
        $this->command = $command;
        $this->asString .= $this->prefixWhitespace($command);
        $this->arguments = array('MAILBOX' => $mailbox, 'INCLUDE' => array());
        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withMessages()
    {
        $this->arguments['INCLUDE'][] = 'MESSAGES';
        return $this;

    }

    /**
     * {@inheritDoc}
     */
    public function withRecent()
    {
        $this->arguments['INCLUDE'][] = 'RECENT';
        return $this;

    }

    /**
     * {@inheritDoc}
     */
    public function withUidNext()
    {
        $this->arguments['INCLUDE'][] = 'UIDNEXT';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withUidValidity()
    {
        $this->arguments['INCLUDE'][] = 'UIDVALIDITY';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withUnseen()
    {
        $this->arguments['INCLUDE'][] = 'UNSEEN';
        return $this;
    }


    /**
     * {@inheritDoc}
     */
    public function store()
    {
        $command = 'STORE';
        $this->asString .= $this->prefixWhitespace($command);
        $arguments = array(
            'FLAGS' => array(),
            '+FLAGS' => array(),
            '-FLAGS' => array()

        );

        if ($this->command === 'UID') {
            $this->arguments['STORE'] = $arguments;
        } else {
            $this->command = $command;
        }

        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function replaceFlag($flag)
    {
        if ($this->command === 'UID') {
            $this->arguments['STORE']['FLAGS'][] = '\\' . $flag;
        } else {
            $this->arguments['FLAGS'][] = '\\' . $flag;
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addFlag($flag)
    {
        if ($this->command === 'UID') {
            $this->arguments['STORE']['+FLAGS'][] = '\\' . $flag;
        } else {
            $this->arguments['+FLAGS'][] = '\\' . $flag;
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeFlag($flag)
    {
        if ($this->command === 'UID') {
            $this->arguments['STORE']['-FLAGS'][] = '\\' . $flag;
        } else {
            $this->arguments['-FLAGS'][] = '\\' . $flag;
        }
        return $this;
    }


    /**
     * {@inheritDoc}
     */
    public function flags()
    {
        $command = 'FETCH';
        $argument = 'FLAGS';
        if ($this->command === 'UID') {
            // fetch is an argument
            $this->arguments[$command]['FIELDS'][] = $argument;
        } else {
            // fetch is a command
            $this->arguments['FIELDS'][] = $argument;
        }

        $this->asString .= $this->prefixWhitespace($argument);

        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addCommandValidator(CommandValidationInterface $commandValidation)
    {
        $this->validators[$commandValidation->command()] = $commandValidation;
    }

    /**
     * {@inheritDoc}
     */
    public function uid()
    {
        $this->command = 'UID';
        $this->arguments = array();
        $this->isValidated = false;
        $this->asString = $this->prefixWhitespace($this->command);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch($message)
    {
        $command = 'FETCH';
        if ($this->command === 'UID') {
            // fetch is an argument
            $this->arguments = array($command => array('MESSAGE' => $message));
        } else {
            // fetch is a command
            $this->command = $command;
            $this->arguments = array('MESSAGE' => $message);
        }

        $this->isValidated = false;
        $this->asString .= $this->prefixWhitespace($command);
        $this->asString .= $this->prefixWhitespace($message);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchRange($messageFrom, $messageTo)
    {
        $command = 'FETCH';
        $message = $messageFrom . ':' . $messageTo;
        if ($this->command === 'UID') {
            // fetch is an argument
            $this->arguments = array($command => array('MESSAGE' => $message));
        } else {
            // fetch is a command
            $this->command = $command;
            $this->arguments = array('MESSAGE' => $message);
        }

        $this->isValidated = false;
        $this->asString .= $this->prefixWhitespace($command);
        $this->asString .= $this->prefixWhitespace($message);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchSet(MessageList $messages)
    {
        $this->buildSet($messages, 'FETCH');
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function header()
    {
        $command = 'FETCH';
        $argument = 'BODY[HEADER] BODYSTRUCTURE';
        if ($this->command === 'UID') {
            // fetch is an argument
            $this->arguments[$command]['FIELDS'][] = $argument;
        } else {
            // fetch is a command
            $this->arguments['FIELDS'][] = $argument;
        }
        $this->asString .= $this->prefixWhitespace($argument);
        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function body()
    {
        $command = 'FETCH';
        $argument = 'BODY[TEXT]';
        if ($this->command === 'UID') {
            // fetch is an argument
            $this->arguments[$command]['FIELDS'][] = $argument;
        } else {
            // fetch is a command
            $this->arguments['FIELDS'][] = $argument;
        }

        $this->asString .= $this->prefixWhitespace($argument);

        $this->isValidated = false;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function uids()
    {
        $command = 'FETCH';
        $argument = 'UID';
        if ($this->command === 'UID') {
            // fetch is an argument
            $this->arguments[$command]['FIELDS'][] = $argument;
        } else {
            // fetch is a command
            $this->arguments['FIELDS'][] = $argument;
        }

        $this->asString .= $this->prefixWhitespace($argument);

        $this->isValidated = false;
        return $this;
    }

    /**
     * add white space to the beginning of string
     * @param string $string
     * @return string
     */
    private function prefixWhitespace($string)
    {
        return "\x20" . $string;
    }

    private function buildSet(MessageList $messages, $command)
    {
        $messageArray = array();
        if ($this->command === 'UID') {
            // fetch is an argument

            foreach ($messages as $message) {
                $messageArray[] = $message->uid();
            }
            $messageNumbers = implode(',', $messageArray);
            $this->arguments = array($command => array('MESSAGE' => $messageNumbers));
        } else {
            // fetch is a command
            foreach ($messages as $message) {
                $messageArray[] = $message->number();
            }
            $messageNumbers = implode(',', $messageArray);
            $this->command = $command;
            $this->arguments = array('MESSAGE' => $messageNumbers);
        }

        $this->isValidated = false;
        $this->asString .= $this->prefixWhitespace($command);
        $this->asString .= $this->prefixWhitespace($messageNumbers);
    }

    private function buildSequence(MessageList $messages, $command)
    {
        $messageArray = array();
        foreach ($messages as $message) {
            if ($this->command === 'UID') {
                $messageArray[] = $message->uid();
            } else {
                $messageArray[] = $message->number();
            }
        }
        return implode(' ', $messageArray);
    }

    /**
     * Used to keep this class DRY
     * @param string $command
     * @param string $argument
     * @param null|string $comparator
     */
    private function buildSearchArgument($command, $argument, $comparator = null)
    {
        $this->asString .= $this->prefixWhitespace($argument);
        if ($comparator !== null) {
            $this->asString .= $this->prefixWhitespace($comparator);
        }

        if ($this->command === 'UID') {
            $this->arguments[$command][] = $argument;
            if ($comparator !== null) {
                $this->arguments[$command][] = $comparator;
            }
        } else {
            $this->arguments[] = $argument;
            if ($comparator !== null) {
                $this->arguments[] = $comparator;
            }
        }
    }

    private function buildGroup(array $haystack)
    {
        return '(' . implode("\x20", $haystack) . ')';
    }

    private function buildStore()
    {
        $command = 'STORE';
        $this->asString = $this->prefixWhitespace($this->command);

        if ($this->command === 'UID') {
            $arguments = $this->arguments[$command];
            $this->asString .= $this->prefixWhitespace($command);
        } else {
            $arguments = $this->arguments;
            $this->command = $command;
        }

        if (array_key_exists('MESSAGE', $arguments)) {
            $this->asString .= $this->prefixWhitespace($arguments['MESSAGE']);
        }

        if (!empty($arguments['+FLAGS'])) {
            $this->asString .= $this->prefixWhitespace('+FLAGS.SILENT');
            $this->asString .= $this->prefixWhitespace($this->buildGroup($arguments['+FLAGS']));
        }

        if (!empty($arguments['-FLAGS'])) {
            $this->asString .= $this->prefixWhitespace('-FLAGS.SILENT');
            $this->asString .= $this->prefixWhitespace($this->buildGroup($arguments['-FLAGS']));
        }
        if (!empty($arguments['FLAGS'])) {
            $this->asString .= $this->prefixWhitespace('FLAGS.SILENT');
            $this->asString .= $this->prefixWhitespace($this->buildGroup($arguments['FLAGS']));
        }
    }

    private function buildFetch()
    {
        $command = 'FETCH';
        $this->asString = $this->prefixWhitespace($this->command);

        if ($this->command === 'UID') {
            $arguments = $this->arguments[$command];
            $this->asString .= $this->prefixWhitespace($command);
        } else {
            $arguments = $this->arguments;
        }
        $this->asString .= $this->prefixWhitespace($arguments['MESSAGE'])
            . $this->prefixWhitespace($this->buildGroup($arguments['FIELDS']));
    }

    private function buildSearch()
    {
        $command = 'SEARCH';
        $this->asString = $this->prefixWhitespace($this->command);

        if ($this->command === 'UID') {
            $arguments = $this->arguments[$command];
            $this->asString .= $this->prefixWhitespace($command);
        } else {
            $arguments = $this->arguments;
        }

        foreach ($arguments as $argumentKey => $argument) {
            $this->asString .= $this->prefixWhitespace($argument);
        }
    }


    private function buildCopy()
    {
        $command = 'COPY';
        $this->asString = $this->prefixWhitespace($this->command);

        if ($this->command === 'UID') {
            $arguments = $this->arguments[$command];
            $this->asString .= $this->prefixWhitespace($command);
            $this->asString = $this->prefixWhitespace($this->command)
                . $this->prefixWhitespace($command)
                . $this->prefixWhitespace($arguments['MESSAGE'])
                . $this->prefixWhitespace($arguments['MAILBOX']);
        } else {
            $arguments = $this->arguments;
            $this->asString = $this->prefixWhitespace($command)
                . $this->prefixWhitespace($arguments['MESSAGE'])
                . $this->prefixWhitespace($arguments['MAILBOX']);
        }
    }
}