<?php

namespace Pigen\Foundation\CommandLine;

use \Symfony\Component\Console\Application;
use Pigen\Foundation\CommandLine\Commands\InsultCommand;
use Pigen\Foundation\CommandLine\Commands\Routes\ListCommand;
use Pigen\Foundation\CommandLine\Commands\ServeCommand;

class Kernel
{
  private static $application;

  private static array $commands = [
    ListCommand::class,
    ServeCommand::class,
    InsultCommand::class,
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
