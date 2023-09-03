<?php

namespace Pigen\Modules\Routing;

class Route
{
  /**
   * Stores registered routes and associated handlers.
   * 
   * @var array 
   */
  private static array $paths = array();

  /**
   * Indicates the route that needs to be processed.
   *
   * @var array
   */
  private array $pathAttributes;

  public function __construct(array $pathAttributes)
  {
    $this->pathAttributes = $pathAttributes;
  }

  public function __destruct()
  {
    if (
      isset(
        self::$paths[$this->pathAttributes['method']][$this->pathAttributes['path']]
      )
    ) {
      $handler = self::$paths[$this->pathAttributes['method']][$this->pathAttributes['path']];

      $className = $handler[0];
      $classMethod = $handler[1];

      $class = new $className();
      echo call_user_func([$class, $classMethod]);
    } else {
      echo '404. Not found';
    }
  }

  /**
   * Define a GET route.
   *
   * @param string $path The path for the route.
   * @param array $handlers An array of handlers to be executed when the route is matched.
   * @return void
   */
  public static function GET(string $path, array $handlers)
  {
    self::append("GET", $path, $handlers);
  }

  /**
   * Define a PUT route.
   *
   * @param string $path The path for the route.
   * @param array $handlers An array of handlers to be executed when the route is matched.
   * @return void
   */
  public static function PUT(string $path, array $handlers)
  {
    self::append("PUT", $path, $handlers);
  }

  /**
   * Define a POST route.
   *
   * @param string $path The path for the route.
   * @param array $handlers An array of handlers to be executed when the route is matched.
   * @return void
   */
  public static function POST(string $path, array $handlers)
  {
    self::append("POST", $path, $handlers);
  }

  /**
   * Define a PATCH route.
   *
   * @param string $path The path for the route.
   * @param array $handlers An array of handlers to be executed when the route is matched.
   * @return void
   */
  public static function PATCH(string $path, array $handlers)
  {
    self::append("PATCH", $path, $handlers);
  }

  /**
   * Define a DELETE route.
   *
   * @param string $path The path for the route.
   * @param array $handlers An array of handlers to be executed when the route is matched.
   * @return void
   */
  public static function DELETE(string $path, array $handlers)
  {
    self::append("DELETE", $path, $handlers);
  }

  /**
   * Define an OPTIONS route.
   *
   * @param string $path The path for the route.
   * @param array $handlers An array of handlers to be executed when the route is matched.
   * @return void
   */
  public static function OPTIONS(string $path, array $handlers)
  {
    self::append("OPTIONS", $path, $handlers);
  }

  /**
   * Append a route to the list of registered routes.
   *
   * @param string $method The HTTP method for the route.
   * @param string $path The path for the route.
   * @param array $handlers An array of handlers to be executed when the route is matched.
   * @return void
   */
  private static function append(string $method, string $path, array $handlers)
  {
    self::$paths[$method][$path] = $handlers;
  }

  /**
   * Create a new instance of the Route class.
   */
  public static function ignite(array $pathAttributes)
  {
    new self($pathAttributes);
  }
}
