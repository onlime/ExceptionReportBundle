<?php
namespace Onlime\ExceptionReportBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ExceptionEvent extends Event
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * ExceptionEvent constructor.
     *
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}