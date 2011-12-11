<?php

namespace ZfTwig;

class TwigEnvironment extends \Twig_Environment
{
    protected $renderer;

    public function __construct($renderer, $loader = null, $options = array())
    {
        $this->renderer = $renderer;
        parent::__construct($loader, $options);
    }

    public function renderer()
    {
        return $this->renderer;
    }

    public function getFunction($name)
    {
        if (false !== $function = parent::getFunction($name))
        {
            return $function;
        }

        $helper = $this->renderer->plugin($name);

        if (null === $helper)
        {
            return null;
        }

        $function = new ViewHelperFunction($name, $helper);
        $this->addFunction($name, $function);

        return $function;
    }
}