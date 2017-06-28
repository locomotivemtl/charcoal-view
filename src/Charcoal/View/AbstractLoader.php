<?php

namespace Charcoal\View;

// PHP Dependencies
use InvalidArgumentException;

// PSR-3 (logger) dependencies
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

// Local namespace dependencies
use Charcoal\View\LoaderInterface;

/**
 * Base template loader.
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
     * @var array
     */
    private $dynamicTemplates = [];

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
    }

    /**
     * Load a template content
     *
     * @param  string $ident The template ident to load and render.
     * @throws InvalidArgumentException If the dynamic template identifier is not a string.
     * @return string
     */
    public function load($ident)
    {
        // Handle dynamic template
        if (substr($ident, 0, 1) === '$') {
            $tryLegacy = ($ident === '$widget_template');

            $ident = $this->dynamicTemplate(substr($ident, 1));

            // Legacy dynamic template hack
            if (!$ident && $tryLegacy) {
                $this->logger->warning('The GLOBALS $widget_template hack will be obsolete soon. Use setDynamicTemplate() instead.');
                $ident = (isset($GLOBALS['widget_template']) ? $GLOBALS['widget_template'] : null);
                if (!is_string($ident)) {
                    throw new InvalidArgumentException(
                        'Dynamic template ident (from "$widget_template") must be a string'
                    );
                }
            }
        }

        $file = $this->findTemplateFile($ident);
        if ($file === null || $file === '') {
            return $ident;
        }

        return file_get_contents($file);
    }

    /**
     * @return string
     */
    protected function basePath()
    {
        return $this->basePath;
    }

    /**
     * @see FileLoader::path()
     * @return string[]
     */
    protected function paths()
    {
        return $this->paths;
    }

    /**
     * Get the template file (full path + filename) to load from an ident.
     *
     * This method first generates the filename for an identifier and search for it in all of the loader's paths.
     *
     * @param string $ident The template identifier to load.
     * @throws InvalidArgumentException If the template ident is not a string.
     * @return string|null The full path + filename of the found template. Null if nothing was found.
     */
    protected function findTemplateFile($ident)
    {
        if (!is_string($ident)) {
            throw new InvalidArgumentException(
                'Template ident must be a string'
            );
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
     * @param string $ident The template identifier to convert to a filename.
     * @return string
     */
    abstract protected function filenameFromIdent($ident);

    /**
     * @param string[] $paths The list of path to add.
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
     * @param string $basePath The base path to set.
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
     * @param string $path The path to add to the load.
     * @return LoaderInterface Chainable
     */
    private function addPath($path)
    {
        $this->paths[] = $this->resolvePath($path);

        return $this;
    }

    /**
     * @param string $path The path to resolve.
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
     * @param string $varName       The name of the variable to set this template unto.
     * @param string $templateIdent The "dynamic template" to set. null to clear.
     * @throws InvalidArgumentException If var name is not a string or if the template is not a string (and not null).
     * @return void
     */
    public function setDynamicTemplate($varName, $templateIdent)
    {
        if (!is_string($varName)) {
            throw new InvalidArgumentException(
                'Can not set dynamic template: var name is not a string.'
            );
        }

        if (!is_string($templateIdent)) {
            throw new InvalidArgumentException(
                'Can not set dynamic template. Must be a a string, or null.'
            );
        }

        $this->dynamicTemplates[$varName] = $templateIdent;
    }

    /**
     * @param string $varName The name of the variable to get template ident from.
     * @throws InvalidArgumentException If the var name is not a string.
     * @return string
     */
    public function dynamicTemplate($varName)
    {
        if (!is_string($varName)) {
            throw new InvalidArgumentException(
                'Can not get dynamic template: var name is not a string.'
            );
        }

        if (!isset($this->dynamicTemplates[$varName])) {
            return '';
        }

        return $this->dynamicTemplates[$varName];
    }
}
