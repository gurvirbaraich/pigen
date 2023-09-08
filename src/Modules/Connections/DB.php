<?php

namespace Pigen\Modules\Connections;

use Pigen\Foundation\Connections\Database;

class DB
{
  private static $instance = null;

  private static function getInstance()
  {
    if (self::$instance == null) {
      

      $options = array(
        \PDO::MYSQL_ATTR_SSL_CA => "/etc/ssl/cert.pem",
      );

      $pdo = new \PDO($dsn, $username, $password, $options);
      self::$instance = new Database($pdo);
    }
    return self::$instance;
  }

  public static function __callStatic($name, $arguments)
  {
    $instance = self::getInstance();

    return call_user_func_array([$instance, $name], $arguments);
  }
}
