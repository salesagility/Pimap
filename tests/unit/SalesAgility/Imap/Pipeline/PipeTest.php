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

use SalesAgility\Imap\Pipeline\Pipe;
use SalesAgility\Imap\CommandBuilder\PimapCommandBuilder;


class PipeTest extends \Codeception\Test\Unit
{
    /** @var UnitTester $tester */
    protected $tester;

    /**
     * @throws ReflectionException
     */
    public function test__construct()
    {
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);
        $reflection = new \ReflectionClass(Pipe::class);
        $propertyTag = $reflection->getProperty('tag');
        $propertyTag->setAccessible(true);
        $propertyPipe = $reflection->getProperty('command');
        $propertyPipe->setAccessible(true);
        $propertyResponse = $reflection->getProperty('response');
        $propertyResponse->setAccessible(true);

        $this->assertEquals(
            'A1',
            $propertyTag->getValue($class),
            'Missing Requirement: Each command must prefixed with a tag.'
            . ' A Pipe in a Pipeline must record the tag, as commands can may be processed in a batch'
        );

        $this->assertEquals(
            $command,
            $propertyPipe->getValue($class),
            'Missing Requirement: A Pipe in a Pipeline must record the raw/full command sent'
        );

        $this->assertEquals(
            'array',
            gettype($propertyResponse->getValue($class)),
            'Missing Requirement: A Pipe in a Pipeline must be able to store multiple raw responses from a command'
        );

        $this->assertEmpty(
            $propertyResponse->getValue($class),
            'Missing Requirement: The responses must initially be a empty array'
        );

        // Test instantiations
        $this->tester->expectException(
            new \Exception('$tag must be a string eg A1'),
            function () use ($command) {
                new Pipe(array(), $command);
            });

        $this->tester->expectException(
            new \Exception('$tag must not be empty'),
            function () use ($command) {
                new Pipe('', $command);
            });
    }

    /**
     * @throws ReflectionException
     */
    public function testAddResponse()
    {
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);
        $reflection = new \ReflectionClass(Pipe::class);
        $propertyTag = $reflection->getProperty('tag');
        $propertyTag->setAccessible(true);
        $propertyPipe = $reflection->getProperty('command');
        $propertyPipe->setAccessible(true);
        $propertyResponse = $reflection->getProperty('response');
        $propertyResponse->setAccessible(true);

        $this->tester->expectException(
            new \Exception('$response must be a string eg A1 OK'),
            function () use ($class) {
                $class->addResponse(1);
            });

        $class->addResponse('A1 OK');
        $this->assertEquals('A1 OK', $propertyResponse->getValue($class)[0]);
    }

    public function testGetResponse()
    {
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);
        $reflection = new \ReflectionClass(Pipe::class);
        $propertyTag = $reflection->getProperty('tag');
        $propertyTag->setAccessible(true);
        $propertyPipe = $reflection->getProperty('command');
        $propertyPipe->setAccessible(true);
        $propertyResponse = $reflection->getProperty('response');
        $propertyResponse->setAccessible(true);

        $class->addResponse('A1 OK');
        $this->assertEquals('A1 OK', $class->getResponse());
    }

    /**
     * @throws ReflectionException
     */
    public function testGetTag()
    {
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);
        $reflection = new \ReflectionClass(Pipe::class);
        $propertyTag = $reflection->getProperty('tag');
        $propertyTag->setAccessible(true);
        $propertyPipe = $reflection->getProperty('command');
        $propertyPipe->setAccessible(true);
        $propertyResponse = $reflection->getProperty('response');
        $propertyResponse->setAccessible(true);
        $this->assertEquals("A1", $class->getTag());
    }

    public function testBuildCommand()
    {
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);
        $reflection = new \ReflectionClass(Pipe::class);
        $propertyTag = $reflection->getProperty('tag');
        $propertyTag->setAccessible(true);
        $propertyPipe = $reflection->getProperty('command');
        $propertyPipe->setAccessible(true);
        $propertyResponse = $reflection->getProperty('response');
        $propertyResponse->setAccessible(true);
        $this->assertEquals("A1 NOOP", $class->buildCommand());
    }


    public function testAddTokenList()
    {
        $tokenList = new \SalesAgility\Imap\Token\TokenList();
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);
        $reflection = new \ReflectionClass(Pipe::class);
        $property = $reflection->getProperty('tokenList');
        $property->setAccessible(true);
        $class->addTokenList($tokenList);
        $this->assertEquals($tokenList, $property->getValue($class));
    }

    public function testTokenList()
    {
        $tokenList = new \SalesAgility\Imap\Token\TokenList();
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);

        $class->addTokenList($tokenList);
        $this->assertEquals($tokenList, $class->tokenList());
    }

    public function testIsTokenized()
    {
        $tokenList = new \SalesAgility\Imap\Token\TokenList();
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);
        $this->assertFalse($class->isTokenized());
        $class->addTokenList($tokenList);
        $this->assertTrue($class->isTokenized());
    }


    public function testAddLexemeList()
    {
        $lexemeList = new \SalesAgility\Imap\Lexeme\LexemeList();
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);
        $reflection = new \ReflectionClass(Pipe::class);
        $property = $reflection->getProperty('lexemeList');
        $property->setAccessible(true);
        $class->addLexemeList($lexemeList);
        $this->assertEquals($lexemeList, $property->getValue($class));
    }

    public function testLexemeList()
    {
        $lexemeList = new \SalesAgility\Imap\Lexeme\LexemeList();
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);

        $class->addLexemeList($lexemeList);
        $this->assertEquals($lexemeList, $class->lexemeList());
    }

    public function testIsLexemized()
    {
        $lexemeList = new \SalesAgility\Imap\Lexeme\LexemeList();
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);
        $this->assertFalse($class->isLexemized());
        $class->addLexemeList($lexemeList);
        $this->assertTrue($class->isLexemized());
    }

    public function testGetCommand()
    {
        $command = PimapCommandBuilder::instance()->noop();
        $class = new Pipe('A1', $command);
        $this->assertEquals($command, $class->getCommand());
    }

    public function  testAddParsed()
    {
        $reflection = new \ReflectionClass(Pipe::class);
        /** @var ReflectionProperty $property */
        $property = $reflection->getProperty('parsed');
        $property->setAccessible(true);

        $command = PimapCommandBuilder::instance()->noop();
        $includedMessage = \SalesAgility\Iteration\StringIterator::withLiteral('', 0, 0);
        $responseMessage = \SalesAgility\Iteration\StringIterator::withLiteral( 'A1 OK '."\r\n");
        $response = new SalesAgility\Imap\Response\Response('OK', $responseMessage, $includedMessage);
        $pipe = new Pipe('A1', $command);


        $pipe->addParsed($response);
        $expected  = $property->getValue($pipe);
        $this->assertEquals($response, $expected);
    }

    public function  testParsed()
    {
        $command = PimapCommandBuilder::instance()->noop();
        $includedMessage = \SalesAgility\Iteration\StringIterator::withLiteral('', 0, 0);
        $responseMessage = \SalesAgility\Iteration\StringIterator::withLiteral( 'A1 OK '."\r\n");
        $response = new SalesAgility\Imap\Response\Response('OK', $responseMessage, $includedMessage);
        $pipe = new Pipe('A1', $command);
        $pipe->addParsed($response);
        $this->assertEquals($response, $pipe->parsed());
    }

}
