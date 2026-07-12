<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function superAdmin()
    {
        return view('dashboards.super-admin');
    }

    public function admin()
    {
        return view('dashboards.admin');
    }

    public function encoder()
    {
        return view('dashboards.encoder');
    }
}