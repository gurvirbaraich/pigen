<?php
namespace Pigen\Foundation\Error;

class Handler
{
  public function __construct()
  {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
  }
}
