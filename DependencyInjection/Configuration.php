<?php

namespace Onlime\ExceptionReportBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */


/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * Possible configurations (brackets indicate optional params):
 *
 * - [exclude_http]: bool, defaults to true (do not report 4xx level http exceptions)
 * - [excluded_404s]: if set, excludes 404s coming from URLs matching any of those patterns
 * - email_to: exception report recipient email
 * - [email_from]: exception report sender email (defaults to email_to)
 * - [enable_email_reports]: bool, defaults to true
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('onlime_exception_report');
        $rootNode
            ->children()
            ->booleanNode('exclude_http')->defaultTrue()->end()
            ->arrayNode('excluded_404s')
                ->canBeUnset()
                ->prototype('scalar')->end()
            ->end()
            ->scalarNode('email_to')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('email_from')->end()
            ->booleanNode('enable_email_reports')->defaultTrue()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
