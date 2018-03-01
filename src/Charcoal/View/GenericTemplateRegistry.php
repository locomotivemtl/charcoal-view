<?php

namespace Charcoal\View;

/**
 * Generic Template Registry
 */
class GenericTemplateRegistry extends AbstractTemplateRegistry
{
    /**
     * Store of templates.
     *
     * @var string[]
     */
    private $templates = [];

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
     * @return void
     */
    public function set($key, $template)
    {
        $this->templates[$key] = $template;
    }

    /**
     * Retrieve the template by key.
     *
     * @param  string $key The template key.
     * @return string|null The view assigned to the $key or NULL if template is missing.
     */
    public function get($key)
    {
        if (!isset($this->templates[$key])) {
            return null;
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
        return isset($this->templates[$key]);
    }

    /**
     * Remove template from collection by key.
     *
     * @param  string $key The template key.
     * @return void
     */
    public function remove($key)
    {
        if (!isset($this->templates[$key])) {
            return;
        }

        unset($this->templates[$key]);
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
     * @return void
     */
    public function clear()
    {
        $this->templates = [];
    }
}
