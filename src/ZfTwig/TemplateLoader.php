<?php

namespace ZfTwig;

use Zend\View\Resolver;

class TemplateLoader implements \Twig_LoaderInterface
{

    /**
     * Template resolver
     *
     * @var Resolver
     */
    protected $templateResolver;

    /**
     * Set script resolver
     *
     * @param \Zend\View\Resolver $resolver
     * @return TemplateLoader
     */
    public function setResolver(Resolver $resolver)
    {
        $this->templateResolver = $resolver;
        return $this;
    }

    /**
     * Retrieve template name or template resolver
     *
     * @param  null|string $name
     * @return string|Resolver
     */
    public function resolver($name = null)
    {
        if (null === $this->templateResolver) {
            $this->setResolver(new Resolver\TemplatePathStack());
        }

        if (null !== $name) {
            return $this->templateResolver->resolve($name);
        }

        return $this->templateResolver;
    }

    /**
     * Gets the source code of a template, given its name.
     *
     * @param  string $name The name of the template to load
     *
     * @return string The template source code
     */
    public function getSource($name)
    {
        if ($name[0] != '/') {
            $name = $this->resolver($name);
        }
        return file_get_contents($name);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param  string $name The name of the template to load
     *
     * @return string The cache key
     */
    public function getCacheKey($name)
    {
        if ($name[0] != '/') {
            $name = $this->resolver($name);
        }
        return $name;
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     * @return bool
     */
    public function isFresh($name, $time)
    {
        if ($name[0] != '/') {
            $name = $this->resolver($name);
        }
        return filemtime($name) < $time;
    }
}