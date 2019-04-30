<?php

namespace Charcoal\View;

use InvalidArgumentException;

// From PSR-3
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

// From 'charcoal-view'
use Charcoal\View\GenericTemplateRegistry;
use Charcoal\View\LoaderInterface;
use Charcoal\View\LoaderRegistryInterface;

/**
 * Base Template Loader
 *
 * Finds a template file in a collection of directory paths.
 */
abstract class AbstractLoader implements
    LoggerAwareInterface,
    LoaderInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $basePath = '';

    /**
     * @var string[]
     */
    private $paths = [];

    /**
     * @var boolean
     */
    private $silent = true;

    /**
     * @var LoaderRegistryInterface
     */
    private $templateRegistry;

    /**
     * Default constructor, if none is provided by the concrete class implementations.
     *
     * ## Required dependencies
     * - `logger` A PSR-3 logger
     *
     * @param array $data The class dependencies map.
     */
    public function __construct(array $data = null)
    {
        $this->setLogger($data['logger']);
        $this->setBasePath($data['base_path']);
        $this->setPaths($data['paths']);

        if (!isset($data['registry'])) {
            $data['registry'] = new GenericTemplateRegistry();
        }

        $this->setTemplateRegistry($data['registry']);

        if (isset($data['silent'])) {
            $this->setSilent($data['silent']);
        }
    }

    /**
     * Load a template content
     *
     * @param  string $ident The template ident to load and render.
     * @return string
     */
    public function load($ident)
    {
        // Bail early
        if ($ident === null || $ident === '') {
            return (string)$ident;
        }

        // Handle dynamic templates
        $resolved = $this->resolve($ident);
        if ($resolved === null || $resolved === '') {
            return $ident;
        }

        $file = $this->findTemplateFile($resolved);
        if ($file === null || $file === '') {
            return $ident;
        }

        return file_get_contents($file);
    }

    /**
     * Converts a dynamic template identifer into a template path.
     *
     * @param  string $ident The template key.
     * @throws InvalidArgumentException If the legacy dynamic template is empty.
     * @return string|null The template path or the template key.
     */
    public function resolve($ident)
    {
        if ($this->templateRegistry === null) {
            return $ident;
        }

        if (substr($ident, 0, 1) !== '$') {
            return $ident;
        }

        /** @deprecated 0.3 */
        if ($ident === '$widget_template') {
            if (empty($GLOBALS['widget_template'])) {
                if ($this->silent() === false) {
                    throw new InvalidArgumentException(
                        sprintf('Dynamic template "%s" does not contain a view.', $ident)
                    );
                }

                $this->logger->warning(sprintf(
                    '%s is deprecated in favor of %s::setDynamicTemplate()',
                    '$GLOBALS[\'widget_template\']',
                    get_called_class()
                ));

                return '';
            }

            $ident = $GLOBALS['widget_template'];

            $this->logger->warning(sprintf(
                '%s ("%s") is deprecated in favor of %s::setDynamicTemplate()',
                '$GLOBALS[\'widget_template\']',
                $ident,
                get_called_class()
            ));

            return $ident;
        }

        return $this->templateRegistry()->get(substr($ident, 1));
    }

    /**
     * @param  string $varName The name of the variable to get template ident from.
     * @return string|null Returns the template ident or NULL if no template found.
     */
    public function dynamicTemplate($varName)
    {
        return $this->templateRegistry()->get($varName);
    }

    /**
     * @param  string $varName The name of the variable to get template ident from.
     * @return boolean
     */
    public function hasDynamicTemplate($varName)
    {
        return $this->templateRegistry()->has($varName);
    }

    /**
     * @param  string      $varName       The name of the variable to set this template unto.
     * @param  string|null $templateIdent The "dynamic template" to set or NULL to clear.
     * @return void
     */
    public function setDynamicTemplate($varName, $templateIdent)
    {
        if ($templateIdent === null) {
            $this->removeDynamicTemplate($varName);
            return;
        }

        /** @deprecated 0.3 */
        if ($varName === 'widget_template') {
            $GLOBALS['widget_template'] = $templateIdent;
        }

        $this->templateRegistry()->set($varName, $templateIdent);
    }

    /**
     * @param  string $varName The name of the variable to remove.
     * @return void
     */
    public function removeDynamicTemplate($varName)
    {
        /** @deprecated 0.3 */
        if ($varName === 'widget_template') {
            $GLOBALS['widget_template'] = null;
        }

        $this->templateRegistry()->remove($varName);
    }

    /**
     * @return void
     */
    public function clearDynamicTemplates()
    {
        /** @deprecated 0.3 */
        $GLOBALS['widget_template'] = null;

        $this->templateRegistry()->clear();
    }

    /**
     * @return string
     */
    protected function basePath()
    {
        return $this->basePath;
    }

    /**
     * @param  string $basePath The base path to set.
     * @throws InvalidArgumentException If the base path parameter is not a string.
     * @return LoaderInterface Chainable
     */
    private function setBasePath($basePath)
    {
        if (!is_string($basePath)) {
            throw new InvalidArgumentException(
                'Base path must be a string'
            );
        }
        $basePath = realpath($basePath);
        $this->basePath = rtrim($basePath, '/\\').DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * @return string[]
     */
    protected function paths()
    {
        return $this->paths;
    }

    /**
     * @param  string[] $paths The list of path to add.
     * @return LoaderInterface Chainable
     */
    private function setPaths(array $paths)
    {
        $this->paths = [];

        foreach ($paths as $path) {
            $this->addPath($path);
        }

        return $this;
    }

    /**
     * @param  string $path The path to add to the load.
     * @return LoaderInterface Chainable
     */
    private function addPath($path)
    {
        $this->paths[] = $this->resolvePath($path);

        return $this;
    }

    /**
     * @param  string $path The path to resolve.
     * @throws InvalidArgumentException If the path argument is not a string.
     * @return string
     */
    private function resolvePath($path)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException(
                'Path needs to be a string'
            );
        }

        $basePath = $this->basePath();
        $path = rtrim($path, '/\\').DIRECTORY_SEPARATOR;
        if ($basePath && strpos($path, $basePath) === false) {
            $path = $basePath.$path;
        }

        return $path;
    }

    /**
     * Get error reporting.
     *
     * @return boolean Whether to be quiet when an error occurs.
     */
    public function silent()
    {
        return $this->silent;
    }

    /**
     * Set error reporting.
     *
     * @param  boolean $silent Whether to be quiet when an error occurs.
     * @return void
     */
    private function setSilent($silent)
    {
        $this->silent = (bool)$silent;
    }

    /**
     * @return LoaderRegistryInterface
     */
    public function templateRegistry()
    {
        return $this->templateRegistry;
    }

    /**
     * @param  LoaderRegistryInterface $registry A loader registry instance.
     * @return void
     */
    private function setTemplateRegistry(LoaderRegistryInterface $registry)
    {
        $this->templateRegistry = $registry;
    }

    /**
     * Get the template file (full path + filename) to load from an ident.
     *
     * This method first generates the filename for an identifier and search for it in all of the loader's paths.
     *
     * @param  string $ident The template identifier to load.
     * @throws InvalidArgumentException If the template ident is not a string.
     * @return string|null The full path + filename of the found template. Null if nothing was found.
     */
    protected function findTemplateFile($ident)
    {
        if (!is_string($ident)) {
            throw new InvalidArgumentException(sprintf(
                'Template ident must be a string, received %s',
                is_object($ident) ? get_class($ident) : gettype($ident)
            ));
        }

        $filename = $this->filenameFromIdent($ident);
        $searchPath = $this->paths();
        foreach ($searchPath as $path) {
            $f = realpath($path).'/'.strtolower($filename);
            if (file_exists($f)) {
                return $f;
            }
        }

        return null;
    }

    /**
     * @param  string $ident The template identifier to convert to a filename.
     * @return string
     */
    abstract protected function filenameFromIdent($ident);

    /**
     * Handle when a template is not found.
     *
     * @param  string $ident The template ident.
     * @throws \Exception If the template is not found.
     * @return string
     */
    abstract protected function handleNotFound($ident);
}
