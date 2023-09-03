<?php

namespace Pigen\Modules\ViewEngine;

use Jenssegers\Blade\Blade;
use Pigen\Modules\Exception\PigenException;

class View
{
  public static Blade $blade;

  public function __construct() {
    static::$blade = new Blade(
      ABSPATH . '/resources/views/',
      ABSPATH . '/cache/views'
    );
  }

  public static function compile(string $filepath, array $parameters)
  {
    $pathCompiled = ABSPATH . '/resources/views/' . $filepath . '.blade.php';

    if (!file_exists($pathCompiled)) {
      throw new PigenException("File '$filepath' does not exist");
    }

    return static::$blade->make($filepath, $parameters)->render();
  }
}