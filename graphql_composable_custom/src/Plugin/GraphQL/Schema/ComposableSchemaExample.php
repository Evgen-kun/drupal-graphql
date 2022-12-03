<?php

namespace Drupal\graphql_composable\Plugin\GraphQL\Schema;

use Drupal\graphql\Plugin\GraphQL\Schema\ComposableSchema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql_examples\Wrappers\QueryConnection;

/**
 * @Schema(
 *   id = "composable",
 *   name = "Composable Example schema",
 *   extensions = "composable",
 * )
 */
class ComposableSchemaExample extends ComposableSchema {
    
    /**
     * {@inheritdoc}
     */
    public function getResolverRegistry() {
        $builder = new ResolverBuilder();
        $registry = new ResolverRegistry();
    
        $this->addQueryFields($registry, $builder);
    
        // Re-usable connection type fields.
        $this->addConnectionFields('ArticleConnection', $registry, $builder);
    
        return $registry;
    }
    

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addQueryFields(ResolverRegistry $registry, ResolverBuilder $builder): void {
        $registry->addFieldResolver('Query', 'articles',
          $builder->produce('query_articles')
            ->map('offset', $builder->fromArgument('offset'))
            ->map('limit', $builder->fromArgument('limit'))
        );
    }
    

    /**
     * @param string $type
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addConnectionFields($type, ResolverRegistry $registry, ResolverBuilder $builder): void {
        $registry->addFieldResolver($type, 'total',
          $builder->callback(function (QueryConnection $connection) {
            return $connection->total();
          })
        );
    
        $registry->addFieldResolver($type, 'items',
          $builder->callback(function (QueryConnection $connection) {
            return $connection->items();
          })
        );
    }
}
