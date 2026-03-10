<?php

namespace Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('customer::dashboard');
    }
}
