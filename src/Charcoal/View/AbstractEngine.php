<?php

namespace Charcoal\View;

// From PSR-3
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

// From 'charcoal-view'
use Charcoal\View\EngineInterface;
use Charcoal\View\LoaderInterface;

/**
 * Default implementation, as abstract class, of the `EngineInterface`.
 *
 * View Engines are comprised of 2 things:
 * - A template loader, wich is a `LoaderInterfaceObject`
 *   - Set with `set_loader()` / Get with `loader()`
 *   - Provides `loadtemplate()` method
 * - A `render()` method, which takes a $template and a $context arguments
 *
 * > Engines implements the `LoggerAwareInterface`. A logger can be accessed with the `logger()` method.
 */
abstract class AbstractEngine implements
    EngineInterface,
    LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * The cache option.
     *
     * @var mixed
     */
    private $cache;

    /**
     * Build the object with an array of dependencies.
     *
     * ## Required parameters:
     * - `logger` a PSR-3 logger
     * - `loader` a Loader object, to load templates.
     *
     * @param array $data Engine dependencie.
     */
    public function __construct(array $data)
    {
        $this->setLogger($data['logger']);
        $this->setLoader($data['loader']);

        if (array_key_exists('cache', $data)) {
            $this->setCache($data['cache']);
        }
    }

    /**
     * @return string
     */
    abstract public function type();

    /**
     * Render a template (from ident) with a given context.
     *
     * @param string $templateIdent The template identifier to load and render.
     * @param mixed  $context       The rendering context.
     * @return string The rendered template string.
     */
    public function render($templateIdent, $context)
    {
        $template = $this->loadTemplate($templateIdent);
        return $this->renderTemplate($template, $context);
    }

    /**
     * @param string $templateString The template string to render.
     * @param mixed  $context        The rendering context.
     * @return string The rendered template string.
     */
    abstract public function renderTemplate($templateString, $context);

    /**
     * Delegates template loading to the engine's Loader object.
     *
     * @param string $templateIdent The template identifier to load.
     * @return string The template string, loaded from identifier.
     */
    public function loadTemplate($templateIdent)
    {
        return $this->loader()->load($templateIdent);
    }

    /**
     * @param string $varName The name of the variable to get template ident from.
     * @return string
     */
    public function dynamicTemplate($varName)
    {
        return $this->loader()->dynamicTemplate($varName);
    }

    /**
     * @param string $varName The name of the variable to get template ident from.
     * @return boolean
     */
    public function hasDynamicTemplate($varName)
    {
        return $this->loader()->hasDynamicTemplate($varName);
    }

    /**
     * @param string      $varName       The name of the variable to set this template unto.
     * @param string|null $templateIdent The "dynamic template" to set. null to clear.
     * @return void
     */
    public function setDynamicTemplate($varName, $templateIdent)
    {
        $this->loader()->setDynamicTemplate($varName, $templateIdent);
    }

    /**
     * @return \Charcoal\View\LoaderRegistryInterface
     */
    public function templateRegistry()
    {
        return $this->loader()->templateRegistry();
    }

    /**
     * Set the engine's cache implementation.
     *
     * @param  mixed $cache A engine cache implementation,
     *                      an absolute path to the compiled views,
     *                      a boolean to enable/disable cache.
     * @return void
     */
    protected function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get the engine's cache implementation.
     *
     * @return mixed
     */
    protected function cache()
    {
        return $this->cache;
    }

    /**
     * @param LoaderInterface $loader A loader instance.
     * @return void
     */
    private function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @return LoaderInterface
     */
    protected function loader()
    {
        return $this->loader;
    }
}
