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

/**
 * This class overrides the core Assetic class
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class AssetFactory extends \Assetic\Factory\AssetFactory
{
    /**
     * {@inheritDoc}
     */
    public function generateAssetName($inputs, $filters, $options = array())
    {
        $path = $inputs[0];

        $partPaths = pathinfo($path);

        $name = str_replace($options['root'], '', $path);
        $name = dirname($name);

        return ltrim("{$name}/{$partPaths['filename']}", '/');
    }
}
