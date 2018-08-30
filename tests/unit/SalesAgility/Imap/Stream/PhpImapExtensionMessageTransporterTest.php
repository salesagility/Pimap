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

require_once codecept_root_dir() . 'tests/mock/imap-ext.php';

use SalesAgility\Imap\Stream\PhpImapExtensionMessageTransporter;
use SalesAgility\Imap\Stream\PhpImpExtensionConnection;
use SalesAgility\Imap\CommandBuilder\PhpImapExtensionCommandBuilder;
use SalesAgility\Imap\Response\Response;
use SalesAgility\Imap\ImapException;
use \SalesAgility\Imap\ManagerFactory;
use SalesAgility\Iteration\StringIterator;

class PhpImapExtensionMessageTransporterTest extends \Codeception\Test\Unit
{

    /** @var \UnitTester $tester */
    protected $tester;

    /** @var \SalesAgility\Imap\Manager\PhpImapExtensionManager */
    private static $manager;
    /** @var PhpImapExtensionMessageTransporter */
    private static $transporter;
    /** @var \ReflectionClass */
    private static $reflected;

    public function _before()
    {
        parent::_before();
        if (self::$reflected === null) {
            self::$reflected = new ReflectionClass(PhpImapExtensionMessageTransporter::class);
        }

        self::$manager = ManagerFactory::instance()->PhpImapExtensionManager();
        self::$transporter = self::$manager->transporter();

    }

    public function testReceive()
    {
        $this->tester->expectException(
            new \Exception('Native Php Client handles it\'s own transmission'),
            function () {
                self::$transporter->receive();
            }
        );
    }

    public function testIsEndOfFile()
    {
        $this->tester->expectException(
            new \Exception('Native Php Client handles it\'s own transmission'),
            function () {
                self::$transporter->isEndOfFile('');
            }
        );
    }

    public function testTransmit()
    {
        $this->tester->expectException(
            new \Exception('Native Php Client handles it\'s own transmission'),
            function () {
                self::$transporter->transmit('');
            }
        );
    }

    public function testSetConnection()
    {
        $reflection = new \ReflectionClass(PhpImapExtensionMessageTransporter::class);
        $connectionProperty = $reflection->getProperty('connection');
        $connectionProperty->setAccessible(true);
        $this->assertEquals(self::$transporter->connection(), $connectionProperty->getValue(self::$transporter));

        $this->tester->expectException(
            new \Exception('Native client is only compatible with PhpImpExtensionConnection'),
            function () {
                $connection = new \SalesAgility\Stream\Connection(ManagerFactory::instance());
                self::$transporter->setConnection($connection);
            }
        );
    }


    public function testTransmitCommand()
    {
        $this->tester->expectException(
            new \Exception('Command Not Supported: IDLE'),
            function () {
                $command = \SalesAgility\Imap\CommandBuilder\PimapCommandBuilder::instance()->idle()->build();
                self::$transporter->transmitCommand($command);
            }
        );
    }

    public function testPipline()
    {
        $actual = self::$manager->transporter()->pipeline();
        $this->assertInstanceOf(SalesAgility\Imap\Pipeline\Pipeline::class, $actual);
    }

    public function testLogin()
    {
        $command = PhpImapExtensionCommandBuilder::instance()->login()->user('user')->password('')->build();
        $actual = self::$transporter->transmitCommand($command);
        $expectedIncluded = \SalesAgility\Iteration\StringIterator::withLiteral('', 0, 0);
        $expectedResponseMessage = \SalesAgility\Iteration\StringIterator::withLiteral('A1 OK login success.' . "\r\n");
        $expected = new Response('OK', $expectedResponseMessage, $expectedIncluded);
        $this->assertEquals($expected->included(), $actual->included());
        $this->assertEquals($expected->message(), $actual->message());
        $this->assertEquals($expected->status(), $actual->status());
    }

    public function testSelect()
    {
        $exists = 5;
        $recent = 1;
        $GLOBALS['mock_imap_num_msg'] = $exists;
        $GLOBALS['mock_imap_num_recent'] = $recent;

        $command = PhpImapExtensionCommandBuilder::instance()->select('INBOX')->build();
        $actual = self::$transporter->transmitCommand($command);
        $expected = new \SalesAgility\Imap\Response\Mailbox();
        $expected->offsetSet('exists', (string)$exists);
        $expected->offsetSet('recent', (string)$recent);
        $this->assertEquals($expected, $actual);
    }

    public function testNoop()
    {
        $command = PhpImapExtensionCommandBuilder::instance()->noop()->build();
        $actual = self::$transporter->transmitCommand($command);
        $expected = new Response('OK', StringIterator::withLiteral('A1 OK Nothing Happened.' . "\r\n"), StringIterator::withLiteral('', 0, 0));
        $this->assertEquals($expected->included(), $actual->included());
        $this->assertEquals($expected->message(), $actual->message());
        $this->assertEquals($expected->status(), $actual->status());
    }

    public function testLogout()
    {
        $command = PhpImapExtensionCommandBuilder::instance()->logout()->build();
        $actual = self::$transporter->transmitCommand($command);
        $expected = new Response('OK', StringIterator::withLiteral('A1 OK Logout completed.' . "\r\n"), StringIterator::withLiteral('* BYE Logging out' . "\r\n"));
        $this->assertEquals($expected->included(), $actual->included());
        $this->assertEquals($expected->message(), $actual->message());
        $this->assertEquals($expected->status(), $actual->status());
    }

    public function testFetch()
    {

        $mock_imap_fetch_overview = array();
        $mock_imap_fetch_overview[] = unserialize(file_get_contents(codecept_data_dir('1534938082_php_imap_extension_imap_fetch_overview.serialise')))['imap_fetch_overview'];
        $mock_imap_fetch_overview[] = unserialize(file_get_contents(codecept_data_dir('1534938086_php_imap_extension_imap_fetch_overview.serialise')))['imap_fetch_overview'];
        $mock_imap_fetch_overview[] = unserialize(file_get_contents(codecept_data_dir('1534938092_php_imap_extension_imap_fetch_overview.serialise')))['imap_fetch_overview'];
        $mock_imap_fetch_overview[] = unserialize(file_get_contents(codecept_data_dir('1534938096_php_imap_extension_imap_fetch_overview.serialise')))['imap_fetch_overview'];

        $mock_imap_fetchstructure = array();
        $mock_imap_fetchstructure[] = unserialize(file_get_contents(codecept_data_dir('1534938083_php_imap_extension_imap_fetchstructure.serialise')))['imap_fetchstructure'];
        $mock_imap_fetchstructure[] = unserialize(file_get_contents(codecept_data_dir('1534938087_php_imap_extension_imap_fetchstructure.serialise')))['imap_fetchstructure'];
        $mock_imap_fetchstructure[] = unserialize(file_get_contents(codecept_data_dir('1534938093_php_imap_extension_imap_fetchstructure.serialise')))['imap_fetchstructure'];
        $mock_imap_fetchstructure[] = unserialize(file_get_contents(codecept_data_dir('1534938097_php_imap_extension_imap_fetchstructure.serialise')))['imap_fetchstructure'];

        $mock_imap_body = array();
        $mock_imap_body[] = unserialize(file_get_contents(codecept_data_dir('1534938088_php_imap_extension_imap_body.serialise')))['imap_body'];

        $mock_imap_fetchbody = array();
        $mock_imap_fetchbody[] = unserialize(file_get_contents(codecept_data_dir('1534938098_php_imap_extension_imap_fetchbody.serialise')))['imap_fetchbody'];
        $mock_imap_fetchbody[] = unserialize(file_get_contents(codecept_data_dir('1534938099_php_imap_extension_imap_fetchbody.serialise')))['imap_fetchbody'];
        $mock_imap_fetchbody[] = unserialize(file_get_contents(codecept_data_dir('1534938100_php_imap_extension_imap_fetchbody.serialise')))['imap_fetchbody'];
        $mock_imap_fetchbody[] = unserialize(file_get_contents(codecept_data_dir('1534938101_php_imap_extension_imap_fetchbody.serialise')))['imap_fetchbody'];

        // mock methods uses pop so we need to reverse the order
        // ensure that the results are correctly return at the right time.
        $mock_imap_fetch_overview = array_reverse($mock_imap_fetch_overview);
        $mock_imap_fetchstructure = array_reverse($mock_imap_fetchstructure);
        $mock_imap_fetchbody = array_reverse($mock_imap_fetchbody);
        $mock_imap_body = array_reverse($mock_imap_body);

        $GLOBALS['mock_imap_fetch_overview'] = $mock_imap_fetch_overview;
        $GLOBALS['mock_imap_fetchstructure'] = $mock_imap_fetchstructure;
        $GLOBALS['mock_imap_fetchbody'] = $mock_imap_fetchbody;
        $GLOBALS['mock_imap_body'] = $mock_imap_body;

        // Test Plain Text Email
        $command = self::$manager->command()->fetch(2677)->flags()->uids()->header()->build();
        /** @var \SalesAgility\Imap\Response\MessageList $actual */
        $actual = self::$transporter->transmitCommand($command);
        $expected = unserialize(file_get_contents(codecept_data_dir('1534938084_php_imap_extension_message.serialise')));
        $this->assertEquals($expected, $actual->offsetGet(0));

        $command = self::$manager->command()->fetch(2677)->body()->build();
        $actual = self::$transporter->transmitCommand($command);
        $expected = unserialize(file_get_contents(codecept_data_dir('1534938089_php_imap_extension_message.serialise')));
        $this->assertEquals($expected, $actual->offsetGet(0));

        // Test Html Email
        $command = self::$manager->command()->fetch(2668)->flags()->uids()->header()->build();
        $actual = self::$transporter->transmitCommand($command);
        $expected = unserialize(file_get_contents(codecept_data_dir('1534938094_php_imap_extension_message.serialise')));
        $this->assertEquals($expected, $actual->offsetGet(0));

        $command = self::$manager->command()->uid()->fetch(2668)->body()->build();
        $actual = self::$transporter->transmitCommand($command);
        $expected = unserialize(file_get_contents(codecept_data_dir('1534938102_php_imap_extension_message.serialise')));
        $this->assertEquals($expected, $actual->offsetGet(0));

    }


    public function testSearch()
    {
        $messageList = new \SalesAgility\Imap\Response\MessageList();
        $message = new \SalesAgility\Imap\Response\Message();;
        $message->offsetSet('number', '1');
        $messageList[] = $message;
        $GLOBALS['mock_imap_search'] = $messageList;

        $command = self::$manager->command()->search()->searchRecent()->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\MessageList::class, $actual);

        $command = self::$manager->command()->uid()->search()->searchRecent()->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\MessageList::class, $actual);
    }

    public function testStore()
    {
        $command = self::$manager->command()->store()->withMessage('1')->replaceFlag('Answered')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(Response::class, $actual);

        $command = self::$manager->command()->uid()->store()->withMessage('1')->replaceFlag('Answered')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(Response::class, $actual);

        $command = self::$manager->command()->uid()->store()->withMessage('1')->addFlag('Answered')->addFlag('Seen')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(Response::class, $actual);


        $command = self::$manager->command()->store()->withMessage('1')->removeFlag('Answered')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(Response::class, $actual);

        $command = self::$manager->command()->uid()->store()->withMessage('1')->removeFlag('Answered')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(Response::class, $actual);
    }

    public function testCopy()
    {
        $command = self::$manager->command()->copy()->withRange(1, 2)->toMailbox('Invoices')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);

        $command = self::$manager->command()->uid()->copy()->withRange(1, 2)->toMailbox('Invoices')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testUid()
    {
        // test invalid case
        $this->tester->expectException(
            new \Exception('UID Command Not Supported: UID'),
            function () {
                $command = self::$manager->command()->uid()->build();
                self::$transporter->transmitCommand($command);
            }
        );
    }

    public function testListMailboxes()
    {
        $mailboxList = new \SalesAgility\Imap\Response\MailboxList();
        $mailbox1 = new \SalesAgility\Imap\Response\Mailbox();
        $mailbox1->offsetSet('name', 'INBOX');
        $mailbox2 = new \SalesAgility\Imap\Response\Mailbox();
        $mailbox2->offsetSet('name', 'Trash');
        $mailbox3 = new \SalesAgility\Imap\Response\Mailbox();
        $mailbox3->offsetSet('name', 'Sent');
        $mailboxList[] = $mailbox1;
        $mailboxList[] = $mailbox2;
        $mailboxList[] = $mailbox3;

        $GLOBALS['mock_imap_list'] = array('INBOX', 'Trash', 'Sent');

        $command = self::$manager->command()->listMailbox()->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\MailboxList::class, $actual);
        $this->assertEquals($mailboxList, $actual);
    }

    public function testListSubMailboxes()
    {
        $mailboxList = new \SalesAgility\Imap\Response\MailboxList();
        $mailbox1 = new \SalesAgility\Imap\Response\Mailbox();
        $mailbox1->offsetSet('hierarchy', '/Customer');
        $mailbox1->offsetSet('name', '/B2C');
        $mailbox2 = new \SalesAgility\Imap\Response\Mailbox();
        $mailbox2->offsetSet('hierarchy', '/Customer');
        $mailbox2->offsetSet('name', '/B2B');

        $mailboxList[] = $mailbox1;
        $mailboxList[] = $mailbox2;

        $GLOBALS['mock_imap_lsub'] = array('/Customer/B2C', '/Customer/B2B');

        $command = self::$manager->command()->listSubsetMailbox('/Customer')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\MailboxList::class, $actual);
        $this->assertEquals($mailboxList, $actual);
    }

    public function testCheck()
    {
        $command = self::$manager->command()->check()->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testCreate()
    {
        $command = self::$manager->command()->create('INVOICES')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testDelete()
    {
        $command = self::$manager->command()->delete('INVOICES')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testRename()
    {
        $command = self::$manager->command()->rename('QUOTES', 'INVOICES')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testSubscribe()
    {
        $command = self::$manager->command()->subscribe('INVOICES')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testUnsubscribe()
    {
        $command = self::$manager->command()->unsubscribe('INVOICES')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testClose()
    {
        $command = self::$manager->command()->close()->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testExpunge()
    {
        $command = self::$manager->command()->expunge()->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testAppend()
    {
        $command = self::$manager->command()->append('INVOICES')->withMessage('FAKE RFC 2822 MESSAGE')->build();
        $actual = self::$transporter->transmitCommand($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testCheckErrors()
    {
        $GLOBALS['mock_imap_last_error'] = 'test imap_last_error';
        $this->tester->expectException(
          new \Exception('test imap_last_error'),
          function () {
              $command = self::$manager->command()->append('INVOICES')->withMessage('FAKE RFC 2822 MESSAGE')->build();
              $actual = self::$transporter->transmitCommand($command);
          }
        );

    }
}
