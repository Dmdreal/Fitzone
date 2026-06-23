<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Payment;
use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GymOwnerController extends Controller
{
    public function dashboard(Request $request): View
    {
        return view('gym-owner.dashboard', [
            'owner' => $request->user(),
            'clientCount' => User::where('role', 'member')->count(),
            'trainerCount' => TrainerProfile::count(),
            'activeMemberships' => Membership::where('status', 'active')->count(),
            'paidPayments' => Payment::where('status', 'paid')->count(),
            'recentTrainers' => TrainerProfile::with('user')->latest()->take(4)->get(),
        ]);
    }
}
