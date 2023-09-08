<?php

namespace Pigen\Foundation\Connections;

use Pigen\Foundation\Database\SQL;
use Pigen\Foundation\Database\PDO\Connection;

class Database extends Connection
{
  protected string $table;
  protected array $columns = array('*');

  protected array $wheres = array();

  public function __construct()
  {
    parent::__construct();
  }

  public function table(string $table)
  {
    $this->table = $table;
    return $this;
  }

  public function fields(array $fields)
  {
    $this->columns = $fields;

    return $this;
  }

  public function where(string $column, string $value)
  {
    return $this->handleWhere($column, Connection::EQUALS, $value);
  }

  private function handleWhere(string $column, string $opreator, string $value)
  {
    $this->wheres[] = [
      'value' => $value,
      'column' => $column,
      'opreator' => $opreator,
      'type' => Connection::AND,
    ];

    return $this;
  }

  public function get()
  {
    $sql = SQL::selectSQL(
      $this->table,
      $this->columns,
      $this->wheres
    );

    return $this->execute(
      $sql,
      array_column(
        $this->wheres,
        'value'
      )
    );
  }
}
