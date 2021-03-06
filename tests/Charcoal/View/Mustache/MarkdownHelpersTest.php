<?php

namespace Charcoal\Tests\View\Mustache;

// From Mustache
use Mustache_Engine as MustacheEngine;

// From 'erusev/parsedown'
use Parsedown;

// From 'charcoal-view'
use Charcoal\View\Mustache\MarkdownHelpers;
use Charcoal\Tests\AbstractTestCase;

/**
 *
 */
class MarkdownHelpersTest extends AbstractTestCase
{
    /**
     * @var MarkdownHelpers
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
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        $this->obj = new MarkdownHelpers([
            'parsedown' => $parsedown,
        ]);
        $this->mustache = new MustacheEngine([
            'helpers' => $this->obj->toArray(),
        ]);
    }

    /**
     * @return void
     */
    public function testMarkdown()
    {
        $template = $this->mustache->loadTemplate(
            '{{# markdown }}**test**{{/ markdown }}'
        );

        $ret = $template->render();
        $this->assertStringContainsString('<strong>test</strong>', $ret);
    }
}
