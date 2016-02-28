<?php
namespace Onlime\ExceptionReportBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        // exclude 4xx level http exceptions
        if ($exception instanceof HttpException) {
            if ($this->excludeHttp) {
                return;
            }

            if ($this->excluded404s && $exception instanceof NotFoundHttpException) {
                $request   = $this->emailReport->getRequest();
                $blacklist = '{(' . implode('|', $this->excluded404s) . ')}i';
                if (preg_match($blacklist, $request->getPathInfo())) {
                    return;
                }
            }
        }

        $this->sendEmailOnError($exception);

        //error_log($exception->getMessage() . ' in: ' . $exception->getFile() . ':' . $exception->getLine());
    }
}
