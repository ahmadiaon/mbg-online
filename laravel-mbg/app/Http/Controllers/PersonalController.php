<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PersonalController extends Controller
{
    //
    public function Profile(){
        return view('app.personal.profile');
    }
    public function User(){
        return view('app.personal.user');
    }
    public function Menu(){
        return view('app.personal.menu');
    }
}
