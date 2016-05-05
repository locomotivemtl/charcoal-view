<?php

namespace Charcoal\View;

/**
 * Viewable objects have a view, and therefore can be rendered.
 */
interface ViewableInterface
{

    /**
     * @return string
     */
    public function __toString();

    /**
     * Set the type of view engine to use for this view.
     *
     * @param string $engineIdent The rendering engine (identifier).
     * @return ViewableInterface Chainable
     */
    public function setTemplateEngine($engineIdent);

    /**
     * @return string The template engine (`mustache`, `php`, `php-mustache` or `twig`)
     */
    public function templateEngine();

    /**
     * @param string $ident The template ident for this viewable object.
     * @return ViewableInterface Chainable
     */
    public function setTemplateIdent($ident);

    /**
     * @return string
     */
    public function templateIdent();

    /**
     * @param ViewInterface $view The view instance to use to render.
     * @return ViewableInterface Chainable
     */
    public function setView(ViewInterface $view);

    /**
     * @return ViewInterface The object's View instance.
     */
    public function view();

    /**
     * @param string $template The template to parse and render. If null, use the object's default.
     * @return string The rendered template.
     */
    public function render($template = null);

    /**
     * @param string $templateIdent The template ident to load and render.
     * @return string The rendered template.
     */
    public function renderTemplate($templateIdent);
}
