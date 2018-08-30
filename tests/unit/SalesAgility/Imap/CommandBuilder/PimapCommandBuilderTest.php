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

use SalesAgility\Imap\CommandBuilder\PimapCommandBuilder;

class PimapCommandBuilderTest extends \Codeception\Test\Unit
{

    /** @var ReflectionClass */
    protected static $reflection;
    /** @var ReflectionProperty */
    protected static $propertyCommandPrefix;
    /** @var ReflectionProperty */
    protected static $propertyCommand;
    /** @var ReflectionProperty */
    protected static $propertyArguments;
    /** @var ReflectionProperty */
    protected static $propertyAsString;
    /** @var ReflectionProperty */
    protected static $propertyIsValidated;
    /** @var ReflectionProperty */
    protected static $propertyValidators;
    /** @var ReflectionProperty */
    protected static $propertyIsRaw;

    protected function _before()
    {
        if(self::$reflection === null) {
            // we will need these to check all the properties
            // lets just declare it once.
            // It will help keep this class as small as possible.
            // It will make it a little easier to read.
            self::$reflection = new \ReflectionClass(PimapCommandBuilder::class);

            self::$propertyCommandPrefix = self::$reflection->getProperty('commandPrefix');
            self::$propertyCommand = self::$reflection->getProperty('command');
            self::$propertyArguments = self::$reflection->getProperty('arguments');
            self::$propertyAsString = self::$reflection->getProperty('asString');
            self::$propertyIsValidated = self::$reflection->getProperty('isValidated');
            self::$propertyValidators = self::$reflection->getProperty('validators');
            self::$propertyIsRaw = self::$reflection->getProperty('isRaw');

            self::$propertyCommandPrefix->setAccessible(true);
            self::$propertyCommand->setAccessible(true);
            self::$propertyArguments->setAccessible(true);
            self::$propertyAsString->setAccessible(true);
            self::$propertyIsValidated->setAccessible(true);
            self::$propertyValidators->setAccessible(true);
            self::$propertyIsRaw->setAccessible(true);
        }
    }

    // Constructor
    public function testInstance()
    {
        $object = PimapCommandBuilder::instance();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals('', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    // Command Builder Util Functions
    public function testUntagged()
    {
        $object = PimapCommandBuilder::instance()->raw('test')->build()->untagged();
        
        $this->assertEquals('*', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('test', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals(' test', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(true, self::$propertyIsRaw->getValue($object));
    }

    public function testTagged()
    {
        $object = PimapCommandBuilder::instance()->raw('test')->build()->tagged('A1');
        
        $this->assertEquals('A1', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('test', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals(' test', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(true, self::$propertyIsRaw->getValue($object));
    }

    public function testBuild()
    {
        $object = PimapCommandBuilder::instance();
        $validator =  new \SalesAgility\Imap\CommandBuilder\CommandValidator\Command\NoopCommandValidator();
        $object->addCommandValidator(
            $validator
        );
        $object = $object->noop()->build();

        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('NOOP', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals(' NOOP', self::$propertyAsString->getValue($object));
        $this->assertEquals(true, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array('NOOP' => $validator), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testCommand()
    {
        $object = PimapCommandBuilder::instance()->raw('test')->build()->command();
        $this->assertEquals('test', $object);
    }

    public function testCommandArguments()
    {
        $object = PimapCommandBuilder::instance()->login()->user('user')->password('password');
        $expect = array(
            'USER' => 'user',
            'PASSWORD' => 'password'
        );
        $this->assertEquals($expect, $object->build()->commandArguments());
    }

    public function testAddCommandValidator()
    {
        $object = PimapCommandBuilder::instance();
        $validator =  new \SalesAgility\Imap\CommandBuilder\CommandValidator\Command\NoopCommandValidator();
        $object->addCommandValidator(
            $validator
        );

        $this->assertEquals(array('NOOP' => $validator), self::$propertyValidators->getValue($object));
    }

    public function testCommandPrefix()
    {
        $object = PimapCommandBuilder::instance()->raw('test')->build()->untagged();
        $this->assertEquals('*', $object->commandPrefix());
    }

    public function testAsString()
    {
        $object = PimapCommandBuilder::instance()->raw('test')->build()->untagged();
        $this->assertEquals('* test', $object->asString());
    }

    public function testAsArray()
    {
        $object = PimapCommandBuilder::instance()->raw('test')->build();
        $this->assertEquals(array(
            'command' => 'test',
            'argument' => array(),
            'validated' => false,
            'raw' => true,
        ), $object->asArray());
    }

    // Commands &&  Arguments
    public function testRaw()
    {
        $object = PimapCommandBuilder::instance()->raw('test');
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('test', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals(' test', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(true, self::$propertyIsRaw->getValue($object));
    }

    public function testNoop()
    {
        $object = PimapCommandBuilder::instance()->noop();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('NOOP', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals(' NOOP', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testUid()
    {
        $object = PimapCommandBuilder::instance()->uid()->fetch(1)->header()->body()->flags()->uids()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('UID', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'FETCH' => array(
                'MESSAGE' => 1,
                'FIELDS' => array(
                    'BODY[HEADER] BODYSTRUCTURE',
                    'BODY[TEXT]',
                    'FLAGS',
                    'UID'
                )
            )
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' UID FETCH 1 (BODY[HEADER] BODYSTRUCTURE BODY[TEXT] FLAGS UID)', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testFetch()
    {
        $object = PimapCommandBuilder::instance()->fetch(1)->header()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('FETCH', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'MESSAGE' => 1,
            'FIELDS' => array(
                'BODY[HEADER] BODYSTRUCTURE'
            )
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' FETCH 1 (BODY[HEADER] BODYSTRUCTURE)', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testFetchRange()
    {
        $object = PimapCommandBuilder::instance()->fetchRange(1, 2)->header()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('FETCH', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'MESSAGE' => '1:2',
            'FIELDS' => array(
                'BODY[HEADER] BODYSTRUCTURE'
            )
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' FETCH 1:2 (BODY[HEADER] BODYSTRUCTURE)', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testFetchSet()
    {
        $messageList = new \SalesAgility\Imap\Response\MessageList();

        $message = new \SalesAgility\Imap\Response\Message();
        $message->offsetSet('number', '1');
        $message->offsetSet('uid', '1');
        $messageList[] = $message;

        $message = new \SalesAgility\Imap\Response\Message();
        $message->offsetSet('number', '3');
        $message->offsetSet('uid', '3');
        $messageList[] = $message;

        $object = PimapCommandBuilder::instance()->fetchSet($messageList)->header()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('FETCH', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'MESSAGE' => '1,3',
            'FIELDS' => array(
                'BODY[HEADER] BODYSTRUCTURE'
            )
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' FETCH 1,3 (BODY[HEADER] BODYSTRUCTURE)', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));


        $object = PimapCommandBuilder::instance()->uid()->fetchSet($messageList)->header()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('UID', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'FETCH' => array(
                'MESSAGE' => '1,3',
                'FIELDS' => array(
                    'BODY[HEADER] BODYSTRUCTURE'
                )
            )
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' UID FETCH 1,3 (BODY[HEADER] BODYSTRUCTURE)', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testUids()
    {
        $object = PimapCommandBuilder::instance()->fetch(1)->uids()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('FETCH', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
                'MESSAGE' => 1,
                'FIELDS' => array(
                    'UID'
                )
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' FETCH 1 (UID)', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));

        $object = PimapCommandBuilder::instance()->uid()->fetchRange(1,2)->header()->body()->flags()->uids()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('UID', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'FETCH' => array(
                'MESSAGE' => '1:2',
                'FIELDS' => array(
                    'BODY[HEADER] BODYSTRUCTURE',
                    'BODY[TEXT]',
                    'FLAGS',
                    'UID'
                )
            )
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' UID FETCH 1:2 (BODY[HEADER] BODYSTRUCTURE BODY[TEXT] FLAGS UID)', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }


    public function testFlags()
    {
        $object = PimapCommandBuilder::instance()->fetch(1)->flags()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('FETCH', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'MESSAGE' => 1,
            'FIELDS' => array(
                'FLAGS'
            )
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' FETCH 1 (FLAGS)', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testHeader()
    {
        $object = PimapCommandBuilder::instance()->fetch(1)->header()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('FETCH', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'MESSAGE' => 1,
            'FIELDS' => array(
                'BODY[HEADER] BODYSTRUCTURE'
            )
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' FETCH 1 (BODY[HEADER] BODYSTRUCTURE)', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testBody()
    {
        $object = PimapCommandBuilder::instance()->fetch(1)->body()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('FETCH', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'MESSAGE' => 1,
            'FIELDS' => array(
                'BODY[TEXT]'
            )
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' FETCH 1 (BODY[TEXT])', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }


    public function testX()
    {
        $object = PimapCommandBuilder::instance()->x();
        $this->assertInstanceOf(PimapCommandBuilder::class, $object);
    }

    public function testSelect()
    {
        $object = PimapCommandBuilder::instance()->select('drafts')->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('SELECT', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('MAILBOX' => 'drafts'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' SELECT "drafts"', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testExamine()
    {
        $object = PimapCommandBuilder::instance()->examine('drafts')->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('EXAMINE', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('MAILBOX' => 'drafts'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' EXAMINE drafts', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testCreate()
    {
        $object = PimapCommandBuilder::instance()->create('drafts')->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('CREATE', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('MAILBOX' => 'drafts'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' CREATE drafts', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testDelete()
    {
        $object = PimapCommandBuilder::instance()->delete('drafts')->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('DELETE', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('MAILBOX' => 'drafts'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' DELETE drafts', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testSubscribe()
    {
        $object = PimapCommandBuilder::instance()->subscribe('drafts')->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('SUBSCRIBE', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('MAILBOX' => 'drafts'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' SUBSCRIBE drafts', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testUnsubscribe()
    {
        $object = PimapCommandBuilder::instance()->unsubscribe('drafts')->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('UNSUBSCRIBE', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('MAILBOX' => 'drafts'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' UNSUBSCRIBE drafts', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testListMailbox()
    {
        $object = PimapCommandBuilder::instance()->listMailbox()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('LIST', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('REFERENCE_NAME'=> '', 'MAILBOX' => '*'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' LIST "" "*"', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testListSubsetMailbox()
    {
        $object = PimapCommandBuilder::instance()->listSubsetMailbox()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('LSUB', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('REFERENCE_NAME'=> '', 'MAILBOX' => '*'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' LSUB "" "*"', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testRename()
    {
        $object = PimapCommandBuilder::instance()->rename('INVOICES', 'RECEIPTS')->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('RENAME', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('MAILBOX'=> 'INVOICES', 'NEW_MAILBOX' => 'RECEIPTS'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' RENAME INVOICES RECEIPTS', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testCapability()
    {
        $object = PimapCommandBuilder::instance()->capability()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('CAPABILITY', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals(' CAPABILITY', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testClose()
    {
        $object = PimapCommandBuilder::instance()->close()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('CLOSE', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals(' CLOSE', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testExpunge()
    {
        $object = PimapCommandBuilder::instance()->expunge()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('EXPUNGE', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals(' EXPUNGE', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testAppend()
    {
        $object = PimapCommandBuilder::instance()->append('IN')->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('APPEND', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'MAILBOX' => 'IN',
            'FLAGS' => array(),
            'DATE' => '',
            'MESSAGE' => '{0}'."\r\n\r\n"
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' APPEND '. '{0}'."\r\n\r\n", self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testWithFlag()
    {
        $object = PimapCommandBuilder::instance()->append('IN')->withFlag('Seen')->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('APPEND', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'MAILBOX' => 'IN',
            'FLAGS' => array('Seen'),
            'DATE' => '',
            'MESSAGE' => '{0}'."\r\n\r\n"
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' APPEND '.'(Seen) {0}'."\r\n\r\n", self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }


    public function testWithDate()
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-01-01 1:00:00');
        $object = PimapCommandBuilder::instance()->append('IN')->withFlag('Seen')->withDateTime($date)->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('APPEND', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'MAILBOX' => 'IN',
            'FLAGS' => array('Seen'),
            'DATE' => 'Mon, 01 Jan 2018 01:00:00 +0000',
            'MESSAGE' => '{0}'."\r\n\r\n"
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' APPEND (Seen) Mon, 01 Jan 2018 01:00:00 +0000 '.'{0}'."\r\n\r\n", self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testWithMessage()
    {
        $message = '';
        $message .= "{310}\r\n";
        $message .= "Date: Mon, 7 Feb 1994 21:52:25 -0800 (PST)\r\n";
        $message .= "From: Fred Foobar <foobar@Blurdybloop.COM>\r\n";
        $message .= "Subject: afternoon meeting\r\n";
        $message .= "To: mooch@owatagu.siam.edu\r\n";
        $message .= "Message-Id: <B27397-0100000@Blurdybloop.COM>\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: TEXT/PLAIN; CHARSET=US-ASCII\r\n";
        $message .= "Hello Joe, do you think we can meet at 3:30 tomorrow?\r\n\r\n";

        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-01-01 1:00:00');
        $object = PimapCommandBuilder::instance()->append('IN')->withFlag('Seen')->withDateTime($date)->withMessage($message)->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('APPEND', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'MAILBOX' => 'IN',
            'FLAGS' => array('Seen'),
            'DATE' => 'Mon, 01 Jan 2018 01:00:00 +0000',
            'MESSAGE' => $message
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' APPEND (Seen) Mon, 01 Jan 2018 01:00:00 +0000 '.$message, self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testCheck()
    {
        $object = PimapCommandBuilder::instance()->check()->build();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('CHECK', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals(' CHECK', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testLogout()
    {
        $object = PimapCommandBuilder::instance()->logout()->build();

        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('LOGOUT', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(), self::$propertyArguments->getValue($object));
        $this->assertEquals(' LOGOUT', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testCopy()
    {
        $object = PimapCommandBuilder::instance()->copy()->withRange(1, 2)->toMailbox('IN')->build();

        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('COPY', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('MESSAGE' => '1:2', 'MAILBOX' => 'IN'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' COPY 1:2 IN', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));

        $object = PimapCommandBuilder::instance()->uid()->copy()->withRange(1, 2)->toMailbox('IN')->build();

        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('UID', self::$propertyCommand->getValue($object));
        $this->assertEquals(array(
            'COPY' => array('MESSAGE' => '1:2', 'MAILBOX' => 'IN')
        ), self::$propertyArguments->getValue($object));
        $this->assertEquals(' UID COPY 1:2 IN', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function withRange()
    {
        $object = PimapCommandBuilder::instance()->copy()->withRange(1, 2)->toMailbox('IN')->build();

        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('COPY', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('MESSAGE' => '1:2', 'MAILBOX' => 'IN'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' COPY', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function toMailBox()
    {
        $object = PimapCommandBuilder::instance()->copy()->withRange(1, 2)->toMailbox('IN')->build();

        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('COPY', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('MESSAGE' => '1:2', 'MAILBOX' => 'IN'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' COPY', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }


    public function testLogin()
    {
        $object = PimapCommandBuilder::instance()->login()->build();

        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('LOGIN', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('USER' => '', 'PASSWORD' => ''), self::$propertyArguments->getValue($object));
        $this->assertEquals(' LOGIN', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testUser()
    {
        $object = PimapCommandBuilder::instance()->login()->user('username')->build();

        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('LOGIN', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('USER' => 'username', 'PASSWORD' => ''), self::$propertyArguments->getValue($object));
        $this->assertEquals(' LOGIN username', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testPassword()
    {
        $object = PimapCommandBuilder::instance()->login()->user('username')->password('password')->build();

        $this->assertEquals('', self::$propertyCommandPrefix->getValue($object));
        $this->assertEquals('LOGIN', self::$propertyCommand->getValue($object));
        $this->assertEquals(array('USER' => 'username', 'PASSWORD' => 'password'), self::$propertyArguments->getValue($object));
        $this->assertEquals(' LOGIN username password', self::$propertyAsString->getValue($object));
        $this->assertEquals(false, self::$propertyIsValidated->getValue($object));
        $this->assertEquals(array(), self::$propertyValidators->getValue($object));
        $this->assertEquals(false, self::$propertyIsRaw->getValue($object));
    }

    public function testSearch()
    {
        $date = new DateTimeImmutable();
        $date = $date->setDate(2018, 8, 29);

        $messageList = new \SalesAgility\Imap\Response\MessageList();
        $message1 = new \SalesAgility\Imap\Response\Message();
        $message1->offsetSet('number', '1');
        $message1->offsetSet('uid', '1');
        $message3 = new \SalesAgility\Imap\Response\Message();
        $message3->offsetSet('number', '3');
        $message3->offsetSet('uid', '3');
        $messageList[] = $message1;
        $messageList[] = $message3;

        $searchCommand = PimapCommandBuilder::instance()
            ->search()
            ->withSequence($messageList)
            ->withRange(1,2)
            ->searchAll()
            ->searchAnswered()
            ->searchBcc('TestBcc')
            ->searchBefore($date)
            ->searchBody('TestBody')
            ->searchCc('TestCc')
            ->searchDeleted()
            ->searchDraft()
            ->searchFlagged()
            ->searchFrom('TestFrom')
            ->searchHeader('TestHeaderName', 'TestHeaderValue')
            ->searchKeyword('test')
            ->searchLarger(1000)
            ->searchNew()
            ->searchNot()
            ->searchOld()
            ->searchOn($date)
            ->searchOr()
            ->searchRecent()
            ->searchSeen()
            ->searchSentBefore($date)
            ->searchSentOn($date)
            ->searchSentSince($date)
            ->searchSince($date)
            ->searchSmaller(1000)
            ->searchSubject('TestSubject')
            ->searchText('TestText')
            ->searchTo('TestTo')
            ->searchUid('TestUid')
            ->searchUnanswered()
            ->searchUndeleted()
            ->searchUnflagged()
            ->searchUnkeyword('TestUnkeyword')
            ->searchUnseen()
            ->searchUndraft()
            ->build();


        $expectedArguments = array (
            '1 3',
            '1:2',
            'ALL',
            'ANSWERED',
            'BCC',
            '"TestBcc"',
            'BEFORE',
            '29-Aug-2018',
            'BODY',
            '"TestBody"',
            'CC',
            '"TestCc"',
            'DELETED',
            'DRAFT',
            'FLAGGED',
            'FROM',
            '"TestFrom"',
            'HEADER',
            'TestHeaderName:"TestHeaderValue"',
            'KEYWORD',
            '"test"',
            'LARGER',
            1000,
            'NEW',
            'NOT',
            'OLD',
            'ON',
            '29-Aug-2018',
            'OR',
            'RECENT',
            'SEEN',
            'SENTBEFORE',
            '29-Aug-2018',
            'SENTON',
            '29-Aug-2018',
            'SENTSINCE',
            '29-Aug-2018',
            'SINCE',
            '29-Aug-2018',
            'SMALLER',
            1000,
            'SUBJECT',
            '"TestSubject"',
            'TEXT',
            '"TestText"',
            'TEXT',
            '"TestTo"',
            'UID',
            'TestUid',
            'UNANSWERED',
            'UNDELETED',
            'UNFLAGGED',
            'UNKEYWORD',
            '"TestUnkeyword"',
            'UNSEEN',
            'UNDRAFT',

        );

        $actualArguments = $searchCommand->commandArguments();

        $expectedString = ' SEARCH 1 3 1:2 ALL ANSWERED BCC "TestBcc" BEFORE 29-Aug-2018 BODY "TestBody" CC "TestCc" DELETED DRAFT FLAGGED FROM "TestFrom" HEADER TestHeaderName:"TestHeaderValue" KEYWORD "test" LARGER 1000 NEW NOT OLD ON 29-Aug-2018 OR RECENT SEEN SENTBEFORE 29-Aug-2018 SENTON 29-Aug-2018 SENTSINCE 29-Aug-2018 SINCE 29-Aug-2018 SMALLER 1000 SUBJECT "TestSubject" TEXT "TestText" TEXT "TestTo" UID TestUid UNANSWERED UNDELETED UNFLAGGED UNKEYWORD "TestUnkeyword" UNSEEN UNDRAFT';
        $actualstring = $searchCommand->asString();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($searchCommand));
        $this->assertEquals($expectedString, $actualstring);
        $this->assertEquals($expectedArguments, $actualArguments);


        $searchUidCommand = PimapCommandBuilder::instance()
            ->uid()
            ->search()
            ->withSequence($messageList)
            ->withRange(1,2)
            ->searchAll()
            ->searchAnswered()
            ->searchBcc('TestBcc')
            ->searchBefore($date)
            ->searchBody('TestBody')
            ->searchCc('TestCc')
            ->searchDeleted()
            ->searchDraft()
            ->searchFlagged()
            ->searchFrom('TestFrom')
            ->searchHeader('TestHeaderName', 'TestHeaderValue')
            ->searchKeyword('test')
            ->searchLarger(1000)
            ->searchNew()
            ->searchNot()
            ->searchOld()
            ->searchOn($date)
            ->searchOr()
            ->searchRecent()
            ->searchSeen()
            ->searchSentBefore($date)
            ->searchSentOn($date)
            ->searchSentSince($date)
            ->searchSince($date)
            ->searchSmaller(1000)
            ->searchSubject('TestSubject')
            ->searchText('TestText')
            ->searchTo('TestTo')
            ->searchUid('TestUid')
            ->searchUnanswered()
            ->searchUndeleted()
            ->searchUnflagged()
            ->searchUnkeyword('TestUnkeyword')
            ->searchUnseen()
            ->searchUndraft()
            ->build();

        $expectedArguments = array (
           "SEARCH" => array(
               '1 3',
               '1:2',
               'ALL',
               'ANSWERED',
               'BCC',
               '"TestBcc"',
               'BEFORE',
               '29-Aug-2018',
               'BODY',
               '"TestBody"',
               'CC',
               '"TestCc"',
               'DELETED',
               'DRAFT',
               'FLAGGED',
               'FROM',
               '"TestFrom"',
               'HEADER',
               'TestHeaderName:"TestHeaderValue"',
               'KEYWORD',
               '"test"',
               'LARGER',
               1000,
               'NEW',
               'NOT',
               'OLD',
               'ON',
               '29-Aug-2018',
               'OR',
               'RECENT',
               'SEEN',
               'SENTBEFORE',
               '29-Aug-2018',
               'SENTON',
               '29-Aug-2018',
               'SENTSINCE',
               '29-Aug-2018',
               'SINCE',
               '29-Aug-2018',
               'SMALLER',
               1000,
               'SUBJECT',
               '"TestSubject"',
               'TEXT',
               '"TestText"',
               'TEXT',
               '"TestTo"',
               'UID',
               'TestUid',
               'UNANSWERED',
               'UNDELETED',
               'UNFLAGGED',
               'UNKEYWORD',
               '"TestUnkeyword"',
               'UNSEEN',
               'UNDRAFT',
           )
        );

        $actualArguments = $searchUidCommand->commandArguments();
        $expectedString = ' UID SEARCH 1 3 1:2 ALL ANSWERED BCC "TestBcc" BEFORE 29-Aug-2018 BODY "TestBody" CC "TestCc" DELETED DRAFT FLAGGED FROM "TestFrom" HEADER TestHeaderName:"TestHeaderValue" KEYWORD "test" LARGER 1000 NEW NOT OLD ON 29-Aug-2018 OR RECENT SEEN SENTBEFORE 29-Aug-2018 SENTON 29-Aug-2018 SENTSINCE 29-Aug-2018 SINCE 29-Aug-2018 SMALLER 1000 SUBJECT "TestSubject" TEXT "TestText" TEXT "TestTo" UID TestUid UNANSWERED UNDELETED UNFLAGGED UNKEYWORD "TestUnkeyword" UNSEEN UNDRAFT';
        $actualstring = $searchUidCommand->asString();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($searchUidCommand));
        $this->assertEquals($expectedString, $actualstring);
        $this->assertEquals($expectedArguments, $actualArguments);
    }

    public function testStatus()
    {
        $statusCommand = PimapCommandBuilder::instance()
            ->status('IN')
            ->withRecent()
            ->withUidNext()
            ->withUidValidity()
            ->withUnseen()
            ->withMessages()
            ->build();

        $expectedArguments = array(
            'MAILBOX' => 'IN',
            'INCLUDE' => Array (
                "RECENT" ,
                "UIDNEXT" ,
                "UIDVALIDITY" ,
                "UNSEEN" ,
                "MESSAGES"
            )
        );
        $actualArguments = $statusCommand->commandArguments();
        $expectedString = ' STATUS IN (RECENT UIDNEXT UIDVALIDITY UNSEEN MESSAGES)';
        $actualstring = $statusCommand->asString();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($statusCommand));
        $this->assertEquals($expectedString, $actualstring);
        $this->assertEquals($expectedArguments, $actualArguments);
    }

    public function testStore()
    {
        $statusCommand = PimapCommandBuilder::instance()
            ->store()
            ->withRange(1,2)
            ->addFlag('Answered')
            ->removeFlag('Seen')
            ->replaceFlag('Custom')
            ->build();

        $expectedArguments = array (
            'MESSAGE' => '1:2',
            '+FLAGS' =>
                array (
                    0 => '\Answered',
                ),
            '-FLAGS' =>
                array (
                    0 => '\Seen',
                ),
            'FLAGS' =>
                array (
                    0 => '\Custom',
                ),
        );

        $actualArguments = $statusCommand->commandArguments();
        $expectedString = ' STORE 1:2 +FLAGS.SILENT (\Answered) -FLAGS.SILENT (\Seen) FLAGS.SILENT (\Custom)';
        $actualstring = $statusCommand->asString();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($statusCommand));
        $this->assertEquals($expectedString, $actualstring);
        $this->assertEquals($expectedArguments, $actualArguments);

        $statusUidCommand = PimapCommandBuilder::instance()
            ->uid()
            ->store()
            ->withRange(1,2)
            ->addFlag('Answered')
            ->removeFlag('Seen')
            ->replaceFlag('Custom')
            ->build();

        $expectedArguments = array (
           'STORE' => array(
               'MESSAGE' => '1:2',
               '+FLAGS' =>
                   array (
                       0 => '\Answered',
                   ),
               '-FLAGS' =>
                   array (
                       0 => '\Seen',
                   ),
               'FLAGS' =>
                   array (
                       0 => '\Custom',
                   ),
           )
        );
        $actualArguments = $statusUidCommand->commandArguments();
        $expectedString = ' UID STORE 1:2 +FLAGS.SILENT (\Answered) -FLAGS.SILENT (\Seen) FLAGS.SILENT (\Custom)';
        $actualstring = $statusUidCommand->asString();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($statusUidCommand));
        $this->assertEquals($expectedString, $actualstring);
        $this->assertEquals($expectedArguments, $actualArguments);
    }

    public function testIdle()
    {
        $idleCommand = PimapCommandBuilder::instance()
            ->idle()
            ->build();
        $actualArguments = $idleCommand->commandArguments();
        $expectedString = ' IDLE';
        $actualstring = $idleCommand->asString();
        $this->assertEquals('', self::$propertyCommandPrefix->getValue($idleCommand));
        $this->assertEquals($expectedString, $actualstring);
        $this->assertEquals(array(), $actualArguments);
    }
}
