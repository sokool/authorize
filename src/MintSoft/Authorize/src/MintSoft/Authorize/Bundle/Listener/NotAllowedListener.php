<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 20.02.15
 * Time: 10:08
 */

namespace MintSoft\Authorize\Bundle\Listener;

use MintSoft\Authorize\Exception\NotAllowedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class NotAllowedListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!$exception instanceof NotAllowedException) {
            return;
        }

        $event->setResponse(new Response($exception->getMessage()));
    }
}