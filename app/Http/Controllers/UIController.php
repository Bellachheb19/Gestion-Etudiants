<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UIController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function studentProfile()
    {
        return view('student.profile');
    }
}