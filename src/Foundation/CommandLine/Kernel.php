<?php

namespace Pigen\Foundation\CommandLine;

use \Symfony\Component\Console\Application;
use Pigen\Foundation\CommandLine\Commands\InsultCommand;

class Kernel
{
  private static $application;

  private static array $commands = [
    InsultCommand::class
  ];

  public function __construct()
  {
    static::$application = new Application();

    foreach (static::$commands as $class) {
      if (!class_exists($class)) {
        throw new \RuntimeException(
          sprintf('Class "%s" does not exist', $class)
        );
      }

      $command = new $class();
      static::$application->add($command);
    }
  }

  public static function handle()
  {
    static::$application->run();
  }
}

new Kernel();
