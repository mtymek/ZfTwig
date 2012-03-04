<?php

namespace ZfTwig;

use Zend\View\Renderer as ZendViewRenderer,
    Zend\View\Resolver,
    Zend\View\HelperBroker,
    Zend\View\Model,
    Zend\View\Variables,
    Zend\Filter\FilterChain,
    ArrayAccess;

class TwigRenderer implements ZendViewRenderer
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var Zend\Filter\FilterChain
     */
    protected $filterChain;

    /**
     * Template resolver
     *
     * @var Resolver
     */
    private $templateResolver;

    protected $__helperBroker;

    /**
     * @var ArrayAccess|array ArrayAccess or associative array representing available variables
     */
    private $__vars;

    /**
     * Set variable storage
     *
     * Expects either an array, or an object implementing ArrayAccess.
     *
     * @param  array|ArrayAccess $variables
     * @return PhpRenderer
     * @throws Exception\InvalidArgumentException
     */
    public function setVars($variables)
    {
        if (!is_array($variables) && !$variables instanceof ArrayAccess) {
            throw new \InvalidArgumentException(sprintf(
                'Expected array or ArrayAccess object; received "%s"',
                (is_object($variables) ? get_class($variables) : gettype($variables))
            ));
        }

        // Enforce a Variables container
        if (!$variables instanceof Variables) {
            $variablesAsArray = array();
            foreach ($variables as $key => $value) {
                $variablesAsArray[$key] = $value;
            }
            $variables = new Variables($variablesAsArray);
        }

        $this->__vars = $variables;
        return $this;
    }

    /**
     * Get a single variable, or all variables
     *
     * @param  mixed $key
     * @return mixed
     */
    public function vars($key = null)
    {
        if (null === $this->__vars) {
            $this->setVars(new Variables());
        }

        if (null === $key) {
            return $this->__vars;
        }
        return $this->__vars[$key];
    }

    /**
     * Set script resolver
     *
     * @param  Resolver $resolver
     * @return PhpRenderer
     * @throws Exception\InvalidArgumentException
     */
    public function setResolver(Resolver $resolver)
    {
        $this->templateResolver = $resolver;
        return $this;
    }

    /**
     * Retrieve template name or template resolver
     *
     * @param  null|string $name
     * @return string|Resolver
     */
    public function resolver($name = null)
    {
        if (null === $this->templateResolver) {
            $this->setResolver(new Resolver\TemplatePathStack());
        }

        if (null !== $name) {
            return $this->templateResolver->resolve($name, $this);
        }

        return $this->templateResolver;
    }

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

    public function __construct(\Twig_Environment $environment)
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
    public function render($nameOrModel, $values = null)
    {
        if ($nameOrModel instanceof Model) {
            $model       = $nameOrModel;
            $nameOrModel = $model->getTemplate();
            if (empty($nameOrModel)) {
                throw new \DomainException(sprintf(
                    '%s: received View Model argument, but template is empty',
                    __METHOD__
                ));
            }
            $options = $model->getOptions();
            foreach ($options as $setting => $value) {
                $method = 'set' . $setting;
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
                unset($method, $setting, $value);
            }
            unset($options);

            // Give view model awareness via ViewModel helper
            $helper = $this->plugin('view_model');
            $helper->setCurrent($model);

            $values = $model->getVariables();
            unset($model);
        }


        if (null !== $values) {
            $this->setVars($values);
        }

        $vars = $this->vars()->getArrayCopy();

        $name = $this->resolver($nameOrModel);

        $content = $this->getEnvironment()->render($name, $vars);
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
     * Set plugin broker instance
     *
     * @param  string|HelperBroker $broker
     * @return Zend\View\Abstract
     * @throws Exception\InvalidArgumentException
     */
    public function setBroker($broker)
    {
        if (is_string($broker)) {
            if (!class_exists($broker)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Invalid helper broker class provided (%s)',
                    $broker
                ));
            }
            $broker = new $broker();
        }
        if (!$broker instanceof HelperBroker) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Helper broker must extend Zend\View\HelperBroker; got type "%s" instead',
                (is_object($broker) ? get_class($broker) : gettype($broker))
            ));
        }
        $broker->setView($this);
        $this->__helperBroker = $broker;
    }

    /**
     * Get plugin broker instance
     *
     * @return HelperBroker
     */
    public function getBroker()
    {
        if (null === $this->__helperBroker) {
            $this->setBroker(new HelperBroker());
        }
        return $this->__helperBroker;
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

}