<?php

namespace Pigen\Modules\Http;

/**
 * The Request call is designed to provide a convenient interface for accessing
 * request parameters, such as GET and POST data.
 * 
 */
class Request
{
  /**
   * Stores the request parameters
   *
   * @var array
   */
  private static array $parameters = [];

  private array $properties = [];

  /**
   * Automatically loads the request parameters based on the 
   * HTTP request method (GET or POST)
   */
  public function __construct()
  {
    match ($_SERVER['REQUEST_METHOD']) {
      'GET' => $this->load($_GET),
      'POST' => $this->load($_POST),
    };

    $this->populateProperties();
  }

  private function populateProperties()
  {
    $this->properties = [
      'method' => $_SERVER['REQUEST_METHOD'],
      'path' => preg_replace('/(\/.*)\?.*/m', "$1", $_SERVER['REQUEST_URI']),
    ];
  }

  /**
   * Loads the request parameters into the class's parameters array
   *
   * @param array $data
   * @return void
   */
  private function load($data)
  {
    foreach ($data as $key => $value) {
      self::$parameters[$key] = $value;
    }
  }

  /**
   * Magic method to access request parameters by their names.
   *
   * @param string $name
   * @return mixed 
   */
  public function __get($name)
  {
    return self::$parameters[$name] ?? $this->properties[$name];
  }

  /**
   * Magic method to update the value of request parameters
   *
   * @return void
   */
  public function __set(string $name, $value): void
  {
    self::$parameters[$name] = $value;
  }

  /**
   * @return array 
   */
  public function all(): array
  {
    return self::$parameters;
  }

  /**
   * Creates a new instance of the Request object
   */
  public static function capture()
  {
    return new self();
  }

  public static function append($name, $value) {
    self::$parameters[$name] = $value;
  }
}
