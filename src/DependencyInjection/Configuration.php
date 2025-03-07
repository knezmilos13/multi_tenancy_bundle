<?php

namespace Hakam\MultiTenancyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @category Database
 *
 * @author   Ramy Hakam <ramyhakam1@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('hakam_multi_tenancy');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->variableNode('tenant_database_className')->info('Tenant dbs configuration Class Name')->defaultValue('TenantDb')->end()
            ->variableNode('tenant_database_identifier')->info('tenant db column name to get db configuration')->defaultValue('id')->end()
            ->arrayNode('tenant_connection')->info('tenant entity manager connection configuration')
            ->ignoreExtraKeys()
            ->addDefaultsIfNotSet()
            ->children()
            ->variableNode('url')->defaultValue( $_ENV['DATABASE_URL']?? 'mysql://root@localhost:3306/DM')->end()
            ->variableNode('host')->defaultValue('127.0.0.1')->end()
            ->variableNode('port')->defaultValue('3306')->end()
            ->variableNode('driver')->defaultValue('pdo_mysql')->end()
            ->variableNode('charset')->defaultValue('utf8')->end()
            ->variableNode('server_version')->defaultValue('5.7')->end()
            ->end()
            ->end()
            ->end()
            ->children()
            ->arrayNode('tenant_migration')
            ->info('tenant db migration configurations, Its recommended to have a different migration for tenants dbs than you main migration config ')
            ->ignoreExtraKeys()
            ->addDefaultsIfNotSet()
            ->children()
            ->variableNode('tenant_migration_namespace')->defaultValue('DoctrineMigrations\Tenant')->end()
            ->variableNode('tenant_migration_path')->defaultValue('%kernel.project_dir%/migrations/Tenant')->end()
            ->end()
            ->end()
            ->end()
            ->children()
            ->arrayNode('tenant_entity_manager')
            ->info('tenant entity manger configuration , which is used to manage tenant entities')
            ->ignoreExtraKeys()
            ->addDefaultsIfNotSet()
            ->children()
            ->variableNode('tenant_naming_strategy')->defaultValue('doctrine.orm.naming_strategy.default')->end()
            ->arrayNode("dql")->canBeUnset()->info('tenant entity manager dql configuration')
            ->children()
            ->arrayNode('string_functions')
            ->useAttributeAsKey('name')
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('numeric_functions')
            ->useAttributeAsKey('name')
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('datetime_functions')
            ->useAttributeAsKey('name')
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
			
            ->arrayNode('mappings')
            ->info('tenant Entity Manager mapping configuration, Its recommended to have a different mapping config than your main entity config')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->beforeNormalization()
            ->ifString()
            ->then(static function ($v) { // not sure what this does
                return ['type' => $v];
            })
            ->end()
            ->treatNullLike([])
            ->treatFalseLike(['mapping' => false])
            ->performNoDeepMerging()
            ->children()
            ->scalarNode('mapping')->defaultValue(true)->end()
            ->scalarNode('type')->end()
            ->scalarNode('dir')->end()
            ->scalarNode('alias')->end()
            ->scalarNode('prefix')->end()
            ->booleanNode('is_bundle')->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
