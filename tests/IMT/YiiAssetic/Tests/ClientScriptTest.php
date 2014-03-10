<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\YiiAssetic\Tests;

use IMT\YiiAssetic\ClientScript;

/**
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class ClientScriptTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderCoreScripts()
    {
        /** @var ClientScript $clientScript */
        $clientScript = $this
            ->getMock('IMT\YiiAssetic\ClientScript', array('getPackageBaseUrl', 'recordCachingAction'));
        $clientScript
            ->expects($this->once())
            ->method('getPackageBaseUrl')
            ->with($this->equalTo('smth'))
            ->will($this->returnValue(array('js' => '/smth.js', 'css' => '/smth.css')));
        $clientScript
            ->expects($this->once())
            ->method('recordCachingAction');

        $clientScript->packages['smth'] = array(
            'basePath'  => __DIR__ . '/Fixture/public',
            'js'        => array(
                'js/file.js',
                'js/file2.js',
            ),
            'css'       => array(
                'css/file.css',
                'css/file2.css',
            ),
            'combineTo' => 'smth',
        );
        $clientScript->registerCoreScript('smth');
        $clientScript->renderCoreScripts();

        $this->assertAttributeEquals(array(array('/smth.js' => '/smth.js')), 'scriptFiles', $clientScript);
        $this->assertAttributeEquals(array('/smth.css' => ''), 'cssFiles', $clientScript);
    }
}
