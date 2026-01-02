<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $events = Event::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(6)
            ->get();

        $recentServices = \App\Models\Service::orderBy('date', 'desc')
            ->limit(3)
            ->get();

        $activeCourses = \App\Models\Course::where('is_active', true)
            ->where('registration_open', true)
            ->get();

        $memberCount = \App\Models\User::where('role', 'membro')->count();

        return view('welcome', compact('events', 'recentServices', 'activeCourses', 'memberCount'));
    }
}
