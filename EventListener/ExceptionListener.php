<?php
namespace Onlime\ExceptionReportBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * The OnlimeExceptionReportBundle ExceptionListener.
 *
 * Handles exceptions that occur in the code base.
 *
 * @author      Philip Iezzi <philip.iezzi@onlime.ch>
 * @copyright   Copyright (c) 2016 Onlime Webhosting (https://www.onlime.ch)
 */
class ExceptionListener extends AbstractListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $this->sendEmailOnError($exception);

        //error_log($exception->getMessage() . ' in: ' . $exception->getFile() . ':' . $exception->getLine());
    }
}
