<?php

namespace Drupal\graphql_composable\GraphQL\Response;

use Drupal\Core\Entity\EntityInterface;
use Drupal\graphql\GraphQL\Response\Response;

/**
 * Type of response used when an quote is returned.
 */
class QuoteResponse extends Response {

  /**
   * The quote to be served.
   *
   * @var \Drupal\Core\Entity\EntityInterface|null
   */
  protected $quote;

  /**
   * Sets the content.
   *
   * @param \Drupal\Core\Entity\EntityInterface|null $quote
   *   The quote to be served.
   */
  public function setQuote(?EntityInterface $quote): void {
    $this->quote = $quote;
  }

  /**
   * Gets the quote to be served.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The quote to be served.
   */
  public function quote(): ?EntityInterface {
    return $this->quote;
  }

}
