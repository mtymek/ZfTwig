ZfTwig Module
=============

This Zend Framework 2 module allows you to use Twig templates in your projects.

It is shipped with custom view listener, so main application module simply needs to do
is setting up some variables in Module.php:


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
        $view->getEnvironment()->getLoader()->addPath(__DIR__ . '/views');

        $url    = $view->plugin('url');
        $url->setRouter($app->getRouter());

        $view->plugin('headTitle')->setSeparator(' - ')
                                          ->setAutoEscape(false)
                                          ->append('Application');
    }


Full example can be found here: https://github.com/mtymek/ZfTwigExample
