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

use SalesAgility\Imap\ImapException;
use SalesAgility\Imap\CommandBuilder\PimapCommandBuilder;
use SalesAgility\Imap\Stream\MessageTransporter;
use SalesAgility\Stream\MockConnection as Connection;
use SalesAgility\Imap\Pipeline\Pipeline;
use SalesAgility\Imap\ManagerFactory;

class MessageTransporterTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testSetConnection()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
        $pipeline = new Pipeline(ManagerFactory::instance());
        $object->setPipeLine($pipeline);
        $reflection = new \ReflectionClass(MessageTransporter::class);
        $propertyConnection = $reflection->getProperty('connection');
        $propertyConnection->setAccessible(true);

        $connection = new Connection();
        $object->setConnection($connection);
        $this->assertEquals($connection, $propertyConnection->getValue($object));
    }

    public function testConnection()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
        $pipeline = new Pipeline(ManagerFactory::instance());
        $object->setPipeLine($pipeline);
        $connection = new Connection();
        $object->setConnection($connection);
        $this->assertEquals($connection, $object->connection());
    }

    public function testTransmit()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
        $pipeline = new Pipeline(ManagerFactory::instance());
        $object->setPipeLine($pipeline);
        $connection = new Connection();
        $reflection = new \ReflectionClass(Connection::class);
        $property = $reflection->getProperty('messageSent');
        $property->setAccessible(true);
        $object->setConnection($connection);
        $object->transmit('hello connection');
        $this->assertEquals('hello connection', $property->getValue($connection));
    }

    public function testReceive()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
        $pipeline = new Pipeline(ManagerFactory::instance());
        $object->setPipeLine($pipeline);
        $connection = new Connection();

        $object->setConnection($connection);
        $this->assertEquals('* OK mock server v1.0some headersome body',  $object->receive());
    }

    public function testIsEndOfFile()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
        $pipeline = new Pipeline(ManagerFactory::instance());
        $pipeline->add(PimapCommandBuilder::instance()->raw('test'));
        $object->setPipeLine($pipeline);
        $connection = new Connection();
        $object->setConnection($connection);
        $object->receive();
        $this->assertTrue($object->isEndOfFile(null));
        $this->assertTrue($object->isEndOfFile('A1 OK'));
        $this->tester->expectException(
            ImapException::BadResponse('A1 BAD'),
            function () use ($object, $pipeline, $connection) {
                $object->isEndOfFile('A1 BAD');
            }
        );

        $this->tester->expectException(
            ImapException::NoResponse('A1 NO'),
            function () use ($object, $pipeline, $connection) {
                $object->isEndOfFile('A1 NO');
            }
        );
    }

    public function testIsConfigured()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
        $pipeline = new Pipeline(ManagerFactory::instance());
        $object->setPipeLine($pipeline);
        $reflection = new \ReflectionClass(MessageTransporter::class);
        $method = $reflection->getMethod('isConfigured');
        $method->setAccessible(true);

        $this->tester->expectException(
            new \Exception('Connection must be set'),
            function () use ($object, $method) {
                $method->invoke($object);
            }
        );

        $connection = new Connection();
        $object->setConnection($connection);
        $this->assertTrue($method->invoke($object));
    }

    public function testTransmitCommand()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
        $pipeline = new Pipeline(ManagerFactory::instance());
        $object->setPipeLine($pipeline);
        $connection = new Connection();
        $object->setConnection($connection);
        $command = PimapCommandBuilder::instance()->raw('test');
        $object->transmitCommand($command);
        $this->assertEquals('A1 test'."\x0D\x0A", $connection->messageSent);
    }
}
