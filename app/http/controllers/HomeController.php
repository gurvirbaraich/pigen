<?php

namespace Controllers;

use Pigen\Modules\Http\Request;
use Pigen\Modules\ViewEngine\Controller;

class HomeController extends Controller
{
  public function index()
  {
    $this->render('index', []);
  }

  public function singlePost(Request $request) {
    $this->render('single-post', [
      'postId' => $request->id
    ]);
  }
}
