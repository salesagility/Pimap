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

use SalesAgility\Imap\ManagerFactory;
use SalesAgility\Imap\Manager\PhpImapExtensionManager;
use SalesAgility\Imap\Manager\PimapManager;
use SalesAgility\Imap\Stream\MessageTransporter;
use SalesAgility\Imap\Stream\PhpImapExtensionMessageTransporter;
use SalesAgility\Stream\Connection;
use SalesAgility\Imap\Pipeline\Pipeline;
use SalesAgility\Imap\Stream\PhpImpExtensionConnection;

class ImapManagerFactoryTest extends \Codeception\Test\Unit
{
    public function testInstance()
    {
        $object = ManagerFactory::instance();
        $this->assertTrue($object instanceof ManagerFactory);
    }

    public function testHas()
    {
        $object = ManagerFactory::instance();
        $this->assertTrue($object->has(PimapManager::class));
        $this->assertTrue($object->has(Pipeline::class));
        $this->assertTrue($object->has(Connection::class));
        $this->assertTrue($object->has(MessageTransporter::class));
        $this->assertTrue($object->has(PhpImapExtensionManager::class));
        $this->assertTrue($object->has(PhpImpExtensionConnection::class));
        $this->assertTrue($object->has(PhpImapExtensionMessageTransporter::class));
    }

    public function testGet()
    {
        $object = ManagerFactory::instance();
        $this->assertTrue($object->get(PimapManager::class) instanceof  PimapManager);
    }

    public function testPhpManager()
    {
        $object = ManagerFactory::instance()->PimapManager();
        $this->assertTrue($object instanceof  PimapManager);
    }

    public function testNativePhpManager()
    {
        $object = ManagerFactory::instance()->PhpImapExtensionManager();
        $this->assertTrue($object instanceof PhpImapExtensionManager);
    }
}
