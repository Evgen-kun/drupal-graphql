<?php

namespace Drupal\graphql_task\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\graphql_task\GraphQL\Response\TaskResponse;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new task entity.
 *
 * @DataProducer(
 *   id = "create_task",
 *   name = @Translation("Create Task"),
 *   description = @Translation("Creates a new task."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Task")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Task data")
 *     )
 *   }
 * )
 */
class CreateTask extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  /**
   * CreateTask constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  /**
   * Creates a task.
   *
   * @param array $data
   *   The submitted values for the task.
   *
   * @return \Drupal\graphql_task\GraphQL\Response\TaskResponse
   *   The newly created task.
   *
   * @throws \Exception
   */
  public function resolve(array $data) {
    $response = new TaskResponse();
    if ($this->currentUser->hasPermission("create task content")) {
      $values = [
        'type' => 'task',
        'title' => $data['title'],
        'body' => $data['description'],
      ];
      $node = Node::create($values);
      $node->save();
      $response->setTask($node);
    }
    else {
      $response->addViolation(
        $this->t('You do not have permissions to create tasks.')
      );
    }
    return $response;
  }

}
