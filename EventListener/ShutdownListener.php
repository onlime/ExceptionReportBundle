<?php
namespace Onlime\ExceptionReportBundle\EventListener;

use Symfony\Component\HttpKernel\Event\KernelEvent;

/**
 * The OnlimeExceptionReportBundle ShutdownListener.
 *
 * Handles exceptions that occur in the code base.
 *
 * @author      Philip Iezzi <philip.iezzi@onlime.ch>
 * @copyright   Copyright (c) 2016 Onlime Webhosting (https://www.onlime.ch)
 */
class ShutdownListener extends AbstractListener
{
    /**
     * Register the handler on the request.
     *
     * @param KernelEvent $event
     */
    public function register(KernelEvent $event)
    {
        register_shutdown_function([$this, 'onShutdown']);
    }

    /**
     * Handles the PHP shutdown event.
     *
     * This event exists almost solely to provide a means to catch and log errors that might have been
     * otherwise lost when PHP decided to die unexpectedly.
     */
    public function onShutdown()
    {
        // Get the last error if there was one, if not, let's get out of here.
        if (!$error = error_get_last()) {
            return;
        }

        $fatal = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR];

        if (!in_array($error['type'], $fatal)) {
            return;
        }

        $message = '[Shutdown Error]: %s';
        $message = sprintf($message, $error['message']);

        $errorMessage = $message . ' in: ' . $error['file'] . ':' . $error['line'];
        $this->sendEmailOnError(null, $errorMessage);

        //error_log($errorMessage);
    }
}
