ZfTwig Module
=============

This Zend Framework 2 module allows you to use Twig (http://twig.sensiolabs.org/) templates in your projects.
It supports Zend view helpers:

    {{ headTitle() }}
    {{ url({ "some_key": "some_val" }) }}

Installation
------------

1. Add ZfTwig to your module directory:


    cd MyApplication/module
    git submodule add git@github.com:mtymek/ZfTwig.git module/ZfTwig

2. Update application.config.php by adding ZfTwig module, so that it will look more or less like this:


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

3. ZfTwig is shipped with custom view listener, so main application module only needs to do
some basic setup:


    public function init(Manager $moduleManager)
    {
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'initializeView'));
    }

    public function initializeView($e)
    {
        $app          = $e->getParam('application');
        $locator      = $app->getLocator();
        $view         = $locator->get('view');

        // tell ZfTwig where it should look for view scripts
        $view->getEnvironment()->getLoader()->addPath(__DIR__ . '/views');

        // setup url relper
        $url = $view->plugin('url');
        $url->setRouter($app->getRouter());

        // set default page title
        $view->plugin('headTitle')->setSeparator(' - ')
                                          ->setAutoEscape(false)
                                          ->append('Application');
    }


After finishing this 3 steps, you can start using twig templates in your project.

Full example can be found here: https://github.com/mtymek/ZfTwigExample
