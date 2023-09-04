<?php

namespace Controllers;

use Pigen\Modules\Http\Request;
use Pigen\Modules\ViewEngine\Controller;

class HomeController extends Controller
{
  public function index(Request $request)
  {
    dd($request->all());
    $this->render('index', []);
  }
}
