<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    //
    public static function loginPage()
    {
        return view('login');
    }
}
