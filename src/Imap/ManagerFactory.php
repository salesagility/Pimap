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


namespace SalesAgility\Imap;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use SalesAgility\Pattern\Singleton;
use SalesAgility\Imap\Manager\PhpImapExtensionManager;
use SalesAgility\Imap\Manager\PimapManager;
use SalesAgility\Imap\Pipeline\Pipeline;
use SalesAgility\Imap\Stream\MessageTransporter;
use SalesAgility\Imap\Stream\PhpImapExtensionMessageTransporter;
use SalesAgility\Imap\Stream\PhpImpExtensionConnection;
use SalesAgility\Stream\Connection;
use SalesAgility\Utility\PimapLogger;

/**
 * Class ManagerFactory
 * @package SalesAgility\Imap\Pipeline
 */
class ManagerFactory implements Singleton, ContainerInterface
{
    private $containers = array();

    private function __construct()
    {
        $this->containers = array(
            PimapManager::class => new PimapManager($this),
            Pipeline::class => new Pipeline($this),
            Connection::class => new Connection($this),
            MessageTransporter::class => new MessageTransporter($this),
            PhpImapExtensionManager::class => new PhpImapExtensionManager($this),
            PhpImpExtensionConnection::class => new PhpImpExtensionConnection($this),
            PhpImapExtensionMessageTransporter::class => new PhpImapExtensionMessageTransporter($this),
            PimapLogger::class => new PimapLogger(),
            'Logger' => new PimapLogger()
        );
    }

    /**
     * @return ManagerFactory
     */
    public static function instance()
    {
        return new self();
    }

    /**
     * @return PimapManager
     */
    public function PimapManager()
    {
        /** @var MessageTransporter $transporter */
        $transporter = $this->get(MessageTransporter::class);
        /** @var Pipeline $pipeline */
        $pipeline = $this->get(Pipeline::class);
        /** @var PimapManager $manager */
        $manager = $this->get(PimapManager::class);
        /** @var PimapManager $manager */
        $connection = $this->get(Connection::class);

        $transporter->setConnection($connection);
        $transporter->setPipeLine($pipeline);
        $manager->setTransporter($transporter);
        $manager->setPipeLine($pipeline);

        return $manager;
    }

    /**
     * @return PhpImapExtensionManager
     * @throws \Exception
     */
    public function PhpImapExtensionManager()
    {
        /** @var PhpImapExtensionMessageTransporter $transporter */
        $transporter = $this->get(PhpImapExtensionMessageTransporter::class);
        /** @var Pipeline $pipeline */
        $pipeline = $this->get(Pipeline::class);
        /** @var PhpImapExtensionManager $manager */
        $manager = $this->get(PhpImapExtensionManager::class);

        $connection = $this->get(PhpImpExtensionConnection::class);
        $transporter->setConnection($connection);
        $transporter->setPipeLine($pipeline);
        $manager->setTransporter($transporter);
        $manager->setPipeLine($pipeline);
        $manager->setLogger($this->get(PimapLogger::class));

        return $manager;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->containers[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->containers);
    }
}