<?php

namespace Charcoal\View\Twig;

// From Twig
use Twig_Environment;

// From 'charcoal-view'
use Charcoal\View\AbstractEngine;
use Charcoal\View\LoaderInterface;

/**
 *
 */
class TwigEngine extends AbstractEngine
{
    const DEFAULT_CACHE_PATH = '../cache/twig';

    /**
     * @var Twig_Environment $twig
     */
    private $twig;

    /**
     * Build the object with an array of dependencies.
     *
     * ## Required parameters:
     * - `loader` a Loader object, to load templates.
     *
     * @param LoaderInterface $loader Twig template loader.
     * @param string|null     $cache  The cache path.
     */
    public function __construct(LoaderInterface $loader, $cache = null)
    {
        $this->setLoader($loader);

        if ($cache !== null) {
            $this->setCache($cache);
        }
    }

    /**
     * @return string
     */
    public function type()
    {
        return 'twig';
    }

    /**
     * @return Twig_Environment
     */
    public function twig()
    {
        if ($this->twig === null) {
            $this->twig = $this->createTwig();
        }
        return $this->twig;
    }

    /**
     * @param string $templateIdent The template identifier to load and render.
     * @param mixed  $context       The rendering context.
     * @return string The rendered template string.
     */
    public function render($templateIdent, $context)
    {
        $arrayContext = json_decode(json_encode($context), true);
        return $this->twig()->render($templateIdent, $arrayContext);
    }

    /**
     * @param string $templateString The template string to render.
     * @param mixed  $context        The rendering context.
     * @return string The rendered template string.
     */
    public function renderTemplate($templateString, $context)
    {
        $template = $this->twig()->createTemplate($templateString);
        $arrayContext = json_decode(json_encode($context), true);
        return $template->render($arrayContext);
    }

    /**
     * @return Twig_Environment
     */
    protected function createTwig()
    {
        $twig = new Twig_Environment($this->loader(), [
            'cache'     => $this->cache(),
            'charset'   => 'utf-8',
            'debug'     => false
        ]);

        return $twig;
    }

    /**
     * Set the engine's cache implementation.
     *
     * @param  mixed $cache A Twig cache option.
     * @return void
     */
    protected function setCache($cache)
    {
        /**
         * If NULL is specified, the value is converted to FALSE
         * because Twig internally requires FALSE to disable the cache.
         */
        if ($cache === null) {
            $cache = false;
        }

        parent::setCache($cache);
    }
}
