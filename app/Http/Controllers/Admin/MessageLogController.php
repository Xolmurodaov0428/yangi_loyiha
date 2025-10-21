<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageLog;
use Illuminate\Http\Request;

class MessageLogController extends Controller
{
    /**
     * Display message logs
     */
    public function index(Request $request)
    {
        $query = MessageLog::with(['user', 'message.conversation.student'])
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->has('action') && $request->action != 'all') {
            $query->where('action', $request->action);
        }

        // Filter by user type
        if ($request->has('user_type') && $request->user_type != 'all') {
            $query->where('user_type', $request->user_type);
        }

        // Search by user name or message content
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('old_content', 'like', "%{$search}%")
                  ->orWhere('new_content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(50);

        return view('admin.message-logs.index', compact('logs'));
    }

    /**
     * Show single log details
     */
    public function show($id)
    {
        $log = MessageLog::with(['user', 'message.conversation.student', 'message.conversation.supervisor'])
            ->findOrFail($id);

        return view('admin.message-logs.show', compact('log'));
    }
}
