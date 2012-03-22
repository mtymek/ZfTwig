<?php

namespace ZfTwig;

use Zend\View\Resolver\TemplatePathStack as ZendTemplatePathStack;

class TemplatePathStack extends ZendTemplatePathStack
{

    protected $defaultSuffix = 'twig';

}