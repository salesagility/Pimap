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

use SalesAgility\Imap\Manager\PhpImapExtensionManager;
use \SalesAgility\Imap\ManagerFactory;

class NativePhpImapManagerTest extends \Codeception\Test\Unit
{

    public function test__construct()
    {
        $factory = \SalesAgility\Imap\ManagerFactory::instance();
        $object = new PhpImapExtensionManager($factory);
        $this->assertInstanceOf(\SalesAgility\Imap\Manager\ManagerInterface::class, $object);
        $this->assertInstanceOf(\SalesAgility\Pattern\ContainerAwareInterface::class, $object);
        $this->assertInstanceOf(\SalesAgility\Imap\Pipeline\PipeLineAwareInterface::class, $object);
        $this->assertInstanceOf(\Psr\Log\LoggerAwareInterface::class, $object);
    }

    public function testSetTransporter()
    {
        $transporter = new \SalesAgility\Imap\Stream\PhpImapExtensionMessageTransporter(ManagerFactory::instance());
        $object = ManagerFactory::instance()->PhpImapExtensionManager();
        $object->setTransporter($transporter);
        $reflection = new ReflectionClass(PhpImapExtensionManager::class);
        $property = $reflection->getProperty('transporter');
        $property->setAccessible(true);
        $this->assertEquals($transporter, $property->getValue($object));
    }

    public function testTransporter()
    {
        $transporter = new \SalesAgility\Imap\Stream\PhpImapExtensionMessageTransporter(ManagerFactory::instance());
        $object = ManagerFactory::instance()->PhpImapExtensionManager();
        $object->setTransporter($transporter);
        $this->assertEquals($transporter, $object->transporter());
    }

    public function testCommand()
    {
        $object = ManagerFactory::instance()->PhpImapExtensionManager();
        $this->assertInstanceOf(\SalesAgility\Imap\CommandBuilder\PhpImapExtensionSupportedCommandsInterface::class, $object->command());
    }

    public function testRun()
    {
        $connection = new \SalesAgility\Imap\Stream\PhpImpExtensionConnection(ManagerFactory::instance());
        $reflection = new ReflectionClass(\SalesAgility\Stream\MockConnection::class);
        $property = $reflection->getProperty('messageReceived');
        $property->setAccessible(true);
        $property->setValue(
            $connection,
            array('A1 OK nothing happened'."\x0A\x0D")
        );

        $object = ManagerFactory::instance()->PhpImapExtensionManager();
        $object->transporter()->setConnection($connection);
        /** @var \SalesAgility\Imap\Response\Response $actual */
        $actual = $object->run($object->command()->logout());
        $this->assertEquals('OK', $actual->status());
        $this->assertEquals('A1 OK Logout completed.', $actual->message()->toString());
        $actualIncluded = $actual->included()->toString();
        $this->assertEquals('* BYE Logging out', $actualIncluded);
    }

    public function testSetLogger()
    {
        $log = new \SalesAgility\Utility\PimapLogger();
        $manager = ManagerFactory::instance()->PhpImapExtensionManager();
        $manager->setLogger($log);
        $reflection = new \ReflectionClass(PhpImapExtensionManager::class);
        $property = $reflection->getProperty('log');
        $property->setAccessible(true);
        $this->assertEquals($log, $property->getValue($manager));
    }

    public function testSetPipeLine()
    {
        $pipeline = new \SalesAgility\Imap\Pipeline\Pipeline(ManagerFactory::instance());
        $manager = ManagerFactory::instance()->PhpImapExtensionManager();
        $manager->setPipeLine($pipeline);
        $reflection = new \ReflectionClass(PhpImapExtensionManager::class);
        $property = $reflection->getProperty('pipeline');
        $property->setAccessible(true);
        $this->assertEquals($pipeline, $property->getValue($manager));
    }

    public function testPipeline()
    {
        $pipeline = new \SalesAgility\Imap\Pipeline\Pipeline(ManagerFactory::instance());
        $manager = ManagerFactory::instance()->PhpImapExtensionManager();
        $manager->setPipeLine($pipeline);
        $this->assertEquals($pipeline, $manager->pipeline());
    }
}
