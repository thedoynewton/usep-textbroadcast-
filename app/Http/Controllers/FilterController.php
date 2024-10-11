<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Office;
use App\Models\Program;
use App\Models\Major;
use App\Models\Year;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Type;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    // Generic method to fetch data based on a model and field
    protected function fetchDataByField($model, $field, $value)
    {
        return response()->json($model::where($field, $value)->get());
    }

    // Fetch colleges for a specific campus
    public function getCollegesByCampus($campusId)
    {
        return $this->fetchDataByField(College::class, 'campus_id', $campusId);
    }

    // Fetch programs for a specific college
    public function getPrograms($collegeId)
    {
        return $this->fetchDataByField(Program::class, 'college_id', $collegeId);
    }

    // Fetch majors for a specific program
    public function getMajors($programId)
    {
        return $this->fetchDataByField(Major::class, 'program_id', $programId);
    }

    // Fetch offices for a specific campus
    public function getOfficesByCampus($campusId)
    {
        return $this->fetchDataByField(Office::class, 'campus_id', $campusId);
    }

    // Fetch types for a specific office
    public function getTypesByOffice($officeId)
    {
        return $this->fetchDataByField(Type::class, 'office_id', $officeId);
    }

    // Fetch all years (no filter)
    public function getYears()
    {
        return response()->json(Year::all());
    }

    // New method for dynamically updating recipient count based on campus and tab
    public function getRecipientCount(Request $request)
    {
        $campusId = $request->get('campus');
        $tab = $request->get('tab', 'all');

        // Initialize base queries for students and employees
        $studentsQuery = Student::query();
        $employeesQuery = Employee::query();

        // Filter by campus if a campus is selected
        if ($campusId) {
            $studentsQuery->where('campus_id', $campusId);
            $employeesQuery->where('campus_id', $campusId);
        }

        // Calculate the total recipients based on the selected tab
        if ($tab === 'students') {
            $totalRecipients = $studentsQuery->count();
        } elseif ($tab === 'employees') {
            $totalRecipients = $employeesQuery->count();
        } else {
            // For 'all' tab, sum the students and employees count
            $totalRecipients = $studentsQuery->count() + $employeesQuery->count();
        }

        return response()->json(['totalRecipients' => $totalRecipients]);
    }
}
