<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\API;

if (!defined('ABSPATH')) exit;


use MailPoet\InvalidStateException;
use MailPoetVendor\Psr\Container\ContainerInterface;

class EndpointFactory {
  /** @var ContainerInterface */
  private $container;

  public function __construct(
    ContainerInterface $container
  ) {
    $this->container = $container;
  }

  public function createEndpoint(string $class): Endpoint {
    $endpoint = $this->container->get($class);
    if (!$endpoint instanceof Endpoint) {
      throw new InvalidStateException(sprintf("Class '%s' doesn't implement '%s'", $class, Endpoint::class));
    }
    return $endpoint;
  }
}
