ZfTwig Module
=============

This Zend Framework 2 module allows you to use Twig (http://twig.sensiolabs.org/) templates in your projects.
It supports Zend view helpers:

```
    {{ headTitle() }}
    {{ url({ "some_key": "some_val" }) }}
```

Installation
------------

1. Add ZfTwig to your vendor directory

    cd MyApplicationDirectory
    git submodule add git@github.com:mtymek/ZfTwig.git vendor/ZfTwig

2. Add ZfTwig to your application.config.php file:

    ```php
        <?php
        return array(
            'modules' => array(
                'ZfTwig',
                'Application',
            ),
        'module_listener_options' => array(
                'config_cache_enabled' => false,
                'cache_dir'            => 'data/cache',
                'module_paths' => array(
                    './module',
                    './vendor',
                ),
            ),
        );
    ```

3. Configure TwigRenderingStrategy in Module.php:

    ```php
        public function init(Manager $moduleManager)
        {
            $events = StaticEventManager::getInstance();
            $events->attach('bootstrap', 'bootstrap', array($this, 'initializeView'));
        }

        public function initializeView($e)
        {
            $app          = $e->getParam('application');
            $basePath     = $app->getRequest()->getBasePath();
            $locator      = $app->getLocator();
            $renderer     = $locator->get('ZfTwig\TwigRenderer');
            $renderer->plugin('basePath')->setBasePath($basePath);

            $view         = $locator->get('Zend\View\View');
            $twigStrategy = $locator->get('ZfTwig\TwigRenderingStrategy');
            $view->events()->attach($twigStrategy, 100);
        }
    ```

4. Configure Dependency Injection Container:

    ```php
        return array(
            'di' => array(
                'instance' => array(

                    // setup other stuff...
                    // ...

                    // setup view script resolvers - very similar to configuration
                    // from ZendSkeletonApplication
                    'Zend\View\Resolver\AggregateResolver' => array(
                        'injections' => array(
                            'Zend\View\Resolver\TemplateMapResolver',
                            'Zend\View\Resolver\TemplatePathStack',
                        ),
                    ),
                    'Zend\View\Resolver\TemplateMapResolver' => array(
                        'parameters' => array(
                            'map'  => array(
                                'layout/layout' => __DIR__ . '/../view/layout/layout.twig',
                            ),
                        ),
                    ),
                    'Zend\View\Resolver\TemplatePathStack' => array(
                        'parameters' => array(
                            'paths'  => array(
                                'application' => __DIR__ . '/../view',
                            ),
                            'defaultSuffix' => 'twig'   // change default extension from .phtml to .twig
                        ),
                    ),
                    // Tell TwigRenderer how it should locate .twig files
                    'ZfTwig\TwigRenderer' => array(
                        'parameters' => array(
                            'resolver' => 'Zend\View\Resolver\AggregateResolver',
                        ),
                    ),
                ),
            );
    ```
