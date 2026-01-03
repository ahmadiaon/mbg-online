<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralRouteController extends Controller
{
    public function indexMenu()
    {
        return view('app.general.menuIndex');
    }
    public function authentication()
    {
        if (session()->missing('auth_token')) {
            return redirect()->intended('/auth/login#TOKEN-FAILED');
        }
        return view('app.general.authentication');
    }

    public function haulingTimerCek()
    {
        return view('hauling.timerCek');
    }


    public function managePayrollSlip()
    {
        return view('app.payroll.manageSlip');
    }
    public function manageRecruitment()
    {
        // dd(session()->all());
        return view('app.manage.manageRecruitment');
    }

    public function mySlip(){
        return view('app.personal.mySlip');        
    }
}
