<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\MessageTemplate;
use App\Models\College;
use App\Models\Program;
use App\Models\Major;
use App\Models\Office;
use App\Models\Status;
use App\Models\Type;

class MessagesController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all campuses from the database
        $campuses = Campus::all();
    
        return view('messages.index', compact('campuses'));
    }
}
