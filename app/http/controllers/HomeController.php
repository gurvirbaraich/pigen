<?php

namespace Controllers;

use Pigen\Modules\Connections\DB;
use Pigen\Modules\Http\Request;
use Pigen\Modules\ViewEngine\Controller;

class HomeController extends Controller
{
  public function index()
  {
    dd(
      DB::table('users')
        ->fields(['name'])
          ->where('id', '>', 0)
            ->GET()
    );
    return $this->render('index', []);
  }

  public function singlePost(Request $request)
  {
    dd($request->all());
  }
}
