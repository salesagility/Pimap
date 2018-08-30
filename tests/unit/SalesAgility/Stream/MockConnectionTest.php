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

use SalesAgility\Stream\MockConnection as Connection;

class MockConnectionTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testSetConnectionString()
    {
        $class = new Connection();
        $reflection = new \ReflectionClass(Connection::class);
        $propertyResource = $reflection->getProperty('resource');
        $propertyResource->setAccessible(true);
        $propertyConnectionString = $reflection->getProperty('connectionString');
        $propertyConnectionString->setAccessible(true);
        $propertySecurity = $reflection->getProperty('security');
        $propertySecurity->setAccessible(true);

        $expected = 'tcp://google.com:80';
        $class->setConnectionString($expected);
        $actual = $propertyConnectionString->getValue($class);
        $this->assertEquals($expected, $actual);

        // Test negative cases
        $this->tester->expectException(
            new \Exception('connection string must be a string'),
            function () use ($class) {
                $class->setConnectionString(1);
            }
        );

        $this->tester->expectException(
            new \Exception('connection string must not be empty'),
            function () use ($class) {
                $class->setConnectionString('');
            }
        );
    }

    public function testEnableEncryption()
    {
        $class = new Connection();
        $reflection = new \ReflectionClass(Connection::class);
        $propertyResource = $reflection->getProperty('resource');
        $propertyResource->setAccessible(true);
        $propertyConnectionString = $reflection->getProperty('connectionString');
        $propertyConnectionString->setAccessible(true);
        $propertySecurity = $reflection->getProperty('security');
        $propertySecurity->setAccessible(true);

        $expected = Connection::TLS;
        $class->enableEncryption($expected);
        $actual = $propertySecurity->getValue($class);
        $this->assertEquals($expected, $actual);
    }

    public function testDisableEncryption()
    {
        $class = new Connection();
        $reflection = new \ReflectionClass(Connection::class);
        $propertyResource = $reflection->getProperty('resource');
        $propertyResource->setAccessible(true);
        $propertyConnectionString = $reflection->getProperty('connectionString');
        $propertyConnectionString->setAccessible(true);
        $propertySecurity = $reflection->getProperty('security');
        $propertySecurity->setAccessible(true);

        $expected = false;
        $class->disableEncryption();
        $actual = $propertySecurity->getValue($class);
        $this->assertEquals($expected, $actual, 'Failed To Disable Encryption');
    }

    public function testIsConnected()
    {
        $class = new Connection();
        $reflection = new \ReflectionClass(Connection::class);
        $propertyResource = $reflection->getProperty('resource');
        $propertyResource->setAccessible(true);
        $propertyConnectionString = $reflection->getProperty('connectionString');
        $propertyConnectionString->setAccessible(true);
        $propertySecurity = $reflection->getProperty('security');
        $propertySecurity->setAccessible(true);

        $expected = false;
        $class->isConnected();
        $actual = $class->isConnected();
        $this->assertEquals($expected, $actual);

        $class->enableEncryption(Connection::TLS);
        $class->setConnectionString('tcp://google.com:443');
        $class->connect();

        $expected = true;
        $class->isConnected();
        $actual = $class->isConnected();
        $this->assertEquals($expected, $actual);
    }


    public function testConnect()
    {
        $log = new \SalesAgility\Utility\PimapLogger();
        $class = new Connection();
        $reflection = new \ReflectionClass(Connection::class);
        $propertyResource = $reflection->getProperty('resource');
        $propertyResource->setAccessible(true);
        $propertyConnectionString = $reflection->getProperty('connectionString');
        $propertyConnectionString->setAccessible(true);
        $propertySecurity = $reflection->getProperty('security');
        $propertySecurity->setAccessible(true);

        $class->enableEncryption(Connection::TLS);
        $class->setConnectionString('tcp://google.com:443');
        $class->setLogger($log);
        $class->connect();
        $expected = true;
        $class->isConnected();
        $actual = $class->isConnected();
        $this->assertEquals($expected, $actual);
        $class->disconnect();
    }


    public function testDisconnect()
    {
        $class = new Connection();
        $reflection = new \ReflectionClass(Connection::class);
        $propertyResource = $reflection->getProperty('resource');
        $propertyResource->setAccessible(true);
        $propertyConnectionString = $reflection->getProperty('connectionString');
        $propertyConnectionString->setAccessible(true);
        $propertySecurity = $reflection->getProperty('security');
        $propertySecurity->setAccessible(true);

        $class->enableEncryption(Connection::TLS);
        $class->setConnectionString('tcp://google.com:443');
        $class->connect();
        $class->disconnect();
        $expected = false;
        $class->isConnected();
        $actual = $class->isConnected();
        $this->assertEquals($expected, $actual);
    }


    public function testTransmitMessage()
    {
        $class = new Connection();
        $reflection = new \ReflectionClass(Connection::class);
        $propertyResource = $reflection->getProperty('resource');
        $propertyResource->setAccessible(true);
        $propertyConnectionString = $reflection->getProperty('connectionString');
        $propertyConnectionString->setAccessible(true);
        $propertySecurity = $reflection->getProperty('security');
        $propertySecurity->setAccessible(true);

        $class->enableEncryption(Connection::TLS);
        $class->setConnectionString('tcp://google.com:443');
        $class->connect();
        $class->transmitMessage("GET / HTTP/1.0\r\nHost: google.com\r\n\r\n\r\n");
        $class->disconnect();
    }

    public function testReadMessage()
    {
        $class = new Connection();
        $reflection = new \ReflectionClass(Connection::class);
        $propertyResource = $reflection->getProperty('resource');
        $propertyResource->setAccessible(true);
        $propertyConnectionString = $reflection->getProperty('connectionString');
        $propertyConnectionString->setAccessible(true);
        $propertySecurity = $reflection->getProperty('security');
        $propertySecurity->setAccessible(true);

        $class->enableEncryption(Connection::TLS);
        $class->setConnectionString('tcp://google.com:443');
        $class->connect();
        $class->transmitMessage("GET / HTTP/1.0\r\nHost: google.com\r\n\r\n\r\n");
        $class->readMessage();
        $class->disconnect();
    }

    public function testIsEndOfLine()
    {
        $class = new Connection();
        $class->enableEncryption(Connection::TLS);
        $class->setConnectionString('tcp://google.com:443');
        $class->connect();
        $class->transmitMessage("GET / HTTP/1.0\r\nHost: google.com\r\n\r\n\r\n");
        $this->assertFalse($class->isEndOfFile());

        // It is generally a bad practice to have loops in unit tests.
        // however, we need to get to the end of the html file
        // in order to test this method.
        while(!$class->isEndOfFile()) {
            $class->readMessage();
        }

        $this->assertTrue($class->isEndOfFile());

        $class->disconnect();
    }

    public function testSetLogger()
    {
        $log = new \SalesAgility\Utility\PimapLogger();
        $object = new Connection();
        $reflection = new \ReflectionClass(Connection::class);
        $propertyLog = $reflection->getProperty('log');
        $propertyLog->setAccessible(true);

        $object->setLogger($log);
        $this->assertEquals($log, $propertyLog->getValue($object));
    }
}
