<?php

namespace Charcoal\Tests\View\Mustache;

use PHPUnit_Framework_TestCase;

use Charcoal\View\ViewConfig;

class ViewConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MustacheEngine
     */
    private $obj;

    /**
     *
     */
    public function setUp()
    {
        $this->obj = new ViewConfig();
    }

    /**
     *
     */
    public function testDefaults()
    {
        $this->assertEquals('.', $this->obj->separator());
        $this->assertEquals([], $this->obj['paths']);
        $this->assertEquals([], $this->obj['engines.mustache']);
        $this->assertEquals([], $this->obj['engines.php']);
        $this->assertEquals([], $this->obj['engines.php-mustache']);
        $this->assertEquals([], $this->obj['engines.twig']);
        $this->assertEquals('mustache', $this->obj['default_engine']);
    }

    /**
     *
     */
    public function testSetPaths()
    {
        $ret = $this->obj->setPaths(['foo', 'bar']);
        $this->assertSame($ret, $this->obj);

        $this->assertEquals(['foo', 'bar'], $this->obj->paths());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->setPaths([false]);
    }

    /**
     *
     */
    public function testSetEngines()
    {
        $ret = $this->obj->setEngines(['foo'=>[]]);
        $this->assertSame($ret, $this->obj);

        $this->assertEquals(['foo'=>[]], $this->obj->engines());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->addEngine(false, []);
    }

    /**
     *
     */
    public function testEngine()
    {
        $this->assertEquals([], $this->obj->engine('mustache'));

        $this->obj->addEngine('mustache', ['foo'=>'bar']);
        $this->assertEquals(['foo'=>'bar'], $this->obj->engine('mustache'));


        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->engine(false);
    }

    /**
     *
     */
    public function testEngineDefaultEngine()
    {
        $this->obj->addEngine('mustache', ['foo'=>'bar']);
        $this->assertEquals(['foo'=>'bar'], $this->obj->engine());
    }

    /**
     *
     */
    public function testEngineInvalid()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->engine('foobar');
    }

    /**
     *
     */
    public function testSetDefaultEngine()
    {
        $ret = $this->obj->setDefaultEngine('php');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('php', $this->obj->defaultEngine());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->setDefaultEngine(false);
    }
}
