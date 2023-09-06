<?php

namespace Controllers;

use Pigen\Modules\Http\Request;
use Pigen\Modules\ViewEngine\Controller;

class HomeController extends Controller
{
  public function index()
  {
    return $this->render('index', []);
  }

  public function singlePost(Request $request) {
    dd($request->all());
  }
}
