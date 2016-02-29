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
class EmailReport
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

        $body = $this->templating->render(
            'OnlimeExceptionReportBundle::emailReport.txt.twig',
            [
                'geoData'   => $geoData,
                'exception' => $exception,
                'errorMsg'  => $errorMsg
            ]
        );

        foreach ($this->handlers as $handler) {
            //dump($handler);
            if (!$handler['enabled']) {
                continue;
            }

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
            }

            $message = \Swift_Message::newInstance()
                ->setSubject($handler['subject'])
                ->setTo($handler['to_email'])
                ->setFrom($handler['from_email'])
                ->setBody($body);

            $this->mailer->send($message);
        }
    }
}
