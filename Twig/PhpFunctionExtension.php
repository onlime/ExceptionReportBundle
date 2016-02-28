<?php
namespace Onlime\ExceptionReportBundle\Twig;

class PhpFunctionExtension extends \Twig_Extension
{
    private $functions = [
        'uniqid',
        'floor',
        'ceil',
        'addslashes',
        'chr',
        'chunk_​split',
        'convert_​uudecode',
        'crc32',
        'crypt',
        'hex2bin',
        'md5',
        'sha1',
        'strpos',
        'strrpos',
        'ucwords',
        'wordwrap',
        'phpversion',
        'realpath',
        'print_r',
        'gethostbyaddr',
    ];

    /**
     * PhpFunctionExtension constructor.
     *
     * @param array $functions override default set of allowed functions
     */
    public function __construct(array $functions = [])
    {
        if ($functions) {
            $this->allowFunctions($functions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $twigFunctions = [];
        foreach ($this->functions as $function) {
            $twigFunctions[] = new \Twig_SimpleFunction($function, $function);
        }
        return $twigFunctions;
    }

    /**
     * Add an allowed PHP function.
     *
     * @param $function
     */
    public function allowFunction($function)
    {
        $this->functions[] = $function;
    }

    /**
     * Override default set of allowed functions
     *
     * @param array $functions
     */
    public function allowFunctions(array $functions)
    {
        $this->functions = $functions;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'php_function';
    }
}
