<?php

namespace Pigen\Modules\ViewEngine;

use Jenssegers\Blade\Blade;
use Pigen\Modules\Exception\PigenException;

class View
{
  public Blade $blade;

  public function __construct() {
    $this->blade = new Blade(
      ABSPATH . '/resources/views/',
      ABSPATH . '/cache/views'
    );
  }
}