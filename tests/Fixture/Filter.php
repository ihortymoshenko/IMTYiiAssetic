<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\YiiAssetic\Fixture;

/**
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class Filter implements \Assetic\Filter\FilterInterface
{
    /**
     * {@inheritDoc}
     */
    public function filterLoad(\Assetic\Asset\AssetInterface $asset)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function filterDump(\Assetic\Asset\AssetInterface $asset)
    {
    }
}
