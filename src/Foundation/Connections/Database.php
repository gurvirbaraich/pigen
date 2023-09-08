<?php

namespace Pigen\Foundation\Connections;

use PDO;
use Pigen\Modules\Exception\PigenException;

class Database
{
  private array $types = [
    'AND',
    'OR'
  ];

  private array $opreators = [
    '=',       //	Equal to
    '>',       // Greater Than
    '<>',      //	Not Equal to
    'LIKE',    //	Search for a pattern
    'BETWEEN', //	In an inclusive Range
    'IN',      //	To specify multiple possible values for a column
  ];

  protected PDO $pdo;

  protected string $table;
  protected string $columns = '*';

  protected array $wheres = [];

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function table(string $table)
  {
    $this->table = $table;
    return $this;
  }

  public function fields(array $fields)
  {
    $this->columns = join(",", $fields);
    return $this;
  }

  public function where(string $column, string $opreator, string $value)
  {
    if (
      !(in_array($opreator, $this->opreators))
    ) {
      throw new PigenException("Unsupported opreator: " . $opreator);
    }

    $this->wheres[] = [
      'type' => 'AND',
      'value' => $value,
      'column' => $column,
      'opreator' => $opreator,
    ];

    return $this;
  }

  public function get()
  {
    $sql = $this->finalizeSQL();
    $stmt = $this->pdo->prepare($sql);

    $bindedValues = array_column($this->wheres, 'value');
    $stmt->execute($bindedValues);

    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  private function finalizeSQL()
  {
    $sql = 'SELECT ' . $this->columns . ' FROM ' . $this->table;

    if (!empty($this->wheres)) {
      $sql .= ' WHERE ';

      foreach ($this->wheres as $index => $where) {
        if ($index > 0) {
          $sql .= $where['type'] . ' ';
        }

        $sql .= $where['column'] . ' ' . $where['opreator'] . ' ?';
      }
    }

    return $sql;
  }
}
