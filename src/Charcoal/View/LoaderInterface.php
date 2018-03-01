<?php

namespace Charcoal\View;

/**
 * Interface LoaderInterface
 */
interface LoaderInterface
{
    /**
     * @param  string $ident The template to load.
     * @return string
     */
    public function load($ident);
}
