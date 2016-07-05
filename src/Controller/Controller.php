<?php

namespace Thruster\Bundle\ViewBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BaseController
{
    /**
     * @param string $view A short notation view (a:b:c) "AppBundle:Default:homepage" or "homepage"
     *                     (for same name view class as controller under View folder with suffix View instead of
     *                     Controller for e.g. For AppBundle\Controller\DefaultController AppBundle\View\DefaultView
     * @param mixed  $data
     *
     * @return mixed
     */
    protected function view($view, $data)
    {
        if (false === strpos($view, ':')) {
            $class = preg_replace(['#\\\\Controller\\\\#', '#Controller$#'], ['\\View\\', 'View'], get_class($this));
            $view  = $class . '::' . $view;
        }

        return call_user_func(
            $this->get('thruster_views.view_resolver')->getView($view),
            $data
        );
    }

    /**
     * @param string $viewA short notation view (a:b:c) "AppBundle:Default:homepage" or "homepage"
     *                      (for same name view class as controller under View folder with suffix View instead of
     *                      Controller for e.g. For AppBundle\Controller\DefaultController AppBundle\View\DefaultView
     * @param mixed  $data
     * @param int    $status
     * @param array  $headers
     * @param array  $context
     *
     * @return JsonResponse
     */
    protected function jsonView($view, $data, $status = 200, $headers = [], $context = [])
    {
        $data = $this->view($view, $data);

        return parent::json($data, $status, $headers, $context);
    }
}
