<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return redirect()->route('member.dashboard');
    }

    public function admin(): View
    {
        return view('admin.dashboard');
    }

    public function trainer(): View
    {
        return view('trainer.dashboard');
    }

    public function member(): View
    {
        return view('member.dashboard');
    }
}
