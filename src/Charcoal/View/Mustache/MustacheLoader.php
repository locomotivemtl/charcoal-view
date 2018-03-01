<?php

namespace Charcoal\View\Mustache;

use Exception;

// From Mustache
use Mustache_Loader as MustacheLoaderInterface;
use Mustache_Exception_UnknownTemplateException as UnknownTemplateException;

// From 'charcoal-view'
use Charcoal\View\AbstractLoader;
use Charcoal\View\LoaderInterface;

/**
 * Mustache Template Loader Adaptor
 *
 * Finds a Mustache template file in a collection of directory paths.
 */
class MustacheLoader extends AbstractLoader implements
    LoaderInterface,
    MustacheLoaderInterface
{
    /**
     * Handle when a template is not found.
     *
     * @param  string $ident The template ident.
     * @throws Exception If the template is not found.
     * @return string
     */
    public function handleNotFound($ident)
    {
        if ($this->silent() === false) {
            $e = new UnknownTemplateException($ident);
            throw new Exception(sprintf('Unknown template: "%s"', $ident), 0, $e);
        }

        $this->logger->notice(
            sprintf('Unable to find template "%s"', $ident)
        );

        return '';
    }

    /**
     * Convert an identifier to a file path.
     *
     * @param  string $ident The template identifier to convert to a filename.
     * @return string
     */
    protected function filenameFromIdent($ident)
    {
        $filename  = str_replace([ '\\' ], '.', $ident);
        $filename .= '.mustache';

        return $filename;
    }
}
