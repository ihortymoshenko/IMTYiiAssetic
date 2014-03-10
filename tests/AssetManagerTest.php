<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\YiiAssetic;

use IMT\YiiAssetic\AssetManager;

/**
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class AssetManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AssetManager
     */
    private $assetManager;

    /**
     * @var string
     */
    private $basePath;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->basePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'IMTYiiAssetic';
        @mkdir($this->basePath);

        $this->assetManager = $this->createAssetManager();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        @rmdir($this->basePath);

        parent::tearDown();
    }

    public function testInit()
    {
        $this->assetManager->workers = array('test' => __NAMESPACE__ . '\Fixture\Worker');
        $this->assetManager->init();

        $this->assertAttributeCount(1, 'workers', $this->assetManager->getAssetFactory());
    }

    public function testPublishLinkAssets()
    {
        $this->assetManager->linkAssets = true;

        $this->setExpectedException(
            'IMT\YiiAssetic\Exception\LogicException',
            'The `IMT\YiiAssetic\AssetManager::linkAssets` property is not supported by IMTYiiAssetic.'
        );

        $this->assetManager->publish(str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Fixture/public/css/file.css'));
    }

    public function testPublishCombineTo()
    {
        $rm = new \ReflectionMethod('IMT\YiiAssetic\AssetManager', 'generatePath');
        $rm->setAccessible(true);

        $path = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Fixture/public');
        $hash = $rm->invoke($this->assetManager, $path);

        $this->assertEquals(
            array(
                'js'  => "/$hash/smth.js",
                'css' => "/$hash/smth.css",
            ),
            $this->assetManager->publish($path, false, -1, null, array(), 'smth')
        );
    }

    public function testGetPublishedPathNotExists()
    {
        $this->assertFalse($this->assetManager->getPublishedPath(uniqid()));
    }

    public function testGetPublishedPathFile()
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Fixture/public/css/file.css');

        $this->assertEquals(
            $this->assetManager->getPublishedPath($path),
            $this->basePath . $this->assetManager->publish($path)
        );
    }

    public function testGetPublishedPathDirectory()
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Fixture/public');

        $this->assertEquals(
            $this->assetManager->getPublishedPath($path),
            $this->basePath . $this->assetManager->publish($path)
        );
    }

    public function testGetPublishedUrlMemoized()
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Fixture/public/css/file.css');

        $this->assertEquals(
            $this->assetManager->publish($path),
            $this->assetManager->getPublishedUrl($path)
        );
    }

    public function testGetPublishedUrlNotExists()
    {
        $this->assertFalse($this->assetManager->getPublishedUrl(uniqid()));
    }

    public function testGetPublishedUrlFile()
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Fixture/public/css/file.css');

        $this->assertEquals(
            $this->assetManager->getPublishedUrl($path),
            $this->assetManager->publish($path)
        );
    }

    public function testGetPublishedUrlDirectory()
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Fixture/public');

        $this->assertEquals(
            $this->assetManager->getPublishedUrl($path),
            $this->assetManager->publish($path)
        );
    }

    public function testWriteAssetNotExists()
    {
        $path = tempnam(sys_get_temp_dir(), uniqid());

        $this->assertFileExists($this->basePath . $this->assetManager->publish($path));
    }

    public function testWriteAssetNotModified()
    {
        $path          = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Fixture/public/css/file.css');
        $publishedPath = $this->basePath . $this->assetManager->publish($path);

        $filemtime = filemtime($publishedPath);

        sleep(1);

        $assetManager = $this->createAssetManager();
        $assetManager->publish($path);

        $this->assertEquals($filemtime, filemtime($publishedPath));
    }

    public function testWriteAssetForced()
    {
        $path          = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Fixture/public/css/file.css');
        $publishedPath = $this->basePath . $this->assetManager->publish($path);

        $filemtime = filemtime($publishedPath);

        sleep(1);

        $assetManager = $this->createAssetManager();
        $assetManager->publish($path, false, -1, true);

        $this->assertNotEquals($filemtime, filemtime($publishedPath));
    }

    /**
     * @return AssetManager
     */
    private function createAssetManager()
    {
        $assetManager = new AssetManager();

        $assetManager->setBasePath($this->basePath);
        $assetManager->setBaseUrl('/');

        return $assetManager;
    }
}
