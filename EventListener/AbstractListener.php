<?php

namespace Onlime\ExceptionReportBundle\EventListener;

use Onlime\ExceptionReportBundle\Utils\EmailReporter;
use Psr\Log\LoggerInterface;

abstract class AbstractListener
{
    /**
     * @var EmailReporter
     */
    private $emailReporter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EmailReporter $emailReporter
     * @param LoggerInterface $logger
     */
    public function __construct(EmailReporter $emailReporter, LoggerInterface $logger)
    {
        $this->emailReporter = $emailReporter;
        $this->logger        = $logger;
    }

    /**
     * Send exception report email.
     *
     * @param \Exception $exception
     * @param string|null $message
     */
    protected function sendEmailOnError(\Exception $exception, $message = null)
    {
        try {
            $this->emailReporter->send($exception, $message);
        } catch (\Exception $e) {
            // silently fail if exception report could not be sent
            $this->logger->error(sprintf(
                'Could not send exception report on %s%s: %s',
                get_class($exception),
                (null === $message) ? '' : " ($message)",
                $e->getMessage()
            ));
        }
    }
}