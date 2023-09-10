<?php

namespace Pigen\Foundation\Database;

use Pigen\Foundation\Database\PDO\Connection;
use Pigen\Modules\Exception\PigenException;

class Database extends Connection
{
  /*
   | Initiating a connection with the database.
   */
  private string $table;

  public function __construct(string $table) {
    parent::__construct();

    $this->table = $table;
  }

  /*
   | A function to retrieve rows from the database.
   */
  /**
   * @throws PigenException
   */
  public function get($fields = ['*']): array|bool
  {
    try {
      return $this->execute(
        SQL::selectSQL($this->table, $fields, []),
        []
      );
    } catch (PigenException $e) {
      throw new PigenException($e->getMessage());
    }
  }
}