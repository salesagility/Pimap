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


namespace SalesAgility\Imap\Stream;


use Psr\Log\LoggerInterface;
use SalesAgility\Imap\CommandBuilder\CommandBuildInterface;
use SalesAgility\Imap\Interpreter\Rfc2822Interpreter;
use SalesAgility\Imap\Pipeline\PipelineInterface;
use SalesAgility\Imap\Response\Mailbox;
use SalesAgility\Imap\Response\MailboxList;
use SalesAgility\Imap\Response\Message;
use SalesAgility\Imap\Response\MessageAttachment;
use SalesAgility\Imap\Response\MessageAttachmentStructure;
use SalesAgility\Imap\Response\MessageFactory;
use SalesAgility\Imap\Response\MessageFlags;
use SalesAgility\Imap\Response\MessageList;
use SalesAgility\Imap\Response\Response;
use SalesAgility\Imap\CommandBuilder\CommandBuildArgumentsInterface;
use SalesAgility\Imap\ImapException;
use SalesAgility\Iteration\StringIterator;
use SalesAgility\Stream\StreamConnectionInterface;
use SalesAgility\Utility\StringValue;

/**
 * Class PhpImapExtensionMessageTransporter
 * @package SalesAgility\Imap\Stream
 */
class PhpImapExtensionMessageTransporter implements CommandTransporterInterface
{
    /** @var PhpImpExtensionConnection */
    private $connection;

    /** @var PipelineInterface */
    private $pipeline;

    public function __construct($container)
    {
    }

    /**
     * @param PipelineInterface $pipeline
     */
    public function setPipeLine(PipelineInterface $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @return PipelineInterface
     */
    public function pipeline()
    {
        return $this->pipeline;
    }

    /**
     * Not Implemented
     * @param StreamConnectionInterface $connection
     * @throws \Exception
     */
    public function setConnection(StreamConnectionInterface $connection)
    {
        if ($connection instanceof PhpImpExtensionConnection) {
            $this->connection = $connection;
        } else {
            throw new \Exception('Native client is only compatible with PhpImpExtensionConnection');
        }
    }

    /**
     * @return StreamConnectionInterface
     */
    public function connection()
    {
        return $this->connection;
    }

    /**
     * Not Implemented
     * @param string $string
     * @throws \Exception
     */
    public function transmit($string)
    {
        throw new \Exception('Native Php Client handles it\'s own transmission');
    }

    /**
     * Not Implemented
     * @return string|void
     * @throws \Exception
     */
    public function receive()
    {
        throw new \Exception('Native Php Client handles it\'s own transmission');
    }

    /**
     * Not Implemented
     * @param string $string
     * @return bool|void
     * @throws \Exception
     */
    public function isEndOfFile($string)
    {
        throw new \Exception('Native Php Client handles it\'s own transmission');
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return mixed
     * @throws ImapException
     * @throws \Exception
     */
    public function transmitCommand(CommandBuildArgumentsInterface $command)
    {
        // cases are ordered by most likely to be used
        switch ($command->command()) {
            case 'LOGIN':
                return $this->login($command);
            case 'SELECT':
                return $this->select($command);
            case 'FETCH':
                return $this->fetch($command);
            case 'SEARCH':
                return $this->search($command);
            case 'UID':
                return $this->uid($command);
            case 'STATUS':
                return $this->select($command);
            case 'LIST':
                return $this->listMailboxes($command);
            case 'LSUB':
                return $this->lsub($command);
            case 'LOGOUT':
                return $this->logout($command);
            case 'EXPUNGE':
                return $this->expunge($command);
            case 'CHECK':
                return $this->check($command);
            case 'EXAMINE':
                return $this->select($command);
            case 'STORE':
                return $this->store($command);
            case 'NOOP':
                return $this->noop($command);
            case 'CREATE':
                return $this->create($command);
            case 'RENAME':
                return $this->rename($command);
            case 'DELETE':
                return $this->delete($command);
            case 'SUBSCRIBE':
                return $this->subscribe($command);
            case 'UNSUBSCRIBE':
                return $this->unsubscribe($command);
            case 'CLOSE':
                return $this->close($command);
            case 'COPY':
                return $this->copy($command);
            case 'APPEND':
                return $this->append($command);
            default:
                throw new \Exception('Command Not Supported: ' . $command->command());
        }
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return mixed
     * @throws \Exception
     */
    private function uid(CommandBuildArgumentsInterface $command)
    {
        $this->addCommand($command);
        if (array_key_exists('FETCH', $command->commandArguments())) {
            return $this->fetch($command, true);
        } elseif (array_key_exists('SEARCH', $command->commandArguments())) {
            return $this->search($command, true);
        } elseif (array_key_exists('STORE', $command->commandArguments())) {
            return $this->store($command, true);
        } elseif (array_key_exists('COPY', $command->commandArguments())) {
            return $this->copy($command, true);
        } else {
            throw new \Exception('UID Command Not Supported: ' . $command->command());
        }
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function noop(CommandBuildArgumentsInterface $command)
    {
        $this->addCommand($command);
        imap_ping($this->connection->connection);
        $this->checkErrors();
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK Nothing Happened.' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));

    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function login(CommandBuildArgumentsInterface $command)
    {
        $this->addCommand($command);
        $arg = $command->commandArguments();
        $this->connection->username = $arg['USER'];
        $this->connection->password = $arg['PASSWORD'];
        $this->connection->connect();
        $this->checkErrors();
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK login success.' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     */
    private function logout(CommandBuildArgumentsInterface $command)
    {
        $this->addCommand($command);
        $this->connection->disconnect();
        $includedMessage = StringIterator::withLiteral('* BYE Logging out' . "\r\n");
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK Logout completed.' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Mailbox
     * @throws \Exception
     */
    private function select(CommandBuildArgumentsInterface $command)
    {
        $this->addCommand($command);
        $arg = $command->commandArguments();
        $selected = '{' . $this->connection->server . ':' . $this->connection->port . $this->connection->security . '}' . $arg['MAILBOX'];
        imap_reopen($this->connection->connection, $selected);
        $this->checkErrors();
        $mailbox = new Mailbox();
        $this->connection->mailbox = $arg['MAILBOX'];
        $mailbox->offsetSet('exists', (string)imap_num_msg($this->connection->connection));
        $this->checkErrors();
        $mailbox->offsetSet('recent', (string)imap_num_recent($this->connection->connection));
        $this->checkErrors();
        return $this->addResponse($mailbox);
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @param bool $isUid
     * @return array|MessageList
     * @throws \Exception
     */
    private function fetch(CommandBuildArgumentsInterface $command, $isUid = false)
    {
        $this->addCommand($command);
        if ($isUid) {
            $arguments = $command->commandArguments()['FETCH'];
        } else {
            $arguments = $command->commandArguments();
        }

        $messageList = new MessageList();
        $rfc2822 = new Rfc2822Interpreter();

        // populate header fields
        // fetch()->header()->uid()->flags
        $headers = $this->imap_fetch_overview($this->connection->connection, $arguments, $isUid);
        $this->checkErrors();
        foreach ($headers as $header) {
            $message = MessageFactory::instance();
            $messageFlags = new MessageFlags();
            foreach ($header as $fieldName => $fieldValue) {
                if ($message->header()->offsetExists($fieldName)) {
                    // header field
                    $mapped = $this->mapHeaderFieldName($fieldName);
                    $rfc2822->parseHeaderField($mapped, $fieldValue, $message);
                } elseif ($messageFlags->offsetExists($this->mapHeaderFieldName($fieldName))) {
                    // flags
                    if ($fieldValue === 1) {
                        $messageFlags->offsetSet($this->mapHeaderFieldName($fieldName), (bool)$fieldValue);
                    }
                } elseif ($fieldName === 'message_id') {
                    // map to header
                    $fn = 'messageId';
                    $rfc2822->parseHeaderField($fn, $fieldValue, $message);
                } elseif ($fieldName === 'msgno') {
                    $message->offsetSet('number', (string)$fieldValue);
                } elseif ($fieldName === 'uid') {
                    $message->offsetSet('uid', (string)$fieldValue);
                }
            }

            $message->offsetSet('flags', $messageFlags);

            $bodystructure = $this->imap_fetchstructure($this->connection->connection, $message, $isUid);
            $this->checkErrors();
            $message->body()->structure()->offsetSet('attachments', (bool)$bodystructure->ifdisposition);
            // fetch()->body()
            if (array_search('BODY[TEXT]', $arguments['FIELDS']) !== false) {
                $this->fetchBody($this->connection->connection, $message, $bodystructure, $isUid);
            }

            $messageList[] = $message;
        }

        return $this->addResponse($messageList);
    }

    /**
     * @param resource $connection
     * @param Message $message
     * @param \stdClass|array $bodystructure
     * @param bool $isUid
     * @throws \Exception
     * @see http://php.net/manual/en/function.imap-fetchstructure.php#85486
     */
    private function fetchBody($connection, &$message, $bodystructure, $isUid = false)
    {
        if (!isset($bodystructure->parts)) {
            // plain
            $this->fetchBodyParts($connection, $message, $bodystructure, 0, $isUid);
        } else {
            // multipart
            foreach ($bodystructure->parts as $part => $parameters) {
                $this->fetchBodyParts($connection, $message, $parameters, $part + 1, $isUid);
            }
        }
    }

    /**
     * @param resource $connection
     * @param Message $message
     * @param array $parameters
     * @param integer|string $part
     * @param bool $isUid
     * @throws \Exception
     * @see http://php.net/manual/en/function.imap-fetchstructure.php#85486
     */
    private function fetchBodyParts($connection, &$message, $parameters, $part, $isUid = false)
    {
        // DECODE DATA
        $data = ($part) ?
            // multipart
            $this->imap_fetchbody($connection, $message, $part, $isUid) :
            // plain text
            $this->imap_body($connection, $message, $isUid);
        $this->checkErrors();
        // Any part may be encoded, even plain text messages, so check everything.
        if ($parameters->encoding == 4) {
            $data = quoted_printable_decode($data);
        } elseif ($parameters->encoding == 3) {
            $data = base64_decode($data);
        }

        // PARAMETERS
        // get all parameters, like charset, filenames of attachments, etc.
        $params = array();
        if (isset($parameters->parameters)) {
            foreach ($parameters->parameters as $parameter) {
                $params[strtolower($parameter->attribute)] = $parameter->value;
            }
        }

        if (isset($parameters->dparameters)) {
            foreach ($parameters->dparameters as $dparameter) {
                $params[strtolower($parameter->attribute)] = $dparameter->value;
            }
        }

        // ATTACHMENT
        if (array_key_exists('filename', $params) || array_key_exists('name', $params)) {
            $filename = (array_key_exists('filename', $params)) ? $params['filename'] : $params['name'];
            $attachment = $this->buildAttachment();
            // if inline
            if (isset($parameters->disposition) && strtolower($parameters->disposition) === 'inline') {
                $attachment->offsetSet('isInline', true);
                $attachment->offsetSet('contentId', trim($parameters->id, '<>'));
            } else {
                $attachment->offsetSet('isInline', false);
            }

            $attachment->offsetSet('content', $data);
            $attachment->offsetSet('hasContent', true);
            $finfo = new \finfo(FILEINFO_MIME);
            $attachment->structure()->offsetSet('type', $finfo->buffer($data));
            $attachment->structure()->offsetSet('size', $parameters->bytes);
            $attachment->structure()->offsetSet('name', $filename);
            $message->body()->offsetSet('attachments', $attachment);
        }

        // TEXT
        if ($parameters->type == 0 && $data) {
            if (strtolower($parameters->subtype) == 'plain') {
                $message['body']['text'] .= $data;
            } else {
                $message['body']['html'] .= $data;
            }
        } elseif ($parameters->type == 2 && $data) {
            $message['body']['text'] .= $data;
        }

        // SUBPART RECURSION
        if (isset($parameters->parts)) {
            foreach ($parameters->parts as $subpart => $subpartParameters) {
                // 1.2, 1.2.1, etc.
                $this->fetchBodyParts($connection, $message, $subpartParameters, $part . '.' . ($subpart + 1));
            }
        }
    }

    /**
     * @param $connection
     * @param $arguments
     * @param $isUid
     * @return array|mixed
     */
    private function imap_fetch_overview($connection, $arguments, $isUid)
    {
        return ($isUid) ?
            imap_fetch_overview($connection, $arguments['MESSAGE'], FT_UID) :
            imap_fetch_overview($connection, $arguments['MESSAGE'], 0);
    }

    /**
     * @param $connection
     * @param $message
     * @param $isUid
     * @return array|mixed|object
     */
    private function imap_fetchstructure($connection, $message, $isUid)
    {
        return ($isUid) ?
            imap_fetchstructure($connection, $message->number(), FT_UID) :
            imap_fetchstructure($connection, $message->number());
    }

    /**
     * @param $connection
     * @param $message
     * @param $part
     * @param $isUid
     * @return array|mixed|string
     */
    private function imap_fetchbody($connection, $message, $part, $isUid)
    {
        return ($isUid) ?
            imap_fetchbody($connection, $message->number(), $part, FT_UID) :
            imap_fetchbody($connection, $message->number(), $part);
    }

    /**
     * @param $connection
     * @param $message
     * @param $isUid
     * @return array|mixed|string
     */
    private function imap_body($connection, $message, $isUid)
    {
        return ($isUid) ?
            imap_body($connection, $message->number(), FT_UID) : imap_body($connection, $message->number());
    }

    /**
     * @param $fieldName
     * @return string
     */
    private function mapHeaderFieldName($fieldName)
    {
        return ucwords($fieldName, '-');
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

    /**
     * @param CommandBuildArgumentsInterface $command
     * @param bool $isUid
     * @return array|MessageList
     * @throws \Exception
     */
    private function search(CommandBuildArgumentsInterface $command, $isUid = false)
    {
        $this->addCommand($command);
        if ($isUid) {
            $arguments = $command->commandArguments()['SEARCH'];
            $messages = imap_search($this->connection->connection, implode(' ', $arguments), FT_UID);
        } else {
            $arguments = $command->commandArguments();
            $messages = imap_search($this->connection->connection, implode(' ', $arguments));
        }

        $this->checkErrors();

        $messageList = new MessageList();
        foreach ($messages as $messageNumber) {
            $message = new Message();
            $message->offsetSet('number', $messageNumber->number());
            $messageList[] = $message;
        }

        return $this->addResponse($messageList);
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @param bool $isUid
     * @return mixed
     * @throws \Exception
     */
    private function store(CommandBuildArgumentsInterface $command, $isUid = false)
    {
        $this->addCommand($command);
        $arguments = $command->commandArguments();
        $flags = array();

        if (isset($arguments['-FLAGS'])) {
            foreach ($arguments['-FLAGS'] as $flag) {
                $flags[] = '\\' . $flag;
            }

            if ($isUid) {
                imap_clearflag_full($this->connection->connection, $command->commandArguments()['MESSAGE'], implode(' ', $flags), FT_UID);
            } else {
                imap_clearflag_full($this->connection->connection, $command->commandArguments()['MESSAGE'], implode(' ', $flags));
            }
        } else {
            if (isset($arguments['FLAGS'])) {
                foreach ($arguments['FLAGS'] as $flag) {
                    $flags[] = '\\' . $flag;
                }
            }

            if (isset($arguments['+FLAGS'])) {
                foreach ($arguments['+FLAGS'] as $flag) {
                    $flags[] = '\\' . $flag;
                }
            }

            if ($isUid) {
                imap_setflag_full($this->connection->connection, $command->commandArguments()['MESSAGE'], implode(' ', $flags), FT_UID);
            } else {
                imap_setflag_full($this->connection->connection, $command->commandArguments()['MESSAGE'], implode(" ", $arguments['FLAGS']));
            }
        }

        $this->checkErrors();

        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    private function copy(CommandBuildArgumentsInterface $command, $isUid = false)
    {
        $this->addCommand($command);
        $arguments = $command->commandArguments();
        if ($isUid) {
            $arguments = $command->commandArguments()['COPY'];
            $mailbox = $arguments['MAILBOX'];
            $message = $arguments['MESSAGE'];

            imap_mail_copy($this->connection->connection, $message, $mailbox, FT_UID);
        } else {
            $arguments = $command->commandArguments();
            $mailbox = $arguments['MAILBOX'];
            $message = $arguments['MESSAGE'];
            imap_mail_copy($this->connection->connection, $message, $mailbox);
        }

        $this->checkErrors();

        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }


    /**
     * @param CommandBuildArgumentsInterface $command
     * @return array|MailboxList
     */
    private function listMailboxes(CommandBuildArgumentsInterface $command)
    {
        $ref = $command->commandArguments()['REFERENCE_NAME'];
        $mailbox = $command->commandArguments()['MAILBOX'];

        $mailboxes = imap_list($this->connection->connection, $this->buildRef($ref), $mailbox);
        $this->checkErrors();

        return $this->mapList($mailboxes, $this->buildRef($ref), $command);
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return array|MailboxList
     */
    private function lsub(CommandBuildArgumentsInterface $command)
    {
        // imap_lsub
        $ref = $command->commandArguments()['REFERENCE_NAME'];
        $mailbox = $command->commandArguments()['MAILBOX'];

        $mailboxes = imap_lsub($this->connection->connection, $this->buildRef($ref), $mailbox);
        $this->checkErrors();

        return $this->mapList($mailboxes, $this->buildRef($ref), $command);
    }

    private function buildRef($ref)
    {
        if (empty($ref)) {
            $ref = '{' . $this->connection->server . ':' . $this->connection->port . $this->connection->security . '}';
        }

        return $ref;
    }

    private function mapList($mailboxes, $ref, CommandBuildArgumentsInterface $command)
    {
        $mailboxList = new MailboxList();
        foreach ($mailboxes as $mailbox) {
            $rmailbox = new Mailbox();
            if (StringValue::startsWith($mailbox, $ref)) {
                $rmailbox->offsetSet('name', substr($mailbox, strlen($ref)));
                $rmailbox->offsetSet('hierarchy', $command->commandArguments()['REFERENCE_NAME']);
            } else {
                $rmailbox->offsetSet('name', $mailbox);
                $rmailbox->offsetSet('hierarchy', $command->commandArguments()['REFERENCE_NAME']);

            }

            $mailboxList[] = $rmailbox;
        }
        return $mailboxList;
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function check(CommandBuildArgumentsInterface $command)
    {

        $object = imap_check($this->connection->connection);
        $this->checkErrors();

        $this->addCommand($command);
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function create(CommandBuildArgumentsInterface $command)
    {
        imap_createmailbox($this->connection->connection, $command->commandArguments()['MAILBOX']);
        $this->checkErrors();

        $this->addCommand($command);
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function delete(CommandBuildArgumentsInterface $command)
    {
        //imap_delete
        imap_delete($this->connection->connection, $command->commandArguments()['MAILBOX']);
        $this->checkErrors();

        $this->addCommand($command);
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function rename(CommandBuildArgumentsInterface $command)
    {
        imap_renamemailbox($this->connection->connection, $command->commandArguments()['MAILBOX'], $command->commandArguments()['NEW_MAILBOX']);
        $this->checkErrors();

        $this->addCommand($command);
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function subscribe(CommandBuildArgumentsInterface $command)
    {
        imap_subscribe($this->connection->connection, $command->commandArguments()['MAILBOX']);
        $this->checkErrors();

        $this->addCommand($command);
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function unsubscribe(CommandBuildArgumentsInterface $command)
    {
        imap_unsubscribe($this->connection->connection, $command->commandArguments()['MAILBOX']);
        $this->checkErrors();

        $this->addCommand($command);
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function close(CommandBuildArgumentsInterface $command)
    {
        imap_expunge($this->connection->connection);
        $this->checkErrors();

        $this->addCommand($command);
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function expunge(CommandBuildArgumentsInterface $command)
    {
        imap_expunge($this->connection->connection);
        $this->checkErrors();

        $this->addCommand($command);
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return Response
     * @throws \Exception
     */
    private function append(CommandBuildArgumentsInterface $command)
    {
        imap_append($this->connection->connection, $command->commandArguments()['MAILBOX'], $command->commandArguments()['MESSAGE']);
        $this->checkErrors();

        $this->addCommand($command);
        $includedMessage = StringIterator::withLiteral('', 0, 0);
        $responseMessage = StringIterator::withLiteral($this->pipeline->getLastPipe()->getTag() . ' OK ' . "\r\n");
        return $this->addResponse(new Response('OK', $responseMessage, $includedMessage));
    }

    /**
     * @throws \Exception
     */
    private function checkErrors()
    {
        if (imap_last_error()) {
            throw new \Exception(imap_last_error());
        }
    }

    /**
     * Add command to pipeline
     * @param CommandBuildInterface $command
     */
    private function addCommand(CommandBuildInterface $command)
    {
        $this->pipeline->add($command);
    }

    /**
     * Add Response to pipeline
     * @param mixed $response
     * @return mixed
     */
    private function addResponse($response)
    {
        $this->pipeline->getLastPipe()->addParsed($response);
        return $response;
    }
}