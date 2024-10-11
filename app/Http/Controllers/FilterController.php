<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Office;
use App\Models\Program;
use App\Models\Major;
use App\Models\Year;
use App\Http\Controllers\Controller;
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
}

