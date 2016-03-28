<?php
namespace Onlime\ExceptionReportBundle\Event;

final class ExceptionEvents
{
    /**
     * The exception.report can be triggered on a caught exception that
     * should be reported but not break the application.
     *
     * Listeners receive an instance of:
     * Onlime\ExceptionReportBundle\Event\ExceptionEvent
     */
    const REPORT = 'exception.report';
}