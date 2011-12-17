<?php

return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'view'  => 'ZfTwig\Renderer',
            ),
            'ZfTwig\Renderer' => array(
                'parameters' => array(
                    'environment' => 'ZfTwig\TwigEnvironment'
                ),
            ),
            'ZfTwig\TwigEnvironment' => array(
                'parameters' => array(
                    'loader' => 'ZfTwig\TemplateLoader',
                    'broker' => 'Zend\View\HelperBroker',
                    'options' => array(
                        'cache' => __DIR__ . '/../../../data/cache/twig',
                        'auto_reload' => true,
                        'debug' => true
                    )
                )
            )
        )
    )
);