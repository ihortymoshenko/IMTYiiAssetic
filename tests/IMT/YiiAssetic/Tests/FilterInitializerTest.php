<?php

/*
 * This file is part of the IMTYiiAssetic package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\YiiAssetic\Tests;

use Assetic\FilterManager;

use IMT\YiiAssetic\FilterInitializer;

/**
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class FilterInitializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->filterManager = new FilterManager();
    }

    public function testConstructor()
    {
        $filterInitializer = new FilterInitializer(
            $this->filterManager,
            array(
                'filter1' => 'class1',
                'filter2' => 'class2',
            ),
            array(
                'filter2' => 'class3',
                'filter3' => 'class4'
            )
        );

        $this->assertAttributeEquals(
            array(
                'filter1' => array('class' => 'class1'),
                'filter2' => array('class' => 'class3'),
                'filter3' => array('class' => 'class4'),
            ),
            'filters',
            $filterInitializer
        );
    }

    public function testInitializeFilterNotExists()
    {
        $filterInitializer = new FilterInitializer($this->filterManager, array());

        $this->setExpectedException(
            'IMT\YiiAssetic\Exception\RuntimeException',
            'The `smth` filter does not exist.'
        );

        $filterInitializer->initialize(array('smth'));
    }

    public function testInitializeFilterFactoryNotInvalid()
    {
        $filterInitializer = new FilterInitializer(
            $this->filterManager,
            array(),
            array('smth' => array('factoryClass' => 'stdClass'))
        );

        $this->setExpectedException(
            'IMT\YiiAssetic\Exception\RuntimeException',
            'The filter factory should implement the `IMT\YiiAssetic\FilterFactoryInterface` interface.'
        );

        $filterInitializer->initialize(array('smth'));
    }

    public function testInitializeFilterConfigurationNotValid()
    {
        $filterInitializer = new FilterInitializer(
            $this->filterManager,
            array(),
            array('smth' => array())
        );

        $this->setExpectedException(
            'IMT\YiiAssetic\Exception\RuntimeException',
            'The `smth` filter configuration is invalid.'
        );

        $filterInitializer->initialize(array('smth'));
    }

    public function testInitializeFilterFactory()
    {
        $filterInitializer = new FilterInitializer(
            $this->filterManager,
            array(),
            array('smth' => array('factoryClass' => __NAMESPACE__ . '\Fixture\FilterFactory'))
        );

        $filterInitializer->initialize(array('smth'));

        $this->assertCount(1, $this->filterManager->getNames());
        $this->assertContains('smth', $this->filterManager->getNames());
    }

    public function testInitializeFilterClass()
    {
        $filterInitializer = new FilterInitializer(
            $this->filterManager,
            array('smth' => __NAMESPACE__ . '\Fixture\Filter')
        );

        $filterInitializer->initialize(array('smth'));

        $this->assertCount(1, $this->filterManager->getNames());
        $this->assertContains('smth', $this->filterManager->getNames());
    }
}
