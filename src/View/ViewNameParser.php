<?php

namespace Thruster\Bundle\ViewBundle\View;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * ViewNameParser converts controller from the short notation a:b:c
 * (BlogBundle:Post:index) to a fully-qualified class::method string
 * (Bundle\BlogBundle\View\PostView::singleItem).
 *
 * @package Thruster\Bundle\ViewsBundle\View
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ViewNameParser
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Converts a short notation a:b:c to a class::method.
     *
     * @param string $view A short notation view (a:b:c)
     *
     * @return string A string in the class::method notation
     *
     * @throws \InvalidArgumentException when the specified bundle is not enabled
     *                                   or the controller cannot be found
     */
    public function parse($view)
    {
        $originalView = $view;

        if (3 !== count($parts = explode(':', $view))) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The "%s" view is not a valid "a:b:c" view string.',
                    $view
                )
            );
        }

        list($bundle, $view, $method) = $parts;
        $view    = str_replace('/', '\\', $view);
        $bundles = [];

        try {
            // this throws an exception if there is no such bundle
            $allBundles = $this->kernel->getBundle($bundle, false);
        } catch (\InvalidArgumentException $e) {
            $message = sprintf(
                'The "%s" (from the view name "%s") does not exist or is not enabled in your kernel!',
                $bundle,
                $originalView
            );

            if ($alternative = $this->findAlternative($bundle)) {
                $message .= sprintf(' Did you mean "%s:%s:%s"?', $alternative, $view, $method);
            }

            throw new \InvalidArgumentException($message, 0, $e);
        }

        foreach ($allBundles as $b) {
            $try = $b->getNamespace() . '\\View\\' . $view . 'View';
            if (class_exists($try)) {
                return $try . '::' . $method;
            }

            $bundles[] = $b->getName();
            $msg       = sprintf(
                'The view value "%s:%s:%s" maps to a "%s" class, but this class was not found. Create this class or ' .
                'check the spelling of the class and its namespace.',
                $bundle,
                $view,
                $method,
                $try
            );
        }

        if (count($bundles) > 1) {
            $msg = sprintf(
                'Unable to find view "%s:%s" in bundles %s.',
                $bundle,
                $view,
                implode(', ', $bundles)
            );
        }

        throw new \InvalidArgumentException($msg);
    }

    /**
     * Converts a class::method notation to a short one (a:b:c).
     *
     * @param string $view A string in the class::method notation
     *
     * @return string A short notation view (a:b:c)
     *
     * @throws \InvalidArgumentException when the view is not valid or cannot be found in any bundle
     */
    public function build($view)
    {
        if (0 === preg_match('#^(.*?\\\\View\\\\(.+)View)::(.+)$#', $view, $match)) {
            throw new \InvalidArgumentException(
                sprintf('The "%s" view is not a valid "class::method" string.', $view)
            );
        }

        $className = $match[1];
        $viewName = $match[2];
        $methodName = $match[3];
        foreach ($this->kernel->getBundles() as $name => $bundle) {
            if (0 !== strpos($className, $bundle->getNamespace())) {
                continue;
            }

            return sprintf('%s:%s:%s', $name, $viewName, $methodName);
        }

        throw new \InvalidArgumentException(
            sprintf('Unable to find a bundle that defines view "%s".', $view)
        );
    }

    /**
     * Attempts to find a bundle that is *similar* to the given bundle name.
     *
     * @param string $nonExistentBundleName
     *
     * @return string
     */
    private function findAlternative($nonExistentBundleName)
    {
        $bundleNames = array_map(function (BundleInterface $b) {
            return $b->getName();
        }, $this->kernel->getBundles());

        $alternative = null;
        $shortest = null;
        foreach ($bundleNames as $bundleName) {
            // if there's a partial match, return it immediately
            if (false !== strpos($bundleName, $nonExistentBundleName)) {
                return $bundleName;
            }

            $lev = levenshtein($nonExistentBundleName, $bundleName);
            if ($lev <= strlen($nonExistentBundleName) / 3 && ($alternative === null || $lev < $shortest)) {
                $alternative = $bundleName;
                $shortest = $lev;
            }
        }

        return $alternative;
    }
}
