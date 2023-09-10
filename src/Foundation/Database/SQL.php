<?php
namespace Pigen\Foundation\Database;


class SQL
{
  const OR = 'OR';
  const AND = 'AND';
  const EQUALS = '=';
  const FIELDS_SEPARATOR = ',';


  public static function selectSQL($table, $columns, $where): string
  {
    $columns = join(SQL::FIELDS_SEPARATOR, $columns);
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
