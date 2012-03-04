<?php

namespace ZfTwig;

class TwigEnvironment extends \Twig_Environment
{

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