<?php

namespace Thruster\Bundle\ViewBundle\View;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ViewResolver
 *
 * @package Thruster\Bundle\ViewsBundle\View
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ViewResolver
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ViewNameParser
     */
    protected $parser;

    public function __construct(ContainerInterface $container, ViewNameParser $parser)
    {
        $this->container = $container;
        $this->parser = $parser;
    }

    /**
     * @param string $view A short notation view (a:b:c) AppBundle:Default:homepage
     * @param mixed  $data
     *
     * @return mixed
     */
    public function renderOne($view, $data)
    {
        return call_user_func(
            $this->getView($view),
            $data
        );
    }

    /**
     * @param string $view A short notation view (a:b:c) AppBundle:Default:homepage
     * @param array  $data
     * @param bool   $preserveKeys
     *
     * @return array
     */
    public function renderMany($view, $data, $preserveKeys = false)
    {
        $result = [];

        foreach ($data as $key => $item) {
            if (true === $preserveKeys) {
                $result[$key] = $this->renderOne($view, $item);
            } else {
                $result[] = $this->renderOne($view, $item);
            }
        }

        return $result;
    }

    /**
     * @param string $view A short notation view (a:b:c) AppBundle:Default:homepage
     *
     * @return callable
     */
    public function getView($view)
    {
        if (is_array($view)) {
            return $view;
        }

        if (is_object($view)) {
            if (method_exists($view, '__invoke')) {
                return $view;
            }

            throw new \InvalidArgumentException(sprintf('View "%s" is not callable.', get_class($view)));
        }

        if (false === strpos($view, ':')) {
            if (method_exists($view, '__invoke')) {
                return $this->instantiateView($view);
            } elseif (function_exists($view)) {
                return $view;
            }
        }

        $callable = $this->createView($view);

        if (false === is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf('View "%s" is not callable.', $view));
        }

        return $callable;
    }

    protected function createView($view)
    {
        if (false === strpos($view, '::')) {
            $view = $this->parser->parse($view);
        }

        list($class, $method) = explode('::', $view, 2);

        if (false === class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        return [$this->instantiateView($class), $method];
    }

    protected function instantiateView($class)
    {
        if ($this->container->has($class)) {
            return $this->container->get($class);
        }

        $view = new $class();

        if ($view instanceof ContainerAwareInterface) {
            $view->setContainer($this->container);
        }

        return $view;
    }

}
