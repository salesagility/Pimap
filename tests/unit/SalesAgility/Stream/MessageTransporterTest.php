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

use SalesAgility\Stream\MessageTransporter;
use SalesAgility\Stream\MockConnection as Connection;
use SalesAgility\Stream\ConnectionException;
use SalesAgility\Imap\ManagerFactory;

class MessageTransporterTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testSetConnection()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
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
        $connection = new Connection();
        $object->setConnection($connection);
        $this->assertEquals($connection, $object->connection());
    }

    public function testTransmit()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
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
        $connection = new Connection();
//        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
//        $property = $reflection->getProperty('messageReceived');
//        $property->setAccessible(true);
//        $property->setValue(
//            $connection,
//            array('A1 OK nothing happened'."\x0A\x0D")
//        );

        $object->setConnection($connection);
        $this->assertEquals('* OK mock server v1.0some headersome body',  $object->receive());

        // negative tests
        $reflection = new \ReflectionClass(MessageTransporter::class);
        $property = $reflection->getProperty('TTL');
        $property->setAccessible(true);
        $property->setValue($object, 1);
    }

    public function testIsEndOfFile()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
        $connection = new Connection();
        $object->setConnection($connection);
        $object->receive();
        $this->assertTrue($object->isEndOfFile(''));
    }

    public function testIsConfigured()
    {
        $object = new MessageTransporter(ManagerFactory::instance());
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


}
