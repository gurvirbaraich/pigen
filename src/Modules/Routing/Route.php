<?php

namespace Pigen\Modules\Routing;

use Pigen\Modules\Http\Request;
use stdClass;

/**
 * Class Route
 *
 * This class handles routing and HTTP request handling.
 */
class Route
{
  /**
   * Stores registered routes and associated handlers.
   *
   * @var array
   */
  public static array $paths = [];

  /**
   * Indicates the route that needs to be processed.
   *
   * @var array
   */
  private array $pathAttributes;

  /**
   * Route constructor.
   *
   * @param array $pathAttributes An array containing route attributes (method and path).
   */
  public function __construct(array $pathAttributes = [])
  {
    if (count($pathAttributes) == 2) {
      $this->pathAttributes = $pathAttributes;
    }
  }

  /**
   * Destructor to handle the route processing.
   */
  public function __destruct()
  {
    $this->handleRoute();
  }

  /**
   * Handles the route processing.
   *
   * This method checks if the requested route matches a registered route and
   * invokes the associated handlers. If no match is found, it returns a 404
   * Not Found response.
   */
  private function handleRoute()
  {
    $handler = $this->findRouteHandler();

    if ($handler !== null) {
      echo $this->invokeHandler($handler);
    } elseif ($response = $this->lookForVariableRoutes()) {
      echo $response;
    } elseif (is_file($filename = ABSPATH . '/public' . $this->pathAttributes['path'])) {
      echo file_get_contents($filename);
    } else {
      echo "404 Not Found!";
    }
  }

  /**
   * Find the route handler based on the request.
   *
   * This method first checks for an exact match of the requested route in the
   * registered routes. If no exact match is found, it iterates through the
   * registered routes with parameters and tries to match the requested route
   * with the parameters.
   *
   * @return array|null An array containing the handler if a match is found, or null if not.
   */
  private function findRouteHandler()
  {
    $method = $this->pathAttributes['method'];
    $path = $this->pathAttributes['path'];

    // Check for an exact match
    if (isset(self::$paths[$method][$path])) {
      return self::$paths[$method][$path];
    }

    // Check for routes with parameters
    foreach (self::$paths[$method] as $route => $handlers) {
      if ($this->matchRouteWithParameters($route, $path)) {
        return $handlers;
      }
    }

    return null;
  }

  /**
   * Matches a route with parameters against the current request path.
   *
   * This method compares each part of the route and path and checks for
   * parameters (e.g., ":id") in the route. If a parameter is found, it is
   * added to the request object.
   *
   * @param string $route The registered route.
   * @param string $path The requested path.
   * @return bool True if the route matches with parameters, false otherwise.
   */
  private function matchRouteWithParameters($route, $path)
  {
    $routeParts = explode('/', $route);
    $pathParts = explode('/', $path);

    if (count($routeParts) !== count($pathParts)) {
      return false;
    }

    for ($i = 0; $i < count($routeParts); $i++) {
      if ($routeParts[$i] != $pathParts[$i] && strpos($routeParts[$i], ':') !== false) {
        // Parameter found in route, e.g., ":id"
        $paramName = ltrim($routeParts[$i], ':');
        Request::append($paramName, $pathParts[$i]);
      } elseif ($routeParts[$i] != $pathParts[$i]) {
        // Route and path parts don't match
        return false;
      }
    }

    return true;
  }

  /**
   * Look for variable routes.
   *
   * This method searches for routes with variable parts (e.g., "/posts/:id") and
   * attempts to match them with the current request path.
   *
   * @return mixed|null
   */
  private function lookForVariableRoutes()
  {
    $variablePaths = $this->getVariableRoutes();

    foreach ($variablePaths as $vpath) {
      $this->getDeepRoute($vpath);
    }

    return null;
  }

  /**
   * Handle variable routes.
   *
   * This method is a placeholder for handling routes with variable parts.
   *
   * @param $vpath
   */
  private function getDeepRoute($vpath)
  {
    if (!($vpath->static_path instanceof stdClass)) {
      $this->getVariableRouteOutput($vpath);
    }

    // Not finished implementation
  }

  /**
   * Handle variable route output.
   *
   * This method extracts values from variable parts of the route and adds them to
   * the request object. If a match is found, it invokes the associated handlers.
   *
   * @param $vpath
   */
  private function getVariableRouteOutput($vpath)
  {
    $pathRegex = '/' . preg_quote($vpath->static_path, "/") . '/m';
    preg_match_all($pathRegex, $this->pathAttributes['path'], $matches, PREG_SET_ORDER, 0);

    if (count($matches) > 0) {
      $pathVariableExtractionRegex = '/' . preg_quote($vpath->static_path, "/") . '(\w+)/m';
      $variableValue = preg_replace($pathVariableExtractionRegex, "$1", $this->pathAttributes['path']);

      if ($variableValue != $this->pathAttributes['path']) {
        Request::append($vpath->variable_path, $variableValue);

        if (isset(self::$paths[$this->pathAttributes['method']][$vpath->path])) {
          return $this->invokeHandler(self::$paths[$this->pathAttributes['method']][$vpath->path]);
        }
      }
    }
  }

  /**
   * Invoke a route handler.
   *
   * This method invokes the specified route handler by instantiating the associated
   * class and passing required parameters.
   *
   * @param $handler
   * @return mixed
   * @throws \Exception
   */
  private function invokeHandler($handler)
  {
    $className = $handler[0];
    $classMethod = $handler[1];

    if (!class_exists($className)) {
      throw new \Exception("Class {$className} does not exist.");
    }

    $class = new $className();
    $parameters = $this->getParametersFor($class, $classMethod);

    return call_user_func([$class, $classMethod], ...$parameters);
  }

  /**
   * Get parameters required for a route handler.
   *
   * This method retrieves parameters required for a route handler and instantiates
   * objects to be passed as parameters.
   *
   * @param $className
   * @param $classMethod
   * @return array
   */
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
   * Get variable routes from registered routes.
   *
   * This method searches for routes with variable parts (e.g., "/posts/:id") in
   * the registered routes for the current HTTP method. It collects and returns
   * these variable routes.
   *
   * @return array An array of variable route objects.
   */
  private function getVariableRoutes()
  {
    $variablePaths = [];

    foreach (self::$paths[$this->pathAttributes['method']] as $key => $_) {
      if ($isWildcard = $this->checkForWildcard($key)) {
        $variablePaths[] = $isWildcard;
      }
    }

    return $variablePaths;
  }


  /**
   * Check for wildcard parameters in a route path.
   *
   * This method searches for wildcard parameters (e.g., ":id") in a given route path.
   * It extracts and structures these parameters into an object if found.
   *
   * @param string $path The route path to check for wildcards.
   * @return object|null An object representing the wildcard parameters or null if none are found.
   */
  private function checkForWildcard(string $path)
  {
    $checkRegex = '/(.*\/):(\w+).*$/m';
    preg_match_all($checkRegex, $path, $matches, PREG_SET_ORDER, 0);

    if (count($matches) > 0) {
      if ($isWildcard = $this->checkForWildcard($matches[0][1])) {
        $matches[0][1] = $isWildcard;
      }

      $variablePath = json_decode(json_encode([
        'path' => $matches[0][0],
        'static_path' => $matches[0][1],
        'variable_path' => $matches[0][2]
      ]));

      return $variablePath;
    }

    return null;
  }


  /**
   * Define a GET route.
   *
   * @param string $path The path for the route.
   * @param array $handlers An array of handlers to be executed when the route is matched.
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
   */
  private static function append(string $method, string $path, array $handlers)
  {
    self::$paths[$method][$path] = $handlers;
  }

  /**
   * Create a new instance of the Route class.
   *
   * @param array $pathAttributes An array containing route attributes (method and path).
   */
  public static function ignite(array $pathAttributes)
  {
    new self($pathAttributes);
  }
}
