<?php

namespace Pigen\Modules\Connections;

use Pigen\Foundation\Connections\Database;

class DB
{
  private static Database | null $instance = null;

  private static function getInstance()
  {
    if (self::$instance == null) {
      self::$instance = new Database();
    }
    return self::$instance;
  }

  public static function __callStatic($name, $arguments)
  {
    $instance = self::getInstance();

    return call_user_func_array([$instance, $name], $arguments);
  }
}
