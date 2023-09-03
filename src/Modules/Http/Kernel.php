<?php

namespace Pigen\Modules\Http;

use Pigen\Modules\Routing\Route;

/**
 * The Kernel class serves as the central entry point for handling HTTP requests in the application.
 * It is responsible for initializing the routing system and processing incoming requests.
 */
class Kernel
{
  /**
   * Handle an incoming HTTP request.
   *
   * @param Request $request
   * @return void
   */
  public static function handle(Request $request)
  {
    // Load application routes from 'web.php' file.
    require_once ABSPATH . '/routes/web.php';

    // Initialize the routing system and process incoming requests
    Route::ignite(
      [
        'path' => $request->path,
        'method' => $request->method,
      ]
    );
  }
}
