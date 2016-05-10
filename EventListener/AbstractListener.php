<?php

namespace Onlime\ExceptionReportBundle\EventListener;

use Onlime\ExceptionReportBundle\Utils\EmailReport;
use Psr\Log\LoggerInterface;

abstract class AbstractListener
{
    /**
     * @var EmailReport
     */
    private $emailReport;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EmailReport $emailReport
     * @param LoggerInterface $logger
     */
    public function __construct(EmailReport $emailReport, LoggerInterface $logger)
    {
        $this->emailReport = $emailReport;
        $this->logger      = $logger;
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
            $this->emailReport->send($exception, $message);
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