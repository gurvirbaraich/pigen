<?php
namespace Pigen\Foundation\Database;

use Pigen\Foundation\Database\PDO\Connection;

class SQL
{
  public static function selectSQL($table, $columns, $where)
  {
    $columns = join(Connection::FIELDS_SEPERATOR, $columns);
    $sql = 'SELECT ' . $columns . ' FROM ' . $table;

    if (!empty($wheres)) {
      $sql .= ' WHERE ';

      foreach ($wheres as $index => $where) {
        if ($index > 0) {
          $sql .= $where['type'] . ' ';
        }

        $sql .= $where['column'] . ' ' . $where['opreator'] . ' ?';
      }
    }

    return $sql;
  }
}
