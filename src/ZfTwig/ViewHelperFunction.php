<?php

namespace ZfTwig;

class ViewHelperFunction extends \Twig_Function
{
    protected $name;
    protected $broker;

    public function __construct($name, $broker)
    {
        $this->name = $name;
        $this->broker = $broker;
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