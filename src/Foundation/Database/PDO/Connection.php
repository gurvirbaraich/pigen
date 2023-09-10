<?php

namespace Pigen\Foundation\Database\PDO;

use PDO;
use Pigen\Modules\Exception\PigenException;
use stdClass;

class Connection
{

  protected PDO $connection;
  private stdClass $environment;

  public function __construct()
  {
    $this->environment = arrayToObject(
      [
        'host' => $_ENV['DB_HOST'],
        'username' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASS'],
        'database' => $_ENV['DB_NAME'],
      ]
    );

    $this->environment->options = [
      \PDO::MYSQL_ATTR_SSL_CA => "/etc/ssl/cert.pem",
    ];

    $this->connect();
  }

  private function connect(): void
  {
    $this->connection = new PDO(
      concatenate(
        "mysql:host=",
        $this->environment->host,
        ";dbname=",
        $this->environment->database
      ),

      $this->environment->username,
      $this->environment->password,
      $this->environment->options
    );
  }

  protected function prepare($sql): bool|\PDOStatement
  {
    return $this->connection->prepare($sql);
  }

  /**
   * @throws PigenException
   */
  protected function execute($sql, $values): bool|array
  {
    $stmt = $this->prepare($sql);

    if (
      $stmt->execute(
        $values
      )
    ) {
      return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    throw new PigenException(
      $stmt->errorInfo()[2]
    );
  }
}
