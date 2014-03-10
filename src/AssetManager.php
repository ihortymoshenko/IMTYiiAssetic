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

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetInterface;
use Assetic\Cache\FilesystemCache;
use Assetic\AssetWriter;
use Assetic\FilterManager;

use IMT\YiiAssetic\Assetic\Factory\AssetFactory;
use IMT\YiiAssetic\Exception\LogicException;
use IMT\YiiAssetic\Exception\RuntimeException;

/**
 * This class overrides the core Yii class
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class AssetManager extends \CAssetManager
{
    /**
     * Determines whether assets will be cached
     *
     * @var boolean
     */
    public $cache = false;

    /**
     * Determines whether a debug mode will be enabled
     *
     * @var boolean
     */
    public $debug = false;

    /**
     * A map that defines relations between file extensions and filters
     *
     * An array of key-value pairs: the key is the file extension, and the value
     * is the filters to be applied to assets
     *
     * @var array
     */
    public $filtersByExt = array();

    /**
     * User-defined filters, can be used to override the core filters
     *
     * An array of key-value pairs: the key is the alias, and the value is the
     * class name. However, if you need a more complex filter, specify the value
     * to an array of options. In that case, the `factoryClass` option is
     * required and will be used to create the filter using the filter factory.
     * An array of options will be passed to the `create` method of the filter
     * factory. Each filter factory must implement the
     * `IMT\YiiAssetic\FilterFactoryInterface` interface
     *
     * @var array
     */
    public $userFilters  = array();

    /**
     * An array of key-value pairs: the key is the alias, and the value is the
     * class name
     *
     * @var array
     */
    public $workers = array();

    /**
     * @var AssetFactory
     */
    protected $assetFactory;

    /**
     * @var \Assetic\AssetManager
     */
    protected $assetManager;

    /**
     * @var AssetWriter
     */
    protected $assetWriter;

    /**
     * Built-in filters, can be replaced with user-defined filters
     *
     * @var array
     */
    protected $coreFilters = array(
        'coffee_scipt'                => 'Assetic\Filter\CoffeeScriptFilter',
        'compass'                     => 'Assetic\Filter\CompassFilter',
        'css_embed'                   => 'Assetic\Filter\CssEmbedFilter',
        'css_import'                  => 'Assetic\Filter\CssImportFilter',
        'css_min'                     => 'Assetic\Filter\CssMinFilter',
        'css_rewrite'                 => 'Assetic\Filter\CssRewriteFilter',
        'dart'                        => 'Assetic\Filter\DartFilter',
        'ember_precompiler'           => 'Assetic\Filter\EmberPrecompileFilter',
        'google_closure_compiler_api' => 'Assetic\Filter\GoogleClosure\CompilerApiFilter',
        'google_closure_compiler_jar' => 'Assetic\Filter\GoogleClosure\CompilerJarFilter',
        'gss'                         => 'Assetic\Filter\GssFilter',
        'handlebars'                  => 'Assetic\Filter\HandlebarsFilter',
        'jpegoptim'                   => 'Assetic\Filter\JpegoptimFilter',
        'jpegtran'                    => 'Assetic\Filter\JpegtranFilter',
        'js_min'                      => 'Assetic\Filter\JSMinFilter',
        'js_min_plus'                 => 'Assetic\Filter\JSMinPlusFilter',
        'less'                        => 'Assetic\Filter\LessFilter',
        'lessphp'                     => 'Assetic\Filter\LessphpFilter',
        'opti_png'                    => 'Assetic\Filter\OptiPngFilter',
        'packager'                    => 'Assetic\Filter\PackagerFilter',
        'packer'                      => 'Assetic\Filter\PackerFilter',
        'php_css_embed'               => 'Assetic\Filter\PhpCssEmbedFilter',
        'pngout'                      => 'Assetic\Filter\PngoutFilter',
        'sass'                        => 'Assetic\Filter\Sass\SassFilter',
        'scss'                        => 'Assetic\Filter\Sass\ScssFilter',
        'scssphp'                     => 'Assetic\Filter\ScssphpFilter',
        'sprockets'                   => 'Assetic\Filter\SprocketsFilter',
        'stylus'                      => 'Assetic\Filter\StylusFilter',
        'type_script'                 => 'Assetic\Filter\TypeScriptFilter',
        'uglify_css'                  => 'Assetic\Filter\UglifyCssFilter',
        'uglify_js2'                  => 'Assetic\Filter\UglifyJs2Filter',
        'uglify_js'                   => 'Assetic\Filter\UglifyJsFilter',
        'yui_css_compressor'          => 'Assetic\Filter\Yui\CssCompressorFilter',
        'yui_js_compressor'           => 'Assetic\Filter\Yui\JsCompressorFilter',
    );

    /**
     * @var FilterInitializer
     */
    protected $filterInitializer;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * An array of published paths, this property is introduced because the
     * `_published` property in the core Yii class has the `private` scope
     *
     * @var array
     */
    protected $published = array();

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        $this->initWorkers();
    }

    /**
     * {@inheritDoc}
     * @param array  $filtersByExt An array of key-value pairs: the key is the
     *                             file extension, and the value is the filters
     *                             to be applied to assets. If it is not
     *                             specified, will be used the `filtersByExt`
     *                             property
     * @param string $combineTo    Determines whether assets will be combined
     *                             into one asset. Actually it is a filename,
     *                             the file extension should be omitted
     */
    public function publish(
        $path,
        $hashByName = false,
        $level = -1,
        $forceCopy = null,
        array $filtersByExt = array(),
        $combineTo = null
    ) {
        if ($forceCopy === null) {
            $forceCopy = $this->forceCopy;
        }

        if ($this->linkAssets) {
            throw new LogicException(
                sprintf(
                    'The `%s` property is not supported by IMTYiiAssetic.',
                    __CLASS__ . '::linkAssets'
                )
            );
        }

        if (isset($this->published[$path])) {
            return $this->published[$path];
        } elseif (($realPath = realpath($path)) !== false) {
            $hash = $this->generatePath($realPath, $hashByName);

            $options = array(
                'output' => $hash . '/*',
                'root'   => $realPath,
            );

            if ($combineTo) {
                $options['name'] = $combineTo;
            }

            if (is_file($realPath)) {
                $filters = $this->resolveFilterByExt(\CFileHelper::getExtension($realPath), $filtersByExt);
                $asset = $this->createAsset($realPath, $filters, $options);
                $this->writeAsset($asset, $forceCopy);

                return $this->published[$path] = $this->getBaseUrl() . '/' . $asset->getTargetPath();
            } elseif (is_dir($realPath)) {
                $files = \CFileHelper::findFiles($realPath, array(
                    'exclude' => $this->excludeFiles,
                    'level'   => $level,
                ));

                if (!$combineTo) {
                    foreach ($files as $file) {
                        $filters = $this->resolveFilterByExt(\CFileHelper::getExtension($file), $filtersByExt);
                        $asset = $this->createAsset($file, $filters, $options);
                        $this->writeAsset($asset, $forceCopy);
                    }

                    return $this->published[$path] = $this->getBaseUrl() . '/' . $hash;
                } else {
                    $filesByExt = array();

                    foreach ($files as $file) {
                        $filesByExt[\CFileHelper::getExtension($file)][] = $file;
                    }

                    $publishedPaths = array();

                    foreach ($filesByExt as $ext => $files) {
                        // do not combine some file types (e.g. images)
                        if (!in_array($ext, array('css', 'js'), true)) {
                            foreach ($files as $file) {
                                $filters = $this->resolveFilterByExt($ext, $filtersByExt);
                                $asset = $this->createAsset($file, $filters, $options);
                                $this->writeAsset($asset, $forceCopy);
                            }

                            continue;
                        }

                        $filters = $this->resolveFilterByExt($ext, $filtersByExt);
                        $asset = $this->createAsset($files, $filters, $options);
                        $this->writeAsset($asset, $forceCopy);

                        $publishedPaths[$ext] = $this->getBaseUrl() . '/' . $asset->getTargetPath();
                    }

                    return $this->published[$path] = !$publishedPaths
                        ? $this->getBaseUrl() . '/' . $hash
                        : $publishedPaths;
                }
            }
        }

        throw new RuntimeException("The `$path` path does not exist.");
    }

    /**
     * {@inheritDoc}
     */
    public function getPublishedPath($path, $hashByName = false)
    {
        if (($realPath = realpath($path)) !== false) {
            $assetName = $this->getAssetFactory()->generateAssetName($realPath, array(), array('root' => $realPath));

            $base = $this->getBasePath()
                . DIRECTORY_SEPARATOR
                . $this->generatePath($realPath, $hashByName)
                . (!$assetName ? '' : DIRECTORY_SEPARATOR . $assetName);

            return is_file($path) ? $base . DIRECTORY_SEPARATOR . basename($path) : $base;
        } else {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getPublishedUrl($path, $hashByName = false)
    {
        if (isset($this->published[$path])) {
            return $this->published[$path];
        }

        if (($realPath = realpath($path)) !== false) {
            $assetName = $this->getAssetFactory()->generateAssetName($realPath, array(), array('root' => $realPath));

            $base = $this->getBaseUrl()
                . '/'
                . $this->generatePath($realPath, $hashByName)
                . (!$assetName ? '' : DIRECTORY_SEPARATOR . $assetName);

            return is_file($path) ? $base . '/' . basename($path) : $base;
        } else {
            return false;
        }
    }

    /**
     * @return AssetFactory
     */
    public function getAssetFactory()
    {
        if ($this->assetFactory === null) {
            $this->assetFactory = new AssetFactory(\Yii::getPathOfAlias('application'), $this->debug);
            $this->assetFactory->setDefaultOutput('/*');
            $this->assetFactory->setAssetManager($this->getAssetManager());
            $this->assetFactory->setFilterManager($this->getFilterManager());
        }

        return $this->assetFactory;
    }

    /**
     * @param  AssetInterface $asset
     * @return AssetInterface
     */
    protected function cacheAsset(AssetInterface $asset)
    {
        return new AssetCache(
            $asset,
            new FilesystemCache(\Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'assetic')
        );
    }

    /**
     * @param  string|array   $inputs
     * @param  array          $filters
     * @param  array          $options
     * @return AssetInterface
     */
    protected function createAsset($inputs = array(), $filters = array(), array $options = array())
    {
        $this->getFilterInitializer()->initialize($filters);

        $asset = $this->getAssetFactory()->createAsset($inputs, $filters, $options);

        if ($this->cache) {
            return $this->cacheAsset($asset);
        }

        return $asset;
    }

    /**
     * @return \Assetic\AssetManager
     */
    protected function getAssetManager()
    {
        if ($this->assetManager === null) {
            $this->assetManager = new \Assetic\AssetManager();
        }

        return $this->assetManager;
    }

    /**
     * @return AssetWriter
     */
    protected function getAssetWriter()
    {
        if ($this->assetWriter === null) {
            $this->assetWriter = new AssetWriter($this->getBasePath());
        }

        return $this->assetWriter;
    }

    /**
     * @return FilterInitializer
     */
    protected function getFilterInitializer()
    {
        if ($this->filterInitializer === null) {
            $this->filterInitializer = new FilterInitializer(
                $this->getFilterManager(),
                $this->coreFilters,
                $this->userFilters
            );
        }

        return $this->filterInitializer;
    }

    /**
     * @return FilterManager
     */
    protected function getFilterManager()
    {
        if ($this->filterManager === null) {
            $this->filterManager = new FilterManager();
        }

        return $this->filterManager;
    }

    protected function initWorkers()
    {
        foreach ($this->workers as $workerClass) {
            $this->getAssetFactory()->addWorker(new $workerClass());
        }
    }

    /**
     * Resolves filter names accordingly to the file extension and a map that
     * defines relations between file extensions and filters
     *
     * If the `filtersByExt` argument is an empty array, will be used the
     * `filtersByExt` property
     *
     * @param  string $ext          The file extension
     * @param  array  $filtersByExt A map that defines relations between file
     *                              extensions and filters
     * @return array
     */
    protected function resolveFilterByExt($ext, array $filtersByExt = array())
    {
        if (!$filtersByExt) {
            $filtersByExt = $this->filtersByExt;
        }

        if (isset($filtersByExt[$ext])) {
            return $filtersByExt[$ext];
        }

        return array();
    }

    /**
     * @param AssetInterface $asset
     * @param boolean        $forceCopy Determines whether the asset will be
     *                                  forcefully written
     */
    protected function writeAsset(AssetInterface $asset, $forceCopy)
    {
        $file = $this->getBasePath() . DIRECTORY_SEPARATOR . $asset->getTargetPath();

        if (!is_file($file) || $asset->getLastModified() > filemtime($file) || $forceCopy) {
            $this->getAssetWriter()->writeAsset($asset);
        }
    }
}
