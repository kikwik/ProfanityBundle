<?php

namespace Vangrg\ProfanityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vangrg_profanity');

        $rootNode
            ->children()
                ->scalarNode('storage')->defaultValue('vangrg_profanity.storage.default')->end()
                ->booleanNode('allow_bound_by_words')->defaultFalse()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
