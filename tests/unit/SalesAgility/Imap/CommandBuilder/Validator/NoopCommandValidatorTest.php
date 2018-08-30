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
/**
 * Created by PhpStorm.
 * User: user
 * Date: 7/9/18
 * Time: 3:58 PM
 */

use SalesAgility\Imap\CommandBuilder\CommandValidator\Command\NoopCommandValidator;
use SalesAgility\Imap\CommandBuilder\PimapCommandBuilder;

class NoopCommandValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testValidate()
    {
        $object = new NoopCommandValidator();
        $this->assertEquals('NOOP', $object->command());
    }

    public function testCommand()
    {
        $object = new NoopCommandValidator();
        $command = PimapCommandBuilder::instance()->noop();
        $this->assertTrue($object->validate($command));

        $this->tester->expectException(
            new \Exception('NOOP does not take any arguments.'),
            function () {
                $object = new NoopCommandValidator();
                $command = PimapCommandBuilder::instance()->noop()->user('username');
                $object->validate($command);
            }
        );
    }
}
