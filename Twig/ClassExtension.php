<?php
namespace Onlime\ExceptionReportBundle\Twig;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ClassExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('HttpException', function ($event) { return $event instanceof HttpException; }),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('class', [$this, 'getClass'])
        ];
    }

    public function getClass($object)
    {
        return (new \ReflectionClass($object))->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'typeof_tests';
    }
}
