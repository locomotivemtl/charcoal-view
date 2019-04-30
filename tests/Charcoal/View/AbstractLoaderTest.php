<?php

namespace Charcoal\Tests\View;

use Exception;
use InvalidArgumentException;

// From PSR-3
use Psr\Log\NullLogger;

// From 'charcoal-view'
use Charcoal\View\AbstractLoader;
use Charcoal\View\GenericTemplateRegistry;
use Charcoal\Tests\AbstractTestCase;

/**
 *
 */
class AbstractLoaderTest extends AbstractTestCase
{
    /**
     * Instance of object under test
     * @var AbstractViewClass $obj
     */
    public $obj;

    /**
     * @return void
     */
    public function setUp()
    {
        $logger = new NullLogger();
        $this->obj = $this->getMockForAbstractClass(AbstractLoader::class, [[
            'logger'    => $logger,
            'base_path' => __DIR__,
            'paths'     => [ 'Mustache/templates' ],
        ]]);
    }

    /**
     * @return void
     */
    public function testInvalidBasePathThrowsException()
    {
        $this->expectException(Exception::class);

        $logger = new NullLogger();
        $loader = $this->getMockForAbstractClass(AbstractLoader::class, [[
            'logger'    => $logger,
            'base_path' => false,
            'paths'     => [ 'Mustache/templates' ],
        ]]);
    }

    /**
     * @return void
     */
    public function testPathsThrowsException()
    {
        $this->expectException('\Exception');

        $logger = new NullLogger();
        $loader = $this->getMockForAbstractClass(AbstractLoader::class, [[
            'logger'    => $logger,
            'base_path' => __DIR__,
            'paths'     => [ false ],
        ]]);
    }

    /**
     * @return void
     */
    public function testSetDynamicTemplate()
    {
        $this->assertNull($this->obj->setDynamicTemplate('dynamic', 'foo'));
        $this->assertEquals('foo', $this->obj->dynamicTemplate('dynamic'));
    }

    /**
     * @return void
     */
    public function testClearDynamicTemplate()
    {
        $this->obj->clearDynamicTemplates();
        $this->assertInstanceOf(AbstractLoader::class, $this->obj);
    }

    /**
     * @return void
     */
    public function testLegacyDynamicTemplate()
    {
        $this->obj->setDynamicTemplate('widget_template', 'foo');
        $this->assertEquals('foo', $GLOBALS['widget_template']);

        $this->obj->removeDynamicTemplate('widget_template');
        $this->assertNull($GLOBALS['widget_template']);
    }
}
