<?php

namespace Drupal\graphql_task\GraphQL\Response;

use Drupal\Core\Entity\EntityInterface;
use Drupal\graphql\GraphQL\Response\Response;

/**
 * Type of response used when an task is returned.
 */
class TaskResponse extends Response {

  /**
   * The task to be served.
   *
   * @var \Drupal\Core\Entity\EntityInterface|null
   */
  protected $task;

  /**
   * Sets the content.
   *
   * @param \Drupal\Core\Entity\EntityInterface|null $task
   *   The task to be served.
   */
  public function setTask(?EntityInterface $task): void {
    $this->task = $task;
  }

  /**
   * Gets the task to be served.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The task to be served.
   */
  public function task(): ?EntityInterface {
    return $this->task;
  }

}
