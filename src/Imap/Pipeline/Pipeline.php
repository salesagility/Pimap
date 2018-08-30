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

namespace SalesAgility\Imap\Pipeline;

use Psr\Container\ContainerInterface;
use SalesAgility\Imap\CommandBuilder\CommandBuildArgumentsInterface;
use SalesAgility\Imap\CommandBuilder\CommandBuildInterface;
use SalesAgility\Pattern\ContainerAwareInterface;

/**
 * Class Pipeline
 * @package SalesAgility\Imap
 * Pipeline used to queue Imap Pipes and store responses
 */
class Pipeline implements PipelineInterface, ContainerAwareInterface
{
    /** @var ContainerInterface $container */
    private $container;

    /** @var int $current */
    private $current = 0;

    /** @var int $last */
    private $last;

    /** @var int $length */
    private $length;

    /**
     * @var PipeInterface[] $pipes
     */
    private $pipes;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Return the current element
     * @return PipeInterface
     */
    public function current()
    {
        return $this->pipes[$this->current];
    }

    /**
     *  Move forward to next element
     */
    public function next()
    {
        ++$this->current;
    }

    /**
     * Return the key of the current element
     * @return int
     */
    public function key()
    {
        return $this->current;
    }

    /**
     * Checks if current position is valid
     * @return bool
     */
    public function valid()
    {
        return $this->length !== 0 && $this->current <= $this->last;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        $this->current = 0;
    }

    /**
     * @param CommandBuildInterface $command
     * @throws \Exception
     */
    public function add(CommandBuildInterface $command)
    {
        ++$this->length;
        $tag = 'A' . ($this->length);
        $pipe = new Pipe($tag, $command);
        $this->pipes[] = $pipe;
    }

    /**
     * @return PipeInterface[]
     * Recommended: use a reference eg $commands = &$pipeline->getPipes();
     */
    public function pipes()
    {
        return $this->pipes;
    }

    /**
     * @return PipeInterface
     */
    public function getLastPipe()
    {
        $key = count($this->pipes) - 1;
        return $this->pipes[$key];
    }

    /**
     * @param CommandBuildArgumentsInterface $command
     * @return PipeInterface|null
     */
    public function pipeByCommand($command)
    {
        /** @var Pipe $pipe */
        foreach ($this->pipes as $pipe) {
            $comparator = substr($command->asString(), strlen($pipe->getTag()));
            $pipeComparator = substr($pipe->getCommand()->asString(), strlen($pipe->getTag()));
            if ($pipeComparator === $comparator) {
                return $pipe;
            }
        }
        return null;
    }

    /**
     * @param PipelineInterface $pipeline
     * @return null|void
     */
    public function mergePipeline($pipeline)
    {

        $skipCommands = array(
            'LOGIN',
            'LOGOUT',
            'NOOP',
            'IDLE',
            'SELECT',
            'EXAMINE',
            'CHECK',
        );

        /** @var PipeInterface $pipe */
        foreach ($pipeline->pipes as $pipe) {
            $command = $pipe->getCommand()->command();
            if (in_array($command, $skipCommands) == false) {
                ++$this->length;
                $this->pipes[] = $pipe;
            }
        }
    }
}