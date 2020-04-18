<?php

namespace Charcoal\View\Php;

// From 'charcoal-view'
use Charcoal\View\AbstractEngine;
use Charcoal\View\LoaderInterface;

/**
 * PHP view rendering engine
 */
class PhpEngine extends AbstractEngine
{
    /**
     * Build the object with an array of dependencies.
     *
     * ## Required parameters:
     * - `loader` a Loader object, to load templates.
     *
     * @param LoaderInterface $loader Engine dependencie.
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->setLoader($loader);
    }
    /**
     * @return string
     */
    public function type()
    {
        return 'php';
    }

    /**
     * @param string $templateString The template string to render.
     * @param mixed  $context        The rendering context.
     * @return string The rendered template string.
     */
    public function renderTemplate($templateString, $context)
    {
        $arrayContext = json_decode(json_encode($context), true);
        // Prevents leaking global variable by forcing anonymous scope
        $render = function($templateString, array $context) {
            extract($context);
            return eval('?>'.$templateString);
        };

        ob_start();
        $render($templateString, $arrayContext);
        $output = ob_get_clean();

        return $output;
    }
}
