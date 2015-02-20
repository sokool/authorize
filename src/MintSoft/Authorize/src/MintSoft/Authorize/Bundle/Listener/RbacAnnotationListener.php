<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 18.02.15
 * Time: 12:33
 */

namespace MintSoft\Authorize\Bundle\Listener;

use MintSoft\Authorize\Exception\NotAllowedException;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RbacAnnotationListener
{
    /**
     * This event will fire during any controller call
     */
    public function onKernelController(FilterControllerEvent $event)
    {

        if (!is_array($controller = $event->getController())) { //return if no controller
            return;
        }

        $className  = get_class($controller[0]);
        $methodName = $controller[1];
        $userName   = $controller[0]->get('security.token_storage')->getToken()->getUser()->getUserName();

        /** @var \MintSoft\Authorize\ClassGuard $classGuard */
        $classGuard = $controller[0]->get('rbac.controller.guard');

        if (!$classGuard->isAllowed($className, $methodName, $userName)) {
            throw new NotAllowedException('User ' . $userName . ' has no access to: ' . $className . '::' . $methodName);
        }
    }
}
