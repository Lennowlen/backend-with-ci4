<?php

namespace App\Controllers\Swagger;

use App\Controllers\BaseController;

class SwaggerController extends BaseController
{
    public function index()
    {
        //
        return view('swagger');
        // echo "hallo";
    }
}
