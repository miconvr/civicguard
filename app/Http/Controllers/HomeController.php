<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin', 'official' => redirect()->route('admin.dashboard'),
            'tanod' => redirect()->route('curfew.create'),
            default => redirect()->route('reports.create'),
        };
    }
}