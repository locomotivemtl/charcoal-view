<?php

namespace Charcoal\View\Php;

use Exception;

// From 'charcoal-view'
use Charcoal\View\AbstractLoader;
use Charcoal\View\LoaderInterface;

/**
 * PHP Template Loader Adaptor
 *
 * Finds a PHP template file in a collection of directory paths.
 */
class PhpLoader extends AbstractLoader implements LoaderInterface
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
            throw new Exception(sprintf('Unknown template: "%s"', $ident));
        }

        $this->logger->notice(
            sprintf('Unable to find template "%s"', $ident)
        );

        return '';
    }

    /**
     * Convert an identifier to a file path.
     *
     * @param string $ident The identifier to convert.
     * @return string
     */
    protected function filenameFromIdent($ident)
    {
        $filename = str_replace([ '\\' ], '.', $ident);
        $filename .= '.php';

        return $filename;
    }
}
