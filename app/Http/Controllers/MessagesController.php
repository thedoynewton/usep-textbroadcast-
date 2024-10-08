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
        $campusId = $request->get('campus'); // Get the selected campus
    
        $campuses = Campus::all(); // Fetch all campuses from the database
        
        // Initialize base queries for students and employees
        $studentsQuery = Student::query();
        $employeesQuery = Employee::query();
    
        // Filter by campus if a campus is selected
        if ($campusId) {
            $studentsQuery->where('campus_id', $campusId);
            $employeesQuery->where('campus_id', $campusId);
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
    
        // Return view with total recipients count and other data
        return view('messages.index', compact('totalRecipients', 'campuses', 'campusId'));
    }
    

}
