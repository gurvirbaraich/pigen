<?php

namespace Pigen\Modules\ViewEngine;

use stdClass;
use Jenssegers\Blade\Blade;
use Pigen\Modules\Exception\PigenException;

class View
{
  public Blade $blade;

  public function __construct()
  {
    $this->blade = new Blade(
      ABSPATH . '/resources/views/',
      ABSPATH . '/cache/views'
    );
  }

  public function render(string $filepath, array $parameters = [])
  {
    $pathCompiled = ABSPATH . '/resources/views/' . $filepath . '.blade.php';

    if (!file_exists($pathCompiled)) {
      throw new PigenException("File '$filepath' does not exist");
    }

    return $this->blade->render($filepath, $parameters);
  }
}
