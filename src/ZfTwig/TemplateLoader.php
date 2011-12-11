<?php

namespace ZfTwig;



class TemplateLoader implements \Twig_LoaderInterface
{

    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Gets the source code of a template, given its name.
     *
     * @param  string $name The name of the template to load
     *
     * @return string The template source code
     */
    function getSource($name)
    {
        return file_get_contents($this->resolver->getScriptPath($name));
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param  string $name The name of the template to load
     *
     * @return string The cache key
     */
    function getCacheKey($name)
    {
        return $this->resolver->getScriptPath($name);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     * @return bool
     */
    function isFresh($name, $time)
    {
        return filemtime($this->resolver->getScriptPath($name)) < $time;
    }
}