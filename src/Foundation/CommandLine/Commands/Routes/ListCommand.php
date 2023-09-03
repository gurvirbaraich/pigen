<?php

namespace Pigen\Foundation\CommandLine\Commands\Routes;

use Pigen\Modules\Routing\Route;

use Symfony\Component\Console\Terminal;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'route:list')]
class ListCommand extends Command
{
  private $methodsAllowed = [
    "GET",
    "POST"
  ];

  protected $description = "List all registered routes";

  protected $colors = [
    'ANY' => 'red',
    'GET' => 'blue',
    'HEAD' => '#6C7280',
    'OPTIONS' => '#6C7280',
    'POST' => 'yellow',
    'PUT' => 'yellow',
    'PATCH' => 'yellow',
    'DELETE' => 'red',
  ];

  protected function configure()
  {
    $this->setDescription($this->description);
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    // Load all the registered routes.
    require_once ABSPATH . '/routes/web.php';

    // Display all the loaded routes.
    return $this->displayRoutes($output);
  }

  private function getTerminalWidth()
  {
    return (new Terminal())->getWidth();
  }

  private function displayRoutes(OutputInterface $output): int
  {
    $routes = $this->getAllRoutes();

    foreach ($routes as $route) {
      $output->writeln(
        sprintf(
          "%s %s <fg=" . $this->colors["HEAD"] . ">%s %s</>",

          $route['method'],
          $route['path'],
          str_repeat(".", $route['dots']),
          $route['caller']
        )
      );
    }

    return 0;
  }

  private function getAllRoutes(): array
  {
    $routes = array();
    $routesLoaded = Route::$paths;

    $terminalWidth = $this->getTerminalWidth();

    foreach ($this->methodsAllowed as $method) {
      if (isset($routesLoaded[$method])) {
        foreach ($routesLoaded[$method] as $key => $_) {
          $width = $terminalWidth;

          $caller = $_[0] . ' › ' . $_[1];
          $m = $method . str_repeat(' ', mb_strlen('       ') - strlen($method));

          $routes[] = [
            'method' =>  !($width = $terminalWidth - mb_strlen($m)) ?: "<fg=" . $this->colors[$method] . ";options=bold>$m</>",
            'path' => $key,
            'caller' => !($width = $width - 2 - mb_strlen($caller)) ?: $caller,
            'dots' => $width - mb_strlen($key . " \t")
          ];
        }
      }
    }

    return $routes;
  }
}
