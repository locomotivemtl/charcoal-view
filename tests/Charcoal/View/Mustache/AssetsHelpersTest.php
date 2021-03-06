<?php

namespace Charcoal\Tests\View\Mustache;

use StdClass;

// From Mustache
use Mustache_Engine as MustacheEngine;
use Mustache_LambdaHelper as LambdaHelper;
use Mustache_Template as MustacheTemplate;

// From 'charcoal-view'
use Charcoal\View\Mustache\AssetsHelpers;
use Charcoal\Tests\AbstractTestCase;

/**
 *
 */
class AssetsHelpersTest extends AbstractTestCase
{
    /**
     * @var AssetsHelpers
     */
    private $obj;

    /**
     * @var MustacheEngine
     */
    private $mustache;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->obj      = new AssetsHelpers();
        $this->mustache = new MustacheEngine([
            'helpers' => $this->obj->toArray(),
        ]);
    }

    /**
     * @return void
     */
    public function testDefaults()
    {
        $this->assertEquals('', $this->obj->js());
        $this->assertEquals('', $this->obj->css());
        $this->assertEquals('', $this->obj->jsRequirements());
        $this->assertEquals('', $this->obj->cssRequirements());
    }

    /**
     * @return void
     */
    public function testAddJs()
    {
        $this->obj->addJs('<script id="foo">');
        $this->obj->addJs('<script id="baz">');
        $this->assertEquals('<script id="foo"><script id="baz">', $this->obj->js());
        $this->assertEquals('', $this->obj->js());
    }

    /**
     * @return void
     */
    public function testAddJsWithMustache()
    {
        $template = $this->mustache->loadTemplate(
            '{{# addJs }}<script id="{{name}}">{{/ addJs }}'."<<<\n".'{{& js }}'."\n>>>"
        );

        $context = new StdClass();
        $context->name = 'qux';

        $rendered = $template->render($context);

        $this->assertEquals("<<<\n".'<script id="qux">'."\n>>>", $rendered);
    }

    /**
     * @return void
     */
    public function testAddCss()
    {
        $this->obj->addCss('<style id="foo">');
        $this->obj->addCss('<style id="baz">');
        $this->assertEquals('<style id="foo"><style id="baz">', $this->obj->css());
    }

    /**
     * @return void
     */
    public function testAddCssWithMustache()
    {
        $template = $this->mustache->loadTemplate(
            '{{# addCss }}<style id="{{name}}">{{/ addCss }}'."<<<\n".'{{& css }}'."\n>>>"
        );

        $context = new StdClass();
        $context->name = 'qux';

        $rendered = $template->render($context);

        $this->assertEquals("<<<\n".'<style id="qux">'."\n>>>", $rendered);
    }

    /**
     * @return void
     */
    public function testAddJsRequirement()
    {
        // Test enqueue
        $this->obj->addJsRequirement('<script id="foo">');
        $this->obj->addJsRequirement('<script id="baz">');
        // Test uniqueness
        $this->obj->addJsRequirement('<script id="baz">');
        // Assertions
        $this->assertEquals('<script id="foo">'."\n".'<script id="baz">', $this->obj->jsRequirements());
        $this->assertEquals('', $this->obj->jsRequirements());
    }

    /**
     * @return void
     */
    public function testAddJsRequirementWithMustache()
    {
        $template = $this->mustache->loadTemplate(
            '{{# addJsRequirement }}<script id="{{name}}">{{/ addJsRequirement }}'.
            "<<<\n".'{{& jsRequirements }}'."\n>>>"
        );

        $context = new StdClass();
        $context->name = 'qux';

        $rendered = $template->render($context);

        $this->assertEquals("<<<\n".'<script id="qux">'."\n>>>", $rendered);
    }

    /**
     * @return void
     */
    public function testAddCssRequirement()
    {
        // Test enqueue
        $this->obj->addCssRequirement('<style id="foo">');
        $this->obj->addCssRequirement('<style id="baz">');
        // Test uniqueness
        $this->obj->addCssRequirement('<style id="baz">');
        // Assertions
        $this->assertEquals('<style id="foo">'."\n".'<style id="baz">', $this->obj->cssRequirements());
        $this->assertEquals('', $this->obj->cssRequirements());
    }

    /**
     * @return void
     */
    public function testAddCssRequirementWithMustache()
    {
        $template = $this->mustache->loadTemplate(
            '{{# addCssRequirement }}<style id="{{name}}">{{/ addCssRequirement }}'.
            "<<<\n".'{{& cssRequirements }}'."\n>>>"
        );

        $context = new StdClass();
        $context->name = 'qux';

        $rendered = $template->render($context);

        $this->assertEquals("<<<\n".'<style id="qux">'."\n>>>", $rendered);
    }

    /**
     * @return void
     */
    public function testPurgeJs()
    {
        $this->obj->addJs('<script>');
        $this->obj->purgeJs();
        $this->assertEquals('', $this->obj->js());
    }

    /**
     * @return void
     */
    public function testPurgeCss()
    {
        $this->obj->addCss('<style>');
        $this->obj->purgeCss();
        $this->assertEquals('', $this->obj->css());
    }

    /**
     * @return void
     */
    public function testPurgeAssets()
    {
        $this->obj->addCss('<style>');
        $this->obj->addJs('<script>');
        $this->obj->purgeAssets();
        $this->assertEquals('', $this->obj->css());
        $this->assertEquals('', $this->obj->js());
    }

    /**
     * @return void
     */
    public function testPurgeAssetsWithMustache()
    {
        $template = $this->mustache->loadTemplate(
            '{{# addCss }}<style>{{/ addCss }}{{# addJs }}<script>{{/ addJs }}{{ purgeAssets }}'
        );
        $rendered = $template->render();

        $this->assertEquals('', $rendered);
    }

    /**
     * @return void
     */
    public function testPurgeJsWithMustache()
    {
        $template = $this->mustache->loadTemplate(
            '{{# addJs }}<script>{{/ addJs }}{{ purgeJs }}'
        );
        $rendered = $template->render();

        $this->assertEquals('', $rendered);
    }

    /**
     * @return void
     */
    public function testPurgeCssWithMustache()
    {
        $template = $this->mustache->loadTemplate(
            '{{# addCss }}<style>{{/ addCss }}{{ purgeCss }}'
        );
        $rendered = $template->render();

        $this->assertEquals('', $rendered);
    }
}
