<?php

return array(
    'di' => array(
        'instance' => array(
            'ZfTwig\TwigStrategy' => array(
                'parameters' => array(
                    'renderer' => 'ZfTwig\TwigRenderer',
                ),
            ),
            'ZfTwig\TwigRenderer' => array(
                'parameters' => array(
                    'environment' => 'ZfTwig\TwigEnvironment'
                )
            ),
            'ZfTwig\TwigEnvironment' => array(
                'parameters' => array(
                    'loader' => 'ZfTwig\TemplateLoader',
                    'options' => array(

                    )
                )
            )
        )
    )
);