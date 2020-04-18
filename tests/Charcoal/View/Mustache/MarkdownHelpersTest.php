<?php

namespace Charcoal\Tests\View\Mustache;

// From Mustache
use Charcoal\View\Mustache\MustacheLoader;
use Charcoal\View\Mustache\MustacheEngine;

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
    public function setUp()
    {
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        $this->obj = new MarkdownHelpers($parsedown);

        $loader = new MustacheLoader(__DIR__, [ 'templates' ]);
        $this->mustache = new MustacheEngine($loader, null, $this->obj->toArray());
    }

    /**
     * @return void
     */
    public function testMarkdown()
    {
        $template = $this->mustache->renderTemplate(
            '{{# markdown }}**test**{{/ markdown }}',
            []
        );

        $this->assertContains('<strong>test</strong>', $template);
    }
}
