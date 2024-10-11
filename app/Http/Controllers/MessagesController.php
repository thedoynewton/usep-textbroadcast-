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

        // Initialize base queries for students and employees
        $studentsQuery = Student::query();
        $employeesQuery = Employee::query();

        // Filter by campus if a campus is selected
        if ($campusId) {
            $studentsQuery->where('campus_id', $campusId);
            $employeesQuery->where('campus_id', $campusId);
            $colleges = College::where('campus_id', $campusId)->get(); // Fetch only colleges for the selected campus

            // Log the colleges being populated
            Log::info('Colleges being populated for Campus ID: ' . $campusId, [
                'colleges' => $colleges->pluck('college_name')->toArray(),
            ]);
        } else {
            $colleges = College::all(); // Fetch all colleges if no campus is selected
            Log::info('No campus selected. Displaying all colleges.');
        }

        // Fetch data based on the selected tab
        if ($tab === 'students') {
            $totalRecipients = $studentsQuery->count();
        } elseif ($tab === 'employees') {
            $totalRecipients = $employeesQuery->count();
        } else {
            // For 'all' tab, sum the students and employees count
            $totalRecipients = $studentsQuery->count() + $employeesQuery->count();
        }

        // Return view with total recipients count, colleges, years, and other data
        return view('messages.index', compact('totalRecipients', 'campuses', 'campusId', 'messageTemplates', 'colleges', 'years', 'statuses'));
    }
}
