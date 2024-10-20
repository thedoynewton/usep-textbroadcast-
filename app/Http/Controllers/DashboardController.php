<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
    // Use paginate instead of get and specify the number of logs per page
    $messageLogs = MessageLog::with(['user', 'campus']) // Eager load user and campus to avoid N+1 query issue
        ->orderBy('created_at', 'desc')                 // Order by latest messages
        ->paginate(10);                                 // Paginate with 10 logs per page

    return view('dashboard', compact('messageLogs'));
    }
    
}
