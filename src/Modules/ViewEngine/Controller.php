<?php

namespace Pigen\Modules\ViewEngine;
use Pigen\Modules\Exception\PigenException;


class Controller extends View
{
  public function __construct()
  {
    parent::__construct();
  }

  protected function render(string $filepath, array $parameters = [])
  {
    $pathCompiled = ABSPATH . '/resources/views/' . $filepath . '.blade.php';

    if (!file_exists($pathCompiled)) {
      throw new PigenException("File '$filepath' does not exist");
    }

    echo $this->blade->render($filepath, $parameters);
  }
}
