<?php

namespace Drupal\graphql_task\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\GraphQL\Response\ResponseInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\graphql_task\GraphQL\Response\TaskResponse;

/**
 * @SchemaExtension(
 *   id = "task_extension",
 *   name = "Composable Task extension",
 *   description = "A simple extension that adds node related fields.",
 *   schema = "composable"
 * )
 */
class ComposableSchemaTaskExtension extends SdlSchemaExtensionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry): void {
    $builder = new ResolverBuilder();

    $registry->addFieldResolver('Query', 'task',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['task']))
        ->map('id', $builder->fromArgument('id'))
    );


    // Create task mutation.
    $registry->addFieldResolver('Mutation', 'createTask',
      $builder->produce('create_task')
        ->map('data', $builder->fromArgument('data'))
    );


    $registry->addFieldResolver('TaskResponse', 'task',
      $builder->callback(function (TaskResponse $response) {
        return $response->task();
      })
    );

    $registry->addFieldResolver('TaskResponse', 'errors',
      $builder->callback(function (TaskResponse $response) {
        return $response->getViolations();
      })
    );


    $registry->addFieldResolver('Task', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Task', 'title',
      $builder->compose(
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Task', 'body',
      $builder->compose(
        $builder->produce('property_path')
        ->map('type', $builder->fromValue('entity:node'))
        ->map('value', $builder->fromParent())
        ->map('path', $builder->fromValue('body.value'))
      )
    );

    $registry->addFieldResolver('Task', 'author',
      $builder->compose(
        $builder->produce('entity_owner')
          ->map('entity', $builder->fromParent()),
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );


    // Response type resolver.
    $registry->addTypeResolver('Response', [
      __CLASS__,
      'resolveResponse',
    ]);
  }

  /**
   * Resolves the response type.
   *
   * @param \Drupal\graphql\GraphQL\Response\ResponseInterface $response
   *   Response object.
   *
   * @return string
   *   Response type.
   *
   * @throws \Exception
   *   Invalid response type.
   */
  public static function resolveResponse(ResponseInterface $response): string {
    // Resolve content response.
    if ($response instanceof TaskResponse) {
      return 'TaskResponse';
    }
    throw new \Exception('Invalid response type.');
  }

}
