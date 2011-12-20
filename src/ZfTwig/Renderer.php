<?php

namespace ZfTwig;

use Zend\View\Renderer as ZendViewRenderer,
    Zend\Filter\FilterChain;

class Renderer implements ZendViewRenderer
{
    /**
     * @var ZfTwig\Environment
     */
    protected $environment;

    /**
     * @var Zend\Filter\FilterChain
     */
    protected $filterChain;

    /**
     * Set filter chain
     *
     * @param \Zend\Filter\FilterChain $filters
     * @return Zend\View\PhpRenderer
     */
    public function setFilterChain(FilterChain $filters)
    {
        $this->filterChain = $filters;
        return $this;
    }

    /**
     * Retrieve filter chain for post-filtering script content
     *
     * @return FilterChain
     */
    public function getFilterChain()
    {
        if (null === $this->filterChain) {
            $this->setFilterChain(new FilterChain());
        }
        return $this->filterChain;
    }

    /**
     * @param TwigEnvironment $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return TwigEnvironment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    public function __construct(TwigEnvironment $environment)
    {
        $this->setEnvironment($environment);
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script name to process.
     * @param array|null $vars
     * @return string The script output.
     */
    public function render($name, $vars = null)
    {
        // Ideally this would be set by Zend\Di...
        $this->getEnvironment()->getBroker()->setView($this);

        $content = $this->getEnvironment()->render($name, (array)$vars);
        return $this->getFilterChain()->filter($content); // filter output
    }

    /**
     * Return the template engine object, if any
     *
     * If using a third-party template engine, such as Smarty, patTemplate,
     * phplib, etc, return the template engine object. Useful for calling
     * methods on these objects, such as for setting filters, modifiers, etc.
     *
     * @return mixed
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Get plugin instance
     *
     * @param string $name Name of plugin to return
     * @param null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return Helper
     */
    public function plugin($name, array $options = null)
    {
        return $this->getEnvironment()->getBroker()->load($name, $options);
    }
}