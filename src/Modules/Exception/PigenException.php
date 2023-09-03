<?php

namespace Pigen\Modules\Exception;

class PigenException extends \Exception
{
  public function __construct(string $message) {
    parent::__construct($message);
  }
}
