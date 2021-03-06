<?php

namespace Onlime\ExceptionReportBundle\Utils;

use Maxmind\Bundle\GeoipBundle\Service\GeoipManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Building the exception report email.
 */
class EmailReporter
{
    /**
     * var Request
     */
    private $request;

    /**
     * @var GeoipManager
     */
    private $geoip;

    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var array
     */
    private $handlers;

    /**
     * @param RequestStack $requestStack
     * @param TwigEngine $templating
     * @param \Swift_Mailer $mailer
     * @param array $handlers
     */
    public function __construct(RequestStack $requestStack, GeoipManager $geoip, TwigEngine $templating, \Swift_Mailer $mailer, array $handlers)
    {
        $this->request    = $requestStack->getCurrentRequest();
        $this->geoip      = $geoip;
        $this->templating = $templating;
        $this->mailer     = $mailer;
        $this->handlers   = $handlers;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Exception $exception
     * @param string|null $errorMsg
     */
    public function send(\Exception $exception, $errorMsg = null)
    {
        if (empty($this->handlers)) {
            return;
        }

        if (null === $errorMsg) {
            $errorMsg = $exception->getMessage();
        }

        // get country information of client IP
        $geoRecord = $this->geoip->lookup($this->request->getClientIp());
        $geoData   = [
            'countryCode' => ($geoRecord) ? $geoRecord->getCountryCode() : '--',
            'countryName' => ($geoRecord) ? $geoRecord->getCountryName() : 'Unknown Country'
        ];

        foreach ($this->handlers as $handler) {
            //dump($handler);
            if (!$handler['enabled']) {
                continue;
            }
            
            $subject = $handler['subject'];

            // exclude 4xx level http exceptions
            if ($exception instanceof HttpException) {
                if ($handler['exclude_http']) {
                    continue;
                }

                if ($handler['excluded_404s'] && $exception instanceof NotFoundHttpException) {
                    $blacklist = '{(' . implode('|', $handler['excluded_404s']) . ')}i';
                    if (preg_match($blacklist, $this->getRequest()->getPathInfo())) {
                        continue;
                    }
                }
                
                // append HTTP status code to subject
                $subject .= sprintf(' (HTTP %s)', $exception->getStatusCode());
            }

            // hide stacktrace output for specific exceptions
            $showStacktrace = true;
            foreach ($handler['no_stacktrace_on_exceptions'] as $noStacktraceException) {
                if ($exception instanceof $noStacktraceException) {
                    $showStacktrace = false;
                    break;
                }
            }

            $body = $this->templating->render(
                'OnlimeExceptionReportBundle::emailReport.txt.twig',
                [
                    'geoData'        => $geoData,
                    'exception'      => $exception,
                    'errorMsg'       => $errorMsg,
                    'showStacktrace' => $showStacktrace
                ]
            );

            $message = \Swift_Message::newInstance($subject, $body)
                ->setTo($handler['to_email'])
                ->setFrom($handler['from_email']);

            $this->mailer->send($message);
        }
    }
}
