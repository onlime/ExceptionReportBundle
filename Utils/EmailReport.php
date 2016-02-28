<?php

namespace Onlime\ExceptionReportBundle\Utils;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\RequestStack;
use Maxmind\Bundle\GeoipBundle\Service\GeoipManager;

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
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $from;

    /**
     * @param RequestStack $requestStack
     * @param TwigEngine $templating
     * @param \Swift_Mailer $mailer
     * @param string $to
     * @param string $from
     */
    public function __construct(RequestStack $requestStack, GeoipManager $geoip, TwigEngine $templating, \Swift_Mailer $mailer, $to = null, $from = null)
    {
        $this->request    = $requestStack->getCurrentRequest();
        $this->geoip      = $geoip;
        $this->templating = $templating;
        $this->mailer     = $mailer;
        $this->to         = $to;
        $this->from       = $from ?: $to;
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
        if (!$this->to) {
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

        $message = \Swift_Message::newInstance()
            ->setSubject('Exception Report')
            ->setTo($this->to)
            ->setFrom($this->from)
            ->setBody($body);

        $this->mailer->send($message);
    }
}
