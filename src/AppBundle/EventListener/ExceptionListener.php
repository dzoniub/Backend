<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use AppBundle\Exception\ControlerNotAvailableException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!($exception instanceof ControlerNotAvailableException)) {
            return;
        }
        $event->setResponse(new JsonResponse(['uspesno' => false]));
        $event->setException(new HttpException('200'));

    }
}