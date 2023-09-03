<?php

namespace Pigen\Modules\ViewEngine;

class Controller extends View
{
  public function __construct()
  {
    parent::__construct();
  }

  protected function render(string $filepath, array $parameters = [])
  {
    echo static::compile($filepath, $parameters);
  }
}
