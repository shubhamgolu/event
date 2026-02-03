<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    // Add constructor to apply auth middleware
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = Auth::user();
        
        // Manual admin check (temporary)
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized access. Admin only.');
        }
        
        return view('admin.dashboard', [
            'user' => $user,
        ]);
    }
}