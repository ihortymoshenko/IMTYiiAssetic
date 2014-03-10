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

/**
 * The interface for the filter initializer
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 * @codeCoverageIgnore
 */
interface FilterInitializerInterface
{
    /**
     * @param array $filters An array of filter names
     */
    public function initialize(array $filters);
}
