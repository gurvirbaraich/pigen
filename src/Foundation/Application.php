<?php

use Pigen\Modules\Http\Kernel;

require_once ABSPATH . '/src/core/helpers.php';

/**
 * Acts as a foundation for the various modules that work 
 * in sync with the application
 */
class Application
{
  /**
   * The current version of Pigen framework.
   *
   * @var string
   */
  private $VERSION = "0.0.1";

  /**
   * A list of the workers that are needed to run the application
   *
   * @var array
   */
  public array $workers = [];

  public function __construct()
  {
    $this->workers['http'] = new Kernel();
    $this->workers['error'] = new \Pigen\Foundation\Error\Handler();

    $dotenv = Dotenv\Dotenv::createImmutable(ABSPATH);
    $dotenv->load();
  }
}

return
  $app = new Application();
