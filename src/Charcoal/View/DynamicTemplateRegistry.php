<?php

namespace Charcoal\View;

use InvalidArgumentException;

/**
 * Dynamic Template Registry
 */
class DynamicTemplateRegistry extends AbstractTemplateRegistry
{
    /**
     * Store of registered template keys.
     *
     * @var boolean[]
     */
    private $registered = [];

    /**
     * Store of template views.
     *
     * @var boolean[]
     */
    private $templates = [];

    /**
     * Store of protected template keys.
     *
     * @var boolean[]
     */
    private $protected = [];

    /**
     * Store of single-use templates.
     *
     * @var {string|boolean}[]
     */
    private $once = [];

    /**
     * Create new dynamic template collection
     *
     * @param array $templates Pre-populate collection with these templates.
     */
    public function __construct(array $templates = [])
    {
        $this->replace($templates);
    }

    /**
     * Set the template for the given key.
     *
     * @param  string $key      The template key.
     * @param  string $template The template view.
     * @throws InvalidArgumentException If the template is protected.
     * @return void
     */
    public function set($key, $template)
    {
        if (isset($this->protected[$key])) {
            if (isset($this->templates[$key]) || isset($this->once[$key])) {
                throw new InvalidArgumentException(
                    sprintf('Cannot remove protected dynamic template "%s".', $key)
                );
            }
        }

        $this->templates[$key]  = $template;
        $this->registered[$key] = true;
    }

    /**
     * Retrieve the template by key.
     *
     * @param  string $key The template key.
     * @return string|null The view assigned to the $key or NULL if template is missing.
     */
    public function get($key)
    {
        if (!isset($this->registered[$key])) {
            return null;
        }

        if (isset($this->once[$key])) {
            $template = $this->once[$key];
            if ($template !== true) {
                if (isset($this->protected[$key])) {
                    $this->once[$key] = true;
                } elseif (isset($this->templates[$key])) {
                    unset($this->once[$key]);
                } else {
                    unset($this->registered[$key], $this->once[$key]);
                }

                return $template;
            } else {
                return null;
            }
        }

        return $this->templates[$key];
    }

    /**
     * Determine if a template exists in the collection by key.
     *
     * @param  string $key The template key.
     * @return boolean
     */
    public function has($key)
    {
        return isset($this->registered[$key]);
    }

    /**
     * Remove template from collection by key.
     *
     * @param  string  $key   The template key.
     * @param  boolean $force To remove protected templates.
     * @throws InvalidArgumentException If the template is protected.
     * @return void
     */
    public function remove($key, $force = false)
    {
        if (!isset($this->registered[$key])) {
            return;
        }

        if ($force === true) {
            unset($this->registered[$key], $this->templates[$key], $this->once[$key], $this->protected[$key]);
            return;
        }

        if (isset($this->protected[$key])) {
            throw new InvalidArgumentException(
                sprintf('Cannot remove protected dynamic template "%s".', $key)
            );
        }

        unset($this->registered[$key], $this->templates[$key], $this->once[$key]);
    }

    /**
     * Protects the template from being replaced or removed.
     *
     * Note: The template view can be assigned after enabling protection.
     *
     * @param  string      $key      The template key to protect from being changed.
     * @param  string|null $template Optional. The template view to protect.
     * @return void
     */
    public function protect($key, $template = null)
    {
        $this->protected[$key] = true;

        if ($template !== null) {
            $this->set($key, $template);
        }
    }

    /**
     * Set the template for the given key to be is used at most once.
     *
     * The `once()` method is identical to `set()`, except that the template
     * is unbound after its first retrieval.
     *
     * Note: If the template is protected, the template cannot be rebound.
     *
     * @param  string      $key      The template key to limit.
     * @param  string|null $template Optional. The template view to limit.
     * @throws InvalidArgumentException If the template is protected.
     * @return void
     */
    public function once($key, $template = null)
    {
        if (isset($this->protected[$key]) && isset($this->once[$key])) {
            throw new InvalidArgumentException(
                sprintf('Cannot remove protected dynamic template "%s".', $key)
            );
        }

        $this->once[$key]       = $template;
        $this->registered[$key] = true;
    }

    /**
     * Add template(s) to collection, replacing existing templates with the same key.
     *
     * @param  array $templates Key-value array of templates to replace to this collection.
     * @return void
     */
    public function replace(array $templates)
    {
        foreach ($templates as $key => $template) {
            $this->set($key, $template);
        }
    }

    /**
     * Retrieve all registered template keys.
     *
     * @return string[]
     */
    public function registeredTemplates()
    {
        return array_keys($this->templates);
    }

    /**
     * Remove all templates from collection.
     *
     * @param  boolean $force If TRUE, protected templates are also removed.
     * @return void
     */
    public function clear($force = false)
    {
        if ($force === true) {
            $this->registered = [];
            $this->protected  = [];
            $this->templates  = [];
            $this->once       = [];
        } else {
            $this->registered = array_intersect_key($this->registered, $this->protected);
            $this->templates  = array_intersect_key($this->templates, $this->protected);
            $this->once       = array_intersect_key($this->once, $this->protected);
        }
    }
}
