<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $events = Event::with('eventType')
            ->where(function ($q) {
                $q->whereDate('date', '>=', now()->startOfDay())
                    ->orWhereDate('end_date', '>=', now()->startOfDay());
            })
            ->orderBy('date', 'asc')
            ->limit(6)
            ->get();

        if ($events->isEmpty()) {
            $events = Event::with('eventType')->orderBy('date', 'desc')->limit(6)->get();
        }

        $publicEvents = Event::with('eventType')
            ->whereHas('eventType', function ($q) {
                $q->whereIn('name', ['Jejum e Oração', 'Jejum e oracao', 'Jejum', 'Oração', 'Oracao', 'Páscoa', 'Pascoa', 'Publico', 'Público'])
                    ->orWhere('name', 'like', '%Public%')
                    ->orWhere('name', 'like', '%Públic%')
                    ->orWhere('name', 'like', '%Jejum%')
                    ->orWhere('name', 'like', '%Ora%');
            })
            ->where(function ($q) {
                $q->whereDate('date', '>=', now()->startOfDay())
                    ->orWhereDate('end_date', '>=', now()->startOfDay());
            })
            ->orderBy('date', 'asc')
            ->limit(3)
            ->get();

        $recentServices = \App\Models\Service::orderBy('date', 'desc')
            ->limit(3)
            ->get();

        $activeCourses = \App\Models\Course::where('is_active', true)
            ->where('registration_open', true)
            ->get();

        $memberCount = \App\Models\User::where('role', 'membro')->count();
        $cellCount = \App\Models\Cell::count();
        $zoneCount = \App\Models\Zone::count();
        $campusCount = 0;

        return view('welcome', compact('events', 'publicEvents', 'recentServices', 'activeCourses', 'memberCount', 'cellCount', 'zoneCount', 'campusCount'));
    }
}
