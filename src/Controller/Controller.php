<?php

namespace Thruster\Bundle\ViewBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Thruster\Bundle\ViewBundle\Traits\ViewAwareTrait;

class Controller extends BaseController
{
    use ViewAwareTrait;

    /**
     * @param string $view  A short notation view (a:b:c) "AppBundle:Default:homepage" or "homepage"
     *                      (for same name view class as controller under View folder with suffix View instead of
     *                      Controller for e.g. For AppBundle\Controller\DefaultController AppBundle\View\DefaultView
     * @param mixed  $data
     * @param int    $status
     * @param array  $headers
     * @param array  $context
     *
     * @return JsonResponse
     * @throws \InvalidArgumentException
     */
    protected function jsonView($view, $data, $status = 200, $headers = [], $context = [])
    {
        $data = $this->view($view, $data);

        return parent::json($data, $status, $headers, $context);
    }
}
