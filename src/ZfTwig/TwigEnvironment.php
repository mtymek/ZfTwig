<?php

namespace ZfTwig;

use Zend\View\HelperBroker;

class TwigEnvironment extends \Twig_Environment
{

    protected $__helperBroker;

    /**
     * Set plugin broker instance
     *
     * @param  string|HelperBroker $broker
     * @return Zend\View\Abstract
     * @throws Exception\InvalidArgumentException
     */
    public function setBroker($broker)
    {
        $this->__helperBroker = $broker;
        return $this;
    }

    /**
     * Get plugin broker instance
     *
     * @return HelperBroker
     */
    public function getBroker()
    {
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

    public function getFunction($name)
    {
        if (false !== $function = parent::getFunction($name)) {
            return $function;
        }

        $helper = $this->plugin($name);

        if (null === $helper) {
            return null;
        }

        $function = new ViewHelperFunction($name, $helper);
        $this->addFunction($name, $function);

        return $function;
    }

}