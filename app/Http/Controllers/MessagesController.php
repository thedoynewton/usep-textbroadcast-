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
        // Determine which tab is currently active (default to 'all')
        $tab = $request->input('tab', 'all');
        
        // Fetch common data for the form
        $campuses = Campus::all();
        $messageTemplates = MessageTemplate::all();

        // Prepare data depending on the active tab
        $data = [
            'tab' => $tab,
            'campuses' => $campuses,
            'messageTemplates' => $messageTemplates,
        ];

        // If the 'students' tab is active, prepare additional student-specific filters
        if ($tab === 'students') {
            $academicUnits = College::all();
            $programs = Program::all();
            $majors = Major::all();
            $years = range(1, 5); // Example years: 1 to 5 (or you could pull from a `Year` model)
            
            $data['academicUnits'] = $academicUnits;
            $data['programs'] = $programs;
            $data['majors'] = $majors;
            $data['years'] = $years;
        }

        // If the 'employees' tab is active, prepare additional employee-specific filters
        if ($tab === 'employees') {
            $offices = Office::all();
            $statuses = Status::all();
            $types = Type::all();

            $data['offices'] = $offices;
            $data['statuses'] = $statuses;
            $data['types'] = $types;
        }

        // Return the view with the necessary data
        return view('messages.index', $data);
    }
}
