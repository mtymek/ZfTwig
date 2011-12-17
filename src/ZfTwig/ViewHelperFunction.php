<?php

namespace ZfTwig;

class ViewHelperFunction extends \Twig_Function
{
    protected $name;
    protected $helper;

    public function __construct($name, $helper)
    {
        $this->name = $name;
        $this->helper = $helper;
    }

    public function getSafe(\Twig_Node $functionArgs)
    {
        return array('html');
    }

    public function compile()
    {
        $name = preg_replace('#[^a-z0-9]+#i', '', $this->name);
        return '$this->env->plugin("' . $name . '")->__invoke';
    }
}