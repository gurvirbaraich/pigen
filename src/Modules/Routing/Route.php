<?php

namespace Pigen\Modules\Routing;

use Pigen\Modules\Http\Request;

class Route
{
  /**
   * Stores registered routes and associated handlers.
   * 
   * @var array 
   */
  public static array $paths = array();

  /**
   * Indicates the route that needs to be processed.
   *
   * @var array
   */
  private array $pathAttributes;

  public function __construct(array $pathAttributes = [])
  {
    if (count($pathAttributes) == 2)
      $this->pathAttributes = $pathAttributes;
  }

  public function __destruct()
  {
    if (
      isset(
        self::$paths[$this->pathAttributes['method']][$this->pathAttributes['path']]
      )
    ) {
      echo $this->invokeHandler(
        self::$paths[$this->pathAttributes['method']][$this->pathAttributes['path']]
      );
    } else {
      if (($resposne = $this->lookForVariableRoutes())) {
        echo $resposne;
        return;
      }
      

      // Check if assets are requested
      if (
        is_file(
          ($filename = ABSPATH . '/public' . $this->pathAttributes['path'])
        )
      ) {

        echo file_get_contents($filename);
        return;
      }

      echo "404 Not Found!";
    }
  }

  private function lookForVariableRoutes()
  {
    $variablePaths = $this->getVariableRoutes();

    foreach ($variablePaths as $vpath) {
      $pathRegex = '/' . preg_quote($vpath->static_path, "/") . '/m';
      preg_match_all($pathRegex, $this->pathAttributes['path'], $matches, PREG_SET_ORDER, 0);

      if (count($matches) > 0) {
        $pathVariableExtractionRegex = '/' . preg_quote($vpath->static_path, "/") . '(\w+)/m';
        $variableValue = preg_replace($pathVariableExtractionRegex, "$1", $this->pathAttributes['path']);

        if ($variableValue != $this->pathAttributes['path']) {
          Request::append($vpath->variable_path, $variableValue);

          return $this->invokeHandler(
            self::$paths[$this->pathAttributes['method']][$vpath->path]
          );
        }
      }
    }

    return null;
  }

  private function invokeHandler($handler)
  {
    $className = $handler[0];
    $classMethod = $handler[1];

    if (!class_exists($className)) {
      throw new \Exception("Class {$className} does not exist.");
    }

    $class = new $className;
    $parameters = $this->getParametersFor($class, $classMethod);

    return (
      call_user_func(
        [$class, $classMethod],
        ...$parameters
      )
    );
  }

  private function getParametersFor($className, $classMethod)
  {
    $parameters = [];
    $class = new $className();

    $parametersRequired = (new \ReflectionClass($class))
      ->getMethod($classMethod)
      ->getParameters();

    foreach ($parametersRequired as $parameter) {
      $parameterName = $parameter->getType()->getName();
      $parameters[] = new $parameterName;
    }

    return $parameters;
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

  private function getVariableRoutes()
  {
    $variablePaths = [];

    foreach (self::$paths[$this->pathAttributes['method']] as $key => $_) {
      $checkRegex = '/(.*\/):(.*)/m';
      preg_match_all($checkRegex, $key, $matches, PREG_SET_ORDER, 0);

      if (count($matches) > 0) {
        $variablePaths[] = json_decode(
          json_encode(
            [
              'path' => $matches[0][0],
              'static_path' => $matches[0][1],
              'variable_path' => $matches[0][2]
            ]
          )
        );
      }
    }

    return $variablePaths;
  }

  /**
   * Create a new instance of the Route class.
   */
  public static function ignite(array $pathAttributes)
  {
    new self($pathAttributes);
  }
}
