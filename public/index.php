<?php

/**
 * @author Gurvir Singh <baraichgurvir@gmail.com>
 * @license MIT
 */

use Pigen\Modules\Http\Request;

/*
 | --------------------------------------------------------------------
 | Register The Auto Loader
 | --------------------------------------------------------------------
 |
 | Composer provides a convenient, automatically generated class loader
 | We'll simply need to require it into the script so we don't need to
 | worry about manual loading any of our classes later on.
 |
 */

require __DIR__ . '/../vendor/autoload.php';

/*
 | ----------------------------------------------------------------
 | Define Global Constants
 | ----------------------------------------------------------------
 | 
 | These constants are used throughout the application. They are
 | defined here so that we don't have to repeat them throughout
 | the application.
 |
 */

define("ABSPATH", dirname(__DIR__));

/*
 | --------------------------------------------------------------------
 | Running the Application
 | --------------------------------------------------------------------
 |
 | Once application is loaded, it will serve as the entry point for all
 | HTTP the requests. Finally, the application will send the response
 | back to the client's browser.
 |
 */

$app = require ABSPATH . '/src/Foundation/Application.php';


$app
  ->workers['http']
  ->handle(Request::capture());
