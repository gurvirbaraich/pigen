<?php

namespace Controllers;

use Models\User;
use Pigen\Modules\Http\Request;
use Pigen\Modules\ViewEngine\Controller;


class HomeController extends Controller
{
  public function index(): string
  {
    return $this->render('table', array(
      'data' => User::get()
    ));
  }

  public function singlePost(Request $request)
  {
    dd($request->all());
  }
}
