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
  /* The `private ` variable is an array that stores the HTTP methods that are allowed
  for the routes. In this case, it includes "GET" and "POST" methods. These methods are used later
  in the code to filter and display only the routes that have these methods. */
  private $methodsAllowed = [
    "GET",
    "POST"
  ];

  /* Ssetting the description property of the `ListCommand` class to the string "List all registered routes". This description
  is used to provide information about the command when it is displayed in the command line interface. */
  protected $description = "List all registered routes";

  /* The `protected ` variable is an array that maps HTTP methods to their corresponding colors.
  Each key-value pair in the array represents an HTTP method and its associated color. */
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

  /**
   * The function sets the description for a PHP command.
   */
  protected function configure()
  {
    $this->setDescription($this->description);
  }

  /**
   * The function executes the web.php file and then displays the routes.
   * 
   * @param InputInterface input The `` parameter is an instance of the `InputInterface` class.
   * It represents the input that is passed to the command when it is executed. It provides methods to
   * access the command arguments and options.
   * @param OutputInterface output The `` parameter is an instance of the `OutputInterface`
   * class. It represents the output stream and provides methods for writing output to the console. You
   * can use methods like `writeln()` or `write()` to display text or information to the user.
   * 
   * @return int The method is returning an integer value.
   */
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    /* 
     * The line `require_once ABSPATH . '/routes/web.php';` is including the `web.php` file, which
     * contains the route definitions for the application. This file is required so that the
     * `ListCommand` class can access and display the registered routes. 
     */
    require_once ABSPATH . '/routes/web.php';

    return $this->displayRoutes($output);
  }

  private function getTerminalWidth(): int
  {
    return (new Terminal())->getWidth();
  }

  /**
   * The function displays all routes with their method, path, dots, and caller.
   * 
   * @param OutputInterface output The  parameter is an instance of the OutputInterface class. It
   * is used to write output to the console or other output streams.
   * 
   * @return int an integer value of 0.
   */
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

  /**
   * The function getAllRoutes() retrieves all routes and their corresponding methods, paths, and
   * callers.
   * 
   * @return array an array of routes. Each route is represented as an associative array with the
   * following keys: 'method', 'path', 'caller', and 'dots'.
   */
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
