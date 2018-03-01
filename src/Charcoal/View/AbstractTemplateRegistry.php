<?php

namespace Charcoal\View;

use InvalidArgumentException;

/**
 * Base Template Registry
 */
abstract class AbstractTemplateRegistry implements
    LoaderRegistryInterface
{
    /**
     * Alias of {@see LoaderRegistryInterface::has()}.
     *
     * @param  string $key The template key.
     * @return boolean
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Alias of {@see LoaderRegistryInterface::get()}.
     *
     * @param  string $key The template key.
     * @return string|null The view assigned to the $key or NULL if template is missing.
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Alias of {@see LoaderRegistryInterface::set()}.
     *
     * @param  string $key      The template key.
     * @param  mixed  $template The template view.
     * @return void
     */
    public function offsetSet($key, $template)
    {
        $this->set($key, $template);
    }

    /**
     * Alias of {@see LoaderRegistryInterface::remove()}.
     *
     * @param  string $key The template key.
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }
}
