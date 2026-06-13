<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShiftKaryawanController extends Controller
{
    //
    public function manageShift()
    {
        return view('app.manage.manageShift');
    }
}
