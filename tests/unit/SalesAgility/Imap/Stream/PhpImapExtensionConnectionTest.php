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

use SalesAgility\Imap\Stream\PhpImpExtensionConnection;
use \SalesAgility\Imap\ManagerFactory;

class PhpImapExtensionConnectionTest extends \Codeception\Test\Unit
{
    /** @var UnitTester */
    protected $tester;

    public function testSetConnectionString()
    {
        $object = new PhpImpExtensionConnection(ManagerFactory::instance());
        $reflection = new \ReflectionClass(PhpImpExtensionConnection::class);
        $server = $reflection->getProperty('server');
        $server->setAccessible(true);
        $port = $reflection->getProperty('port');
        $port->setAccessible(true);
        $object->setConnectionString('tcp://localhost:143');
        $this->assertEquals('localhost', $server->getValue($object));
        $this->assertEquals('143', $port->getValue($object));

        // negative tests
        $this->tester->expectException(
            new \Exception('Expected tcp://hostname:portnumber'),
            function () use ($object) {
                $object->setConnectionString('{localhost:143}');
            }
        );
    }

    public function testReadMessage()
    {
        $object =new PhpImpExtensionConnection(ManagerFactory::instance());
        $this->tester->expectException(
            new \Exception('Not supported'),
            function () use ($object) {
                $object->readMessage();
            }
        );
    }

    public function testTransmitMessage()
    {
        $object =new PhpImpExtensionConnection(ManagerFactory::instance());
        $this->tester->expectException(
            new \Exception('Not supported'),
            function () use ($object) {
                $object->transmitMessage('');
            }
        );
    }

    public function testIsEndOfFile()
    {
        $object =new PhpImpExtensionConnection(ManagerFactory::instance());
        $this->tester->expectException(
            new \Exception('Not supported'),
            function () use ($object) {
                $object->isEndOfFile();
            }
        );
    }

    public function testEnableEncryption()
    {
        $object =new PhpImpExtensionConnection(ManagerFactory::instance());
        $reflection = new \ReflectionClass(PhpImpExtensionConnection::class);
        $security = $reflection->getProperty('security');
        $security->setAccessible(true);
        $object->enableEncryption();
        $this->assertEquals('/imap/ssl/novalidate-cert', $security->getValue($object));
    }

    public function testDisableEncryption()
    {
        $object =new PhpImpExtensionConnection(ManagerFactory::instance());
        $reflection = new \ReflectionClass(PhpImpExtensionConnection::class);
        $security = $reflection->getProperty('security');
        $security->setAccessible(true);
        $object->enableEncryption();
        $object->disableEncryption();
        $this->assertEquals('', $security->getValue($object));
    }

    public function testConnect()
    {
        $object =new PhpImpExtensionConnection(ManagerFactory::instance());
        $reflection = new \ReflectionClass(PhpImpExtensionConnection::class);
        $connection = $reflection->getProperty('connection');
        $connection->setAccessible(true);
        $object->connect();
        $this->assertEquals(1, $connection->getValue($object));
    }

    public function testDisconnect()
    {
        $object =new PhpImpExtensionConnection(ManagerFactory::instance());
        $reflection = new \ReflectionClass(PhpImpExtensionConnection::class);
        $connection = $reflection->getProperty('connection');
        $connection->setAccessible(true);
        $object->connect();
        $object->disconnect();
        $this->assertEquals(null, $connection->getValue($object));

        $object->username = 'user';
        $object->password = '';
        $object->connect();
        $object->disconnect();
        $this->assertEquals(null, $connection->getValue($object));
    }

    public function testIsConnected()
    {
        $object =new PhpImpExtensionConnection(ManagerFactory::instance());
        $reflection = new \ReflectionClass(PhpImpExtensionConnection::class);
        $connection = $reflection->getProperty('connection');
        $connection->setAccessible(true);
        $object->connect();
        $this->assertTrue($object->isConnected());
        $object->disconnect();
        $this->assertFalse($object->isConnected());

        $object->username = 'user';
        $object->password = '';
        $object->connect();
    }
}
