<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 27/08/18
 * Time: 14:21
 */

use SalesAgility\Utility\PimapLogger;

class PimapLoggerTest extends \Codeception\Test\Unit
{

    /** @var UnitTester $tester */
    protected $tester;

    public function testLog()
    {
        $object = new PimapLogger();
        $object->emergency('{a}', array('a' => 'test'));
        $object->alert('{a}', array('a' => 'test'));
        $object->critical('{a}', array('a' => 'test'));
        $object->error('{a}', array('a' => 'test'));
        $object->warning('{a}', array('a' => 'test'));
        $object->notice('{a}', array('a' => 'test'));
        $object->info('{a}', array('a' => 'test'));
        $object->debug('{a}', array('a' => 'test'));
        $object->debug('test');

        $this->tester->expectException(
            new \InvalidArgumentException('Invalid Log Level'),
            function () {
                $object = new PimapLogger();
                $object->log('invalidLevel', 'message');
            }
        );
    }
}
