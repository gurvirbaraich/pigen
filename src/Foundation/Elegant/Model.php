<?php

namespace Pigen\Foundation\Elegant;

use Pigen\Foundation\Database\Database;

/**
 * @method static get(array $fields = ['*'])
 */
class Model {
  private static ?Database $database = null;

  public static function __callStatic(string $name, array $arguments)
  {
    $instance = self::getInstance();

    return $instance->$name(...$arguments);
  }

  private static function getInstance(): Database
  {
    if (self::$database === null) {
      self::$database = new Database(
        self::computeTableName()
      );
    }

    return self::$database;
  }

  private static function computeTableName(): string
  {
    $class = get_called_class();
    return strtolower(preg_replace('/.*\\\\(.*)/m', '$1s', $class));
  }
}