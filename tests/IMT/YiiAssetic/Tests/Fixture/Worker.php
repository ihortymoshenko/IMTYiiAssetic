<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\YiiAssetic\Tests\Fixture;

/**
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class Worker implements \Assetic\Factory\Worker\WorkerInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(\Assetic\Asset\AssetInterface $asset, \Assetic\Factory\AssetFactory $factory)
    {
    }
}
