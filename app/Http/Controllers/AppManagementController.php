<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Employee;
use App\Models\Campus;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class AppManagementController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all students and employees with campus information
        $students = Student::with('campus')
                    ->when($request->search, function($query, $search) {
                        return $query->where('stud_fname', 'like', "%{$search}%")
                                     ->orWhere('stud_lname', 'like', "%{$search}%")
                                     ->orWhere('stud_email', 'like', "%{$search}%");
                    })
                    ->when($request->campus_id, function($query, $campusId) {
                        return $query->where('campus_id', $campusId);
                    })
                    ->paginate(10);

        $employees = Employee::with('campus')
                    ->when($request->search, function($query, $search) {
                        return $query->where('emp_fname', 'like', "%{$search}%")
                                     ->orWhere('emp_lname', 'like', "%{$search}%")
                                     ->orWhere('emp_email', 'like', "%{$search}%");
                    })
                    ->when($request->campus_id, function($query, $campusId) {
                        return $query->where('campus_id', $campusId);
                    })
                    ->paginate(10);

        // Fetch all message templates
        $messageTemplates = MessageTemplate::paginate(10);

        // Get total counts
        $totalStudents = Student::count();
        $totalEmployees = Employee::count();
        $campuses = Campus::all(); // For campus filter

        // Return the view with the required data
        return view('app-management.index', compact('students', 'employees', 'messageTemplates', 'totalStudents', 'totalEmployees', 'campuses'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Display the form for adding new students or employees (if needed)
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Store the new student or employee (if needed)
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Show details of a specific student or employee (if needed)
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Show the form for editing a specific student or employee (if needed)
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Update the student or employee details (if needed)
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Delete the student or employee (if needed)
    }
}
