<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Student;
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
        $tab = $request->get('tab', 'all'); // Default to 'all' if no tab is selected

        $campuses = Campus::all(); // Fetch all campuses from the database
        
        // Fetch data based on the selected tab
        if ($tab === 'students') {
            $totalRecipients = Student::count();
        } elseif ($tab === 'employees') {
            $totalRecipients = Employee::count();
        } else {
            // For 'all' tab, sum the students and employees count
            $totalRecipients = Student::count() + Employee::count();
        }

        // Return view with total recipients count and other data
        return view('messages.index', compact('totalRecipients', 'campuses'));
    }

}
