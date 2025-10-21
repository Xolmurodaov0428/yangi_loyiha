<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApiToken;

class ApiTokenController extends Controller
{
    /**
     * Display API tokens
     */
    public function index()
    {
        $tokens = ApiToken::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.api-tokens.index', compact('tokens'));
    }

    /**
     * Store new API token
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $token = ApiToken::generate(
            $request->name,
            auth()->id(),
            $request->expires_at
        );

        return redirect()->route('admin.api-tokens.index')
            ->with('success', 'API token yaratildi')
            ->with('new_token', $token->token);
    }

    /**
     * Toggle token status
     */
    public function toggle($id)
    {
        $token = ApiToken::findOrFail($id);
        $token->update(['is_active' => !$token->is_active]);

        return redirect()->route('admin.api-tokens.index')
            ->with('success', 'Token holati o\'zgartirildi');
    }

    /**
     * Delete token
     */
    public function destroy($id)
    {
        $token = ApiToken::findOrFail($id);
        $token->delete();

        return redirect()->route('admin.api-tokens.index')
            ->with('success', 'Token o\'chirildi');
    }
}
