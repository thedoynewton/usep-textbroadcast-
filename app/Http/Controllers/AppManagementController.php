<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Student;
use App\Models\Employee;
use Illuminate\Http\Request;

class AppManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search and filter query from the request
        $search = $request->input('search');
        $filterCampus = $request->input('campus_id');
    
        // Fetch all campuses for the filter dropdown
        $campuses = Campus::all();
    
        // Fetch students and employees with pagination, optional search, and filtering
        $studentsQuery = Student::with('campus');
        $employeesQuery = Employee::with('campus');
    
        // Apply campus filter if provided
        if ($filterCampus) {
            $studentsQuery->where('campus_id', $filterCampus);
            $employeesQuery->where('campus_id', $filterCampus);
        }
    
        // Apply search filter if provided
        if ($search) {
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('stud_fname', 'like', "%{$search}%")
                      ->orWhere('stud_lname', 'like', "%{$search}%")
                      ->orWhere('stud_email', 'like', "%{$search}%");
            });
    
            $employeesQuery->where(function ($query) use ($search) {
                $query->where('emp_fname', 'like', "%{$search}%")
                      ->orWhere('emp_lname', 'like', "%{$search}%")
                      ->orWhere('emp_email', 'like', "%{$search}%");
            });
        }
    
        // Fetch the results with pagination
        $students = $studentsQuery->paginate(10); // Display 10 students per page
        $employees = $employeesQuery->paginate(10); // Display 10 employees per page
    
        // Total counts (without pagination)
        $totalStudents = $studentsQuery->count();
        $totalEmployees = $employeesQuery->count();
    
        return view('app-management.index', compact('students', 'employees', 'campuses', 'filterCampus', 'search', 'totalStudents', 'totalEmployees'));
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
