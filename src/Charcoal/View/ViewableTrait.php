<?php

namespace Charcoal\View;

// Dependencies from `PHP`
use \InvalidArgumentException;

// Local namespace dependencies
use \Charcoal\View\AbstractView;
use \Charcoal\View\ViewInterface;

/**
* A default (abstract) implementation, as trait, of the ViewableInterface.
*
* There is one additional abstract method: `create_view()`
*
*/
trait ViewableTrait
{
    /**
     * @var string $templateEngine
     */
    private $templateEngine;

    /**
     * @var string $templateIdent
     */
    private $templateIdent;

    /**
     * @var ViewInterface $view
     */
    private $view;

    /**
     * Viewable objects can be rendered with `print` or `echo`
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param string $engine
     * @throws InvalidArgumentException
     * @return ViewableTrait Chainable
     */
    public function setTemplateEngine($engine)
    {
        if (!is_string($engine)) {
            throw new InvalidArgumentException(
                'Template engine must be a string.'
            );
        }
        $this->templateEngine = $engine;
        return $this;
    }

    /**
     * Return the view engine type (identifier).
     *
     * Can be "mustache", "php", "php-mustache" or "twig".
     *
     * @return string
     */
    public function templateEngine()
    {
        if ($this->templateEngine === null) {
            $this->templateEngine = AbstractView::DEFAULT_ENGINE;
        }
        return $this->templateEngine;
    }

    /**
     * @param string $ident
     * @throws InvalidArgumentException
     * @return ViewableTrait Chainable
     */
    public function setTemplateIdent($ident)
    {
        if (!is_string($ident)) {
            throw new InvalidArgumentException(
                'Template ident must be a string.'
            );
        }
        $this->templateIdent = $ident;
        return $this;
    }

    /**
     * @return string
     */
    public function templateIdent()
    {
        return $this->templateIdent;
    }

    /**
     * @param ViewInterface|array $view
     * @throws InvalidArgumentException If the view parameter is not an array or a View object.
     * @return ViewableInterface Chainable
     */
    public function setView(ViewInterface $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @return ViewInterface The object's View.
     */
    public function view()
    {
        return $this->view;
    }

    /**
     * @param string $template The template to parse and render. If null, use the object's default.
     * @return string The rendered template.
     */
    public function render($templateIdent = null)
    {
        if ($templateIdent === null) {
            $templateIdent = $this->templateIdent();
        }
        return $this->view()->render($templateIdent, $this->viewController());
    }

    /**
     * @param string $templateIdent The template ident to load and render.
     * @return string The rendered template.
     */
    public function renderTemplate($templateString = null)
    {

        return $this->view()->renderTemplate($templateString, $this->viewController());
    }

    /**
     * Retrieve a ViewableInterface instance for the template's context.
     *
     * @return mixed
     */
    public function viewController()
    {
        return $this;
    }
}
