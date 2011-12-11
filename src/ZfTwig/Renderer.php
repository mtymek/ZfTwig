<?php

namespace ZfTwig;

use Zend\View\Renderer as ZendViewRenderer,
    Zend\View\TemplateResolver,
    Zend\View\HelperBroker,
    Zend\Filter\FilterChain,
    InvalidArgumentException;


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
     * Helper broker
     *
     * @var HelperBroker
     */
    protected $helperBroker;

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
     * Retrieve template name or template resolver
     *
     * @param  null|string $name
     * @return string|TemplateResolver
     */
    public function resolver($name = null)
    {
        if (null === $this->templateResolver) {
            $this->setResolver(new TemplatePathStack());
        }

        if (null !== $name) {
            return $this->templateResolver->getScriptPath($name);
        }

        return $this->templateResolver;
    }

    /**
     * Set script resolver
     *
     * @param  string|TemplateResolver $resolver
     * @param  mixed $options
     * @return PhpRenderer
     * @throws InvalidArgumentException
     */
    public function setResolver($resolver, $options = null)
    {
        if (is_string($resolver)) {
            if (!class_exists($resolver)) {
                throw new InvalidArgumentException('Class passed as resolver could not be found');
            }
            $resolver = new $resolver($options);
        }
        if (!$resolver instanceof TemplateResolver) {
            throw new InvalidArgumentException(sprintf(
                'Expected resolver to implement TemplateResolver; received "%s"',
                (is_object($resolver) ? get_class($resolver) : gettype($resolver))
            ));
        }
        $this->templateResolver = $resolver;
        return $this;
    }

    /**
     * @return ZfTwig\Environment
     */
    public function environment()
    {
        if (null === $this->environment) {
            $loader = new TemplateLoader($this->resolver());
            $this->environment = new TwigEnvironment($this, $loader, array(
                'cache' => __DIR__ . '/../../../../data/cache',
                'auto_reload' => true,
                'debug' => true
            ));
        }
        return $this->environment;
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
        $content = $this->environment()->render($name, (array)$vars);
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
        return $this->getBroker()->load($name, $options);
    }

    /**
     * Set plugin broker instance
     *
     * @param  string|HelperBroker $broker
     * @return Zend\View\Abstract
     * @throws InvalidArgumentException
     */
    public function setBroker($broker)
    {
        if (is_string($broker)) {
            if (!class_exists($broker)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid helper broker class provided (%s)',
                    $broker
                ));
            }
            $broker = new $broker();
        }
        if (!$broker instanceof HelperBroker) {
            throw new InvalidArgumentException(sprintf(
                'Helper broker must extend Zend\View\HelperBroker; got type "%s" instead',
                (is_object($broker) ? get_class($broker) : gettype($broker))
            ));
        }
        $broker->setView($this);
        $this->helperBroker = $broker;
    }


    /**
     * Get plugin broker instance
     *
     * @return HelperBroker
     */
    public function getBroker()
    {
        if (null === $this->helperBroker) {
            $this->setBroker(new HelperBroker());
        }
        return $this->helperBroker;
    }
}