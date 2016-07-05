<?php

namespace Thruster\Bundle\ViewBundle\View;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class View
 *
 * @package Thruster\Bundle\ViewsBundle
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class View implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param string $view A short notation view (a:b:c) AppBundle:Default:homepage
     *
     * @return callable
     */
    public function getView($view)
    {
        return $this->container->get('thruster_views.view_resolver')->getView($view);
    }

    /**
     * @param string $view A short notation view (a:b:c) AppBundle:Default:homepage
     * @param mixed  $data
     *
     * @return mixed
     */
    public function renderOne($view, $data)
    {
        return $this->container->get('thruster_views.view_resolver')->renderOne($view, $data);
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
        return $this->container->get('thruster_views.view_resolver')->renderMany($view, $data, $preserveKeys);
    }
}
