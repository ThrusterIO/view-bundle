<?php

namespace Thruster\Bundle\ViewBundle\Traits;

use Thruster\Bundle\ViewBundle\View\ViewResolver;

/**
 * Trait ViewAwareTrait
 *
 * @package Thruster\Bundle\ViewBundle\Traits
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
trait ViewAwareTrait
{
    /**
     * @var ViewResolver
     */
    protected $viewResolver;

    /**
     * @param ViewResolver $viewResolver
     *
     * @return ViewAwareTrait
     * @required
     */
    public function setViewResolver(ViewResolver $viewResolver)
    {
        $this->viewResolver = $viewResolver;

        return $this;
    }

    /**
     * @param string $view A short notation view (a:b:c) "AppBundle:Default:homepage" or "homepage"
     *                     (for same name view class as controller under View folder with suffix View instead of
     *                     Controller for e.g. For AppBundle\Controller\DefaultController AppBundle\View\DefaultView
     * @param mixed  $data
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function view($view, $data)
    {
        if (false === strpos($view, ':')) {
            $class = preg_replace(['#\\\\Controller\\\\#', '#Controller$#'], ['\\View\\', 'View'], get_class($this));
            $view  = $class . '::' . $view;
        }

        return call_user_func(
            $this->viewResolver->getView($view),
            $data
        );
    }
}