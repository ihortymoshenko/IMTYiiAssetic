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

use Assetic\Filter\FilterInterface;
use Assetic\FilterManager;

use IMT\YiiAssetic\Exception\RuntimeException;

/**
 * This class represents the filter initializer
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class FilterInitializer implements FilterInitializerInterface
{
    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * An array of key-value pairs: the key is the alias, and the value is an
     * array of filter options
     *
     * @var array
     */
    protected $filters;

    /**
     * @param FilterManager $filterManager
     * @param array         $coreFilters   An array of built-in filters
     * @param array         $userFilters   An array of user-defined filters
     */
    public function __construct(FilterManager $filterManager, array $coreFilters, array $userFilters = array())
    {
        $this->filterManager = $filterManager;
        $this->filters       = $this->combineFilters($coreFilters, $userFilters);
    }

    /**
     * {@inheritDoc}
     */
    public function initialize(array $filters)
    {
        foreach ($filters as $filter) {
            if (!$this->filterManager->has($filter)) {
                $this->filterManager->set($filter, $this->createFilter($filter));
            }
        }
    }

    /**
     * Combines an array of built-in filters and an array of user-defined
     * filters into one array of filters
     *
     * @param  array $coreFilters An array of built-in filters
     * @param  array $userFilters An array of user-defined filters
     * @return array
     */
    protected function combineFilters(array $coreFilters, array $userFilters)
    {
        return array_merge($this->mapFilters($coreFilters), $this->mapFilters($userFilters));
    }

    /**
     * @param  string           $alias
     * @return FilterInterface
     * @throws RuntimeException        If the filter does not exist
     * @throws RuntimeException        If the filter factory is invalid
     * @throws RuntimeException        If the filter configuration is invalid
     */
    protected function createFilter($alias)
    {
        if (!isset($this->filters[$alias])) {
            throw new RuntimeException("The `$alias` filter does not exist.");
        } elseif (isset($this->filters[$alias]['class'])) {
            return new $this->filters[$alias]['class']();
        } elseif (isset($this->filters[$alias]['factoryClass'])) {
            $filterFactory = new $this->filters[$alias]['factoryClass']();

            if (!$filterFactory instanceof FilterFactoryInterface) {
                throw new RuntimeException(
                    sprintf(
                        'The filter factory should implement the `%s` interface.',
                        __NAMESPACE__ . '\FilterFactoryInterface'
                    )
                );
            }

            return $filterFactory->create($this->filters[$alias]);
        }

        throw new RuntimeException("The `$alias` filter configuration is invalid.");
    }

    /**
     * @param  array $filters
     * @return array
     */
    protected function mapFilters(array $filters)
    {
        return array_map(
            function ($v) {
                return !is_string($v) ? $v : array('class' => $v);
            },
            $filters
        );
    }
}
