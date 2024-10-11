<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Program;
use App\Models\Major;
use App\Models\Year;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    // Fetch colleges for a specific campus
    public function getCollegesByCampus($campusId)
    {
        // Fetch colleges where the campus_id matches the selected campus
        $colleges = College::where('campus_id', $campusId)->get();
        return response()->json($colleges);
    }

    // Method to fetch programs based on the selected college
    public function getPrograms($collegeId)
    {
        return response()->json(Program::where('college_id', $collegeId)->get());
    }

    // Method to fetch majors based on the selected program
    public function getMajors($programId)
    {
        return response()->json(Major::where('program_id', $programId)->get());
    }

    // Method to fetch all years
    public function getYears()
    {
        return response()->json(Year::all());
    }
}
