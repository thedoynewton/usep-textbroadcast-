<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Student;
use App\Models\Year;
use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\MessageTemplate;
use App\Models\College;
use App\Models\Program;
use App\Models\Major;
use App\Models\Office;
use App\Models\Status;
use App\Models\Type;
use Illuminate\Support\Facades\Log;

class MessagesController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all'); // Default to 'all' if no tab is selected
        $campusId = $request->get('campus'); // Get the selected campus
    
        $campuses = Campus::all(); // Fetch all campuses from the database
        $messageTemplates = MessageTemplate::all(); // Fetch all message templates
        $years = Year::all(); // Fetch all years to populate the year dropdown
        $statuses = Status::all(); // Fetch all statuses to populate the status dropdown
    
        // Return the view with the necessary data for rendering the page
        return view('messages.index', compact('campuses', 'campusId', 'messageTemplates', 'years', 'statuses'));
    }
    
}
