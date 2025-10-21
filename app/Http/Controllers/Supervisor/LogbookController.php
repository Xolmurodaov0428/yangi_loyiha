<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Logbook;

class LogbookController extends Controller
{
    public function index()
    {
        $logbooks = Logbook::with('student')
            ->whereHas('student', fn($q) => $q->where('supervisor_id', auth()->id()))
            ->latest()
            ->paginate(20);

        return view('supervisor.logbooks', compact('logbooks'));
    }

    public function approve(Logbook $logbook)
    {
        $logbook->update(['status' => 'approved']);
        return back()->with('success', 'Kundalik tasdiqlandi');
    }

    public function reject(Logbook $logbook)
    {
        $logbook->update(['status' => 'rejected']);
        return back()->with('success', 'Kundalik rad etildi');
    }
}
