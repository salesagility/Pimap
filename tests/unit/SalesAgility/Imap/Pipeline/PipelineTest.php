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

use SalesAgility\Imap\Pipeline\Pipeline;
use SalesAgility\Imap\Pipeline\Pipe;
use \SalesAgility\Imap\ManagerFactory;
use SalesAgility\Imap\CommandBuilder\PimapCommandBuilder;
class PipelineTest extends \Codeception\Test\Unit
{
    /**
     * @throws ReflectionException
     */
    public function test__construct()
    {
        $class = new Pipeline(\SalesAgility\Imap\ManagerFactory::instance());
        $reflection = new \ReflectionClass(Pipeline::class);
        $propertyPipes = $reflection->getProperty('pipes');
        $propertyPipes->setAccessible(true);
        $this->assertEmpty(array(), $propertyPipes->getValue($class));
    }

    /**
     *
     */
    public function testAdd()
    {
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipeline(\SalesAgility\Imap\ManagerFactory::instance());
        $class->add($command);
        $c1 = $class->pipes()[0];
        $this->assertEquals('A1', $c1->getTag());
    }

    /**
     * @throws ReflectionException
     */
    public function testGetPipes()
    {
        $noop = PimapCommandBuilder::instance()->noop();
        $logout = PimapCommandBuilder::instance()->logout();
        $class = new Pipeline(ManagerFactory::instance());
        $reflection = new \ReflectionClass(Pipeline::class);
        $propertyPipes = $reflection->getProperty('pipes');
        $propertyPipes->setAccessible(true);
        $class->add($noop);
        $class->add($logout);

        $c1 = $class->pipes()[0];
        $c2 = $class->pipes()[1];
        $this->assertEquals('A1', $c1->getTag());
        $this->assertEquals('A2', $c2->getTag());
    }

    public function testCurrent() {

        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipeline(ManagerFactory::instance());
        $class->add($command);
        $c1 = $class->pipes()[0];
        $this->assertEquals($c1, $class->current());
    }

    public function testNext()
    {
        $command1 = PimapCommandBuilder::instance()->noop();
        $command2 = PimapCommandBuilder::instance()->logout();
        $class = new Pipeline(ManagerFactory::instance());
        $class->add($command1);
        $class->add($command2);
        $c1 = $class->pipes()[0];
        $c2 = $class->pipes()[1];
        $this->assertEquals($c1, $class->current());
        $class->next();
        $this->assertEquals($c2, $class->current());
    }

    public function testKey()
    {
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipeline(ManagerFactory::instance());
        $class->add($command);
        $c1 = $class->pipes()[0];
        $this->assertEquals(0, $class->key());
    }

    public function testValid()
    {
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipeline(ManagerFactory::instance());
        $class->add($command);
        $c1 = $class->pipes()[0];
        $this->assertTrue($class->valid());
        $class->next();
        $this->assertFalse($class->valid());
    }

    public function testRewind()
    {
        $command1 = PimapCommandBuilder::instance()->noop();
        $command2 = PimapCommandBuilder::instance()->logout();
        $class = new Pipeline(ManagerFactory::instance());
        $class->add($command1);
        $class->add($command2);
        $c1 = $class->pipes()[0];
        $c2 = $class->pipes()[1];
        $class->next();
        $class->rewind();
        $this->assertEquals($c1, $class->current());
    }

    public function testGetLastPipe()
    {
        $command1 = PimapCommandBuilder::instance()->noop();
        $command2 = PimapCommandBuilder::instance()->logout();
        $class = new Pipeline(ManagerFactory::instance());
        $class->add($command1);
        $class->add($command2);

        $actual = $class->getLastPipe();
        $this->assertInstanceOf(Pipe::class, $actual);
        $this->assertEquals('A2 LOGOUT', $actual->buildCommand());
    }

    public function testPipeByCommand()
    {
        $command1 = PimapCommandBuilder::instance()->noop()->build();
        $command2 = PimapCommandBuilder::instance()->logout()->build();
        $class = new Pipeline(ManagerFactory::instance());
        $class->add($command1);
        $class->add($command2);

        $actual = $class->pipeByCommand($command2);
        $this->assertInstanceOf(Pipe::class, $actual);

        $command3 = PimapCommandBuilder::instance()->login()->user('user')->password('secret')->build();
        $actual = $class->pipeByCommand($command3);
        $this->assertNull($actual);
    }

    public function testMergePipeline()
    {
        $command1 = PimapCommandBuilder::instance()->delete('INVOICES')->build();
        $pipeline1 = new Pipeline(ManagerFactory::instance());
        $pipeline1->add($command1);

        $command2 = PimapCommandBuilder::instance()->delete('QUOTES')->build();
        $pipeline2 = new Pipeline(ManagerFactory::instance());
        $pipeline2->add($command2);

        $pipeline1->mergePipeline($pipeline2);
        $lastPipe = $pipeline1->pipeByCommand($command2);
        $this->assertInstanceOf(Pipe::class, $lastPipe);
        $this->assertEquals('DELETE', $lastPipe->getCommand()->command());
        $this->assertEquals(array('MAILBOX' => 'QUOTES'), $lastPipe->getCommand()->commandArguments());
    }
}
