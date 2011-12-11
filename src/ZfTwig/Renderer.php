<?php

namespace ZfTwig;

use Zend\View\PhpRenderer,
    Zend\View\TemplateResolver;


class Renderer extends PhpRenderer
{
    protected $environment;

    public function environment()
    {
        if (null === $this->environment) {
            $loader = new TemplateLoader($this->resolver());
            $this->environment = new TwigEnvironment($this, $loader, array(
                'cache' => __DIR__ . '/../../../../data/cache',
                'auto_reload' => true,
                'debug' => true
            ));
        }
        return $this->environment;
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script name to process.
     * @param array|null $vars
     * @return string The script output.
     */
    public function render($name, $vars = null)
    {
        $this->varsCache[] = $this->vars();

        if (null !== $vars) {
            $this->setVars($vars);
        }
        unset($vars);

        $vars = $this->vars()->getArrayCopy();

        $content = $this->environment()->render($name, $vars);

        $this->setVars(array_pop($this->varsCache));

        return $this->getFilterChain()->filter($content); // filter output
    }

}