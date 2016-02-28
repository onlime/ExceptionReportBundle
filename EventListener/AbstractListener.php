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
     * @var bool
     */
    protected $enableEmailReports;

    /**
     * @var bool
     */
    protected $excludeHttp;

    /**
     * @var array
     */
    protected $excluded404s;

    /**
     * @param EmailReport $emailReport
     * @param bool $enableEmailReports
     * @param bool $excludeHttp
     * @param array|null $excluded404s
     */
    public function __construct(EmailReport $emailReport, $enableEmailReports = true, $excludeHttp = true, array $excluded404s = null)
    {
        $this->emailReport        = $emailReport;
        $this->enableEmailReports = $enableEmailReports;
        $this->excludeHttp        = $excludeHttp;
        $this->excluded404s       = $excluded404s;
    }

    /**
     * @param \Exception $exception
     * @param string|null $message
     */
    protected function sendEmailOnError(\Exception $exception, $message = null)
    {
        if ($this->enableEmailReports) {
            $this->emailReport->send($exception, $message);
        }
    }
}