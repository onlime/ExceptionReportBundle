<?php

namespace Onlime\ExceptionReportBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OnlimeExceptionReportExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('onlime.exception_report.handlers', $config['handlers']);
        /*
        $container->setParameter('onlime.exception_report.email_to', $config['email_to']);
        $container->setParameter('onlime.exception_report.email_from', $config['email_from']);
        $container->setParameter('onlime.exception_report.enable_email_reports', $config['enable_email_reports']);
        $container->setParameter('onlime.exception_report.exclude_http', $config['exclude_http']);
        $container->setParameter('onlime.exception_report.excluded_404s', $config['excluded_404s']);
        */

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');
    }
}
