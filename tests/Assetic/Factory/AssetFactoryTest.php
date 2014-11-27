<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\YiiAssetic\Assetic\Factory;

use IMT\YiiAssetic\Assetic\Factory\AssetFactory;

/**
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class AssetFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateAssetName()
    {
        $root = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');

        $assetFactory = new AssetFactory($root);

        $this->assertEquals(
            'Factory/AssetFactoryTest',
            $assetFactory->generateAssetName(array(__FILE__), array(), array('root' => $root))
        );
    }

    public function testGenerateAssetNameWithAssetNameEqualsToFolderName()
    {
        $root = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');

        $assetFactory = new AssetFactory($root);

        $this->assertEquals(
            'some_dir/normalize.css/normalize',
            $assetFactory->generateAssetName(array("/some_dir/normalize.css/normalize.css"), array(), array('root' => $root))
        );
    }
}
