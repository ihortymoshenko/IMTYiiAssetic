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
class FilterFactory implements \IMT\YiiAssetic\FilterFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(array $options = array())
    {
        return new Filter();
    }
}
