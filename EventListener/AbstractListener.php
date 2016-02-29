<?php

namespace Onlime\ExceptionReportBundle\EventListener;

use Onlime\ExceptionReportBundle\Utils\EmailReport;

abstract class AbstractListener
{
    /**
     * @var EmailReport
     */
    protected $emailReport;

    /**
     * @param EmailReport $emailReport
     */
    public function __construct(EmailReport $emailReport)
    {
        $this->emailReport        = $emailReport;
    }

    /**
     * @param \Exception $exception
     * @param string|null $message
     */
    protected function sendEmailOnError(\Exception $exception, $message = null)
    {
        $this->emailReport->send($exception, $message);
    }
}