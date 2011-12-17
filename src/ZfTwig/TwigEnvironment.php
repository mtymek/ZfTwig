<?php

namespace ZfTwig;

use Zend\View\HelperBroker;

class TwigEnvironment extends \Twig_Environment
{
    protected $renderer;

    /**
     * Helper broker
     *
     * @var HelperBroker
     */
    protected $helperBroker;

    /**
     * @param TemplateLoader $loader
     * @param HelperBroker $broker
     * @param array $options
     */
    public function __construct(TemplateLoader $loader, HelperBroker $broker, $options = array())
    {
        $this->setBroker($broker);
        parent::__construct($loader, $options);
    }

    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
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

    public function getFunction($name)
    {
        if (false !== $function = parent::getFunction($name))
        {
            return $function;
        }

        $helper = $this->plugin($name);

        if (null === $helper)
        {
            return null;
        }

        $function = new ViewHelperFunction($name, $helper);
        $this->addFunction($name, $function);

        return $function;
    }
}