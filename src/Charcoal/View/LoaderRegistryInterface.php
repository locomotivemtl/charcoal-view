<?php

namespace Charcoal\View;

/**
 * Template Loader Registry Interface
 */
interface LoaderRegistryInterface extends \ArrayAccess
{
    /**
     * Set the template for the given key.
     *
     * @param  string $ident    The template key.
     * @param  string $template The template view.
     * @return void
     */
    public function set($ident, $template);

    /**
     * Retrieve the template by key.
     *
     * @param  string $ident The template key.
     * @return string|null The view assigned to the $ident or NULL if template is missing.
     */
    public function get($ident);

    /**
     * Determine if a template exists in the collection by key.
     *
     * @param  string $ident The template key.
     * @return boolean
     */
    public function has($ident);

    /**
     * Remove template from collection by key.
     *
     * @param  string $ident The template key.
     * @return void
     */
    public function remove($ident);

    /**
     * Add template(s) to collection, replacing existing templates with the same key.
     *
     * @param  array $templates Key-value array of templates to replace to this collection.
     * @return void
     */
    public function replace(array $templates);

    /**
     * Retrieve all registered template keys.
     *
     * @return string[]
     */
    public function registeredTemplates();

    /**
     * Remove all templates from collection.
     *
     * @return void
     */
    public function clear();
}
