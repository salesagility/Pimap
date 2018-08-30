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

use SalesAgility\Imap\Manager\PimapManager;
use \SalesAgility\Imap\ManagerFactory;

class PhpImapManagerTest extends \Codeception\Test\Unit
{

    public function test__construct()
    {
        $factory = \SalesAgility\Imap\ManagerFactory::instance();
        $object = new PimapManager($factory);
        $this->assertInstanceOf(\SalesAgility\Imap\Manager\ManagerInterface::class, $object);
        $this->assertInstanceOf(\SalesAgility\Pattern\ContainerAwareInterface::class, $object);
        $this->assertInstanceOf(\SalesAgility\Imap\Pipeline\PipeLineAwareInterface::class, $object);
        $this->assertInstanceOf(\Psr\Log\LoggerAwareInterface::class, $object);
    }

    public function testSetTransporter()
    {
        $transporter = new \SalesAgility\Imap\Stream\PhpImapExtensionMessageTransporter(ManagerFactory::instance());
        $object = ManagerFactory::instance()->PimapManager();
        $object->setTransporter($transporter);
        $reflection = new ReflectionClass(PimapManager::class);
        $property = $reflection->getProperty('transporter');
        $property->setAccessible(true);
        $this->assertEquals($transporter, $property->getValue($object));
    }

    public function testTransporter()
    {
        $transporter = new \SalesAgility\Imap\Stream\PhpImapExtensionMessageTransporter(ManagerFactory::instance());
        $object = ManagerFactory::instance()->PimapManager();
        $object->setTransporter($transporter);
        $this->assertEquals($transporter, $object->transporter());
    }

    public function testCommand()
    {
        $object = ManagerFactory::instance()->PimapManager();
        $this->assertInstanceOf(\SalesAgility\Imap\CommandBuilder\PimapSupportedCommandsInterface::class, $object->command());
    }

    public function testRun()
    {
        $connection = new \SalesAgility\Stream\MockConnection();
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $property = $reflection->getProperty('messageReceived');
        $property->setAccessible(true);
        $property->setValue(
            $connection,
            array('A1 OK nothing happened'."\x0D\x0A")
        );

        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);
        /** @var \SalesAgility\Imap\Response\Response $actual */
        $actual = $object->run($object->command()->logout());
        $this->assertEquals('OK', $actual->status());
        $actualMessage = $actual->message()->toString();
        $this->assertEquals('A1 OK nothing happened', $actualMessage);
        $this->assertEmpty($actual->included()->toString());
    }

    public function testSetLogger()
    {
        $log = new \SalesAgility\Utility\PimapLogger();
        $manager = ManagerFactory::instance()->PimapManager();
        $manager->setLogger($log);
        $reflection = new \ReflectionClass(PimapManager::class);
        $property = $reflection->getProperty('log');
        $property->setAccessible(true);
        $this->assertEquals($log, $property->getValue($manager));
    }

    public function testSetPipeLine()
    {
        $pipeline = new \SalesAgility\Imap\Pipeline\Pipeline(ManagerFactory::instance());
        $manager = ManagerFactory::instance()->PimapManager();
        $manager->setPipeLine($pipeline);
        $reflection = new \ReflectionClass(PimapManager::class);
        $property = $reflection->getProperty('pipeline');
        $property->setAccessible(true);
        $this->assertEquals($pipeline, $property->getValue($manager));
    }

    public function testPipeline()
    {
        $pipeline = new \SalesAgility\Imap\Pipeline\Pipeline(ManagerFactory::instance());
        $manager = ManagerFactory::instance()->PimapManager();
        $manager->setPipeLine($pipeline);
        $this->assertEquals($pipeline, $manager->pipeline());
    }

    public function testIdle()
    {
        $connection = new \SalesAgility\Stream\MockConnection();
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $property = $reflection->getProperty('messageReceived');
        $property->setAccessible(true);
        $property->setValue(
            $connection,
            array(
                '+ idling'."\x0D\x0A",
                '* 8 EXISTS'."\x0D\x0A",
                'A1 OK '."\x0D\x0A",
            )
        );
        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);
        $idle = $object->command()->idle();
        $response = $object->run($idle);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Mailbox::class, $response);
    }

    public function testSearch()
    {
        $connection = new \SalesAgility\Stream\MockConnection();
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $messagePosition = $reflection->getProperty('messagePosition');
        $messagePosition->setAccessible(true);

        $messageReceived = $reflection->getProperty('messageReceived');
        $messageReceived->setAccessible(true);
        $searchResultFile = file_get_contents(codecept_data_dir('SEARCH_1_5.txt'));
        $searchResultArray = explode("\r\n", $searchResultFile);
        // remove last empty array element
        array_pop($searchResultArray);
        foreach ($searchResultArray as $item => $searchResult) {
            $searchResultArray[$item] .= "\r\n";
        }

        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            $searchResultArray
        );

        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);

        $searchCommand = $object->command()->search()->withRange(1, 5)->build();
        $actual = $object->run($searchCommand);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\MessageList::class, $actual);
        $this->assertCount(5, $actual);

        // Since the file only as A1
        // we need to reset these objects
        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);
        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            $searchResultArray
        );

        $searchCommand2 = $object->command()->uid()->search()->withRange(1, 5)->build();
        $actual = $object->run($searchCommand2);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\MessageList::class, $actual);
        $this->assertCount(5, $actual);
    }

    public function testFetch()
    {
        $connection = new \SalesAgility\Stream\MockConnection();
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $messagePosition = $reflection->getProperty('messagePosition');
        $messagePosition->setAccessible(true);

        $messageReceived = $reflection->getProperty('messageReceived');
        $messageReceived->setAccessible(true);
        $fetchResultFile = file_get_contents(codecept_data_dir('FETCH_BODY_HEADER_WITH_RESPONSE.txt'));
        $fetchResultArray = explode("\r\n", $fetchResultFile);
        // remove last empty array element
        array_pop($fetchResultArray);
        foreach ($fetchResultArray as $item => $searchResult) {
            $fetchResultArray[$item] .= "\r\n";
        }

        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            $fetchResultArray
        );

        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);

        $fetchCommand = $object->command()->fetch(1)->header()->build();
        $actual = $object->run($fetchCommand);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\MessageList::class, $actual);
        $this->assertCount(1, $actual);

        // Since the file only as A1
        // we need to reset these objects
        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);
        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            $fetchResultArray
        );

        $fetchCommand2 = $object->command()->uid()->fetch(1)->header()->build();
        $actual = $object->run($fetchCommand2);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\MessageList::class, $actual);
        $this->assertCount(1, $actual);
    }

    public function testCopy()
    {
        $connection = new \SalesAgility\Stream\MockConnection();
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $messagePosition = $reflection->getProperty('messagePosition');
        $messagePosition->setAccessible(true);

        $messageReceived = $reflection->getProperty('messageReceived');
        $messageReceived->setAccessible(true);

        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            array('A1 OK' . "\r\n")
        );

        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);

        $command = $object->command()->copy()->withRange(1, 5)->toMailbox('INVOICES')->build();
        $actual = $object->run($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);

        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            array('A2 OK' . "\r\n")
        );

        $command2 = $object->command()->uid()->copy()->withRange(1, 5)->toMailbox('INVOICES')->build();
        $actual = $object->run($command2);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Response::class, $actual);
    }

    public function testStatus()
    {
        $connection = new \SalesAgility\Stream\MockConnection();
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $messagePosition = $reflection->getProperty('messagePosition');
        $messagePosition->setAccessible(true);

        $messageReceived = $reflection->getProperty('messageReceived');
        $messageReceived->setAccessible(true);

        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            array('A1 OK' . "\r\n")
        );

        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);

        $command = $object->command()->status('INVOICES')->withRecent()->build();
        $actual = $object->run($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Mailbox::class, $actual);
    }

    public function testSelect()
    {
        $connection = new \SalesAgility\Stream\MockConnection();
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $messagePosition = $reflection->getProperty('messagePosition');
        $messagePosition->setAccessible(true);

        $messageReceived = $reflection->getProperty('messageReceived');
        $messageReceived->setAccessible(true);

        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            array('A1 OK' . "\r\n")
        );

        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);

        $command = $object->command()->select('INVOICES')->build();
        $actual = $object->run($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Mailbox::class, $actual);
    }

    public function testExamine()
    {
        $connection = new \SalesAgility\Stream\MockConnection();
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $messagePosition = $reflection->getProperty('messagePosition');
        $messagePosition->setAccessible(true);

        $messageReceived = $reflection->getProperty('messageReceived');
        $messageReceived->setAccessible(true);

        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            array('A1 OK' . "\r\n")
        );

        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);

        $command = $object->command()->examine('INVOICES')->build();
        $actual = $object->run($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\Mailbox::class, $actual);
    }

    public function testList()
    {
        $connection = new \SalesAgility\Stream\MockConnection();
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $messagePosition = $reflection->getProperty('messagePosition');
        $messagePosition->setAccessible(true);

        $messageReceived = $reflection->getProperty('messageReceived');
        $messageReceived->setAccessible(true);

        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            array('A1 OK' . "\r\n")
        );

        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);

        $command = $object->command()->listMailbox()->build();
        $actual = $object->run($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\MailboxList::class, $actual);
    }

    public function testSubList()
    {
        $connection = new \SalesAgility\Stream\MockConnection();
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $messagePosition = $reflection->getProperty('messagePosition');
        $messagePosition->setAccessible(true);

        $messageReceived = $reflection->getProperty('messageReceived');
        $messageReceived->setAccessible(true);

        $messagePosition->setValue($connection, 0);
        $messageReceived->setValue(
            $connection,
            array('A1 OK' . "\r\n")
        );

        $object = ManagerFactory::instance()->PimapManager();
        $object->transporter()->setConnection($connection);

        $command = $object->command()->listSubsetMailbox()->build();
        $actual = $object->run($command);
        $this->assertInstanceOf(\SalesAgility\Imap\Response\MailboxList::class, $actual);
    }
}
