<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 18.02.15
 * Time: 12:33
 */

namespace MintSoft\Authorize\Bundle\Listener;

use MintSoft\Authorize\ClassGuard;
use MintSoft\Authorize\Exception\NotAllowedException;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class RbacAnnotationListener
{
    /**
     * @var ClassGuard
     */
    protected $classGuard;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    public function __construct(ClassGuard $classGuard, TokenStorage $tokenStorage)
    {
        $this->classGuard   = $classGuard;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * This event will fire during any controller call
     */
    public function onKernelController(FilterControllerEvent $event)
    {

        if (!is_array($controller = $event->getController())) { //return if no controller
            return;
        }

        $token      = $this->tokenStorage->getToken();
        $userName   = $token ? $token->getUsername() : '';
        $className  = get_class($controller[0]);
        $methodName = $controller[1];

        if (!$this->classGuard->isAllowed($className, $methodName, $userName)) {
            throw new NotAllowedException('User ' . $userName . ' has no access to: ' . $className . '::' . $methodName);
        }
    }
}
