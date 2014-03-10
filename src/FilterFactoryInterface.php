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
 * The interface for filter factories
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 * @codeCoverageIgnore
 */
interface FilterFactoryInterface
{
    /**
     * @param  array $options An array of filter options
     * @return \Assetic\Filter\FilterInterface
     */
    public function create(array $options = array());
}
