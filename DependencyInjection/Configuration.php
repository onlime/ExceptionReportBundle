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
 * Possible handler configurations (brackets indicate optional params):
 *
 * - [enabled]: bool, defaults to true
 * - [exclude_http]: bool, defaults to true (do not report 4xx level http exceptions)
 * - [excluded_404s]: if set, excludes 404s coming from URLs matching any of those patterns
 * - from_email: exception report sender email
 * - to_email: exception report recipient email(s)
 * - [subject]: optional, defaults to 'Exception Report'
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
            ->fixXmlConfig('handler')
            ->children()
                ->arrayNode('handlers')
                    ->canBeUnset()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->fixXmlConfig('excluded_404')
                        ->canBeUnset()
                        ->children()
                            ->booleanNode('enabled')->defaultTrue()->end()
                            ->booleanNode('exclude_http')->defaultTrue()->end()
                            ->arrayNode('excluded_404s')
                                ->canBeUnset()
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('from_email')->isRequired()->cannotBeEmpty()->end()
                            ->arrayNode('to_email')->isRequired()->cannotBeEmpty()
                                ->prototype('scalar')->end()
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function ($v) { return array($v); })
                                ->end()
                            ->end()
                            ->scalarNode('subject')->defaultValue('Exception Report')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
