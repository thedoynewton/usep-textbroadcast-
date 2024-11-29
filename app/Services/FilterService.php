<?php

namespace App\Services;

use App\Models\College;
use App\Models\Office;
use App\Models\Program;
use App\Models\Major;
use App\Models\Year;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Status;
use App\Models\Type;

class FilterService
{
    // Generic method to fetch data based on a model and field
    public function fetchDataByField($model, $field, $value)
    {
        return $value === null ? $model::all() : $model::where($field, $value)->get();
    }

    // Fetch colleges by campus
    public function getCollegesByCampus($campusId)
    {
        return $this->fetchDataByField(College::class, 'campus_id', $campusId)
            ->sortBy('college_name')
            ->values();
    }

    // Fetch programs by college
    public function getProgramsByCollege($collegeId)
    {
        return $this->fetchDataByField(Program::class, 'college_id', $collegeId)
            ->sortBy('program_name')
            ->values();
    }

    // Fetch majors by program
    public function getMajorsByProgram($programId)
    {
        return $this->fetchDataByField(Major::class, 'program_id', $programId)
            ->sortBy('major_name')
            ->values();
    }

    // Fetch all academic years
    public function getYears()
    {
        return Year::all();
    }

    // Fetch offices by campus
    public function getOfficesByCampus($campusId)
    {
        if ($campusId === null) {
            // Return all offices if no campus is specified
            return Office::all()->sortBy('office_name')->values();
        }

        // Fetch unique office IDs for employees belonging to the selected campus
        $officeIds = Employee::where('campus_id', $campusId)
            ->pluck('office_id')
            ->unique();

        // Fetch and return offices corresponding to those office IDs
        return Office::whereIn('office_id', $officeIds)
            ->get()
            ->sortBy('office_name')
            ->values();
    }

    // Fetch types by office
    public function getTypesByOffice($officeId)
    {
        if ($officeId === null) {
            // Return all types if no office is specified
            return Type::all()->sortBy('type_name')->values();
        }

        // Fetch unique type IDs from employees associated with the selected office
        $typeIds = Employee::where('office_id', $officeId)
            ->pluck('type_id')
            ->unique();

        // Fetch types corresponding to those type IDs
        return Type::whereIn('type_id', $typeIds)
            ->get()
            ->sortBy('type_name')
            ->values();
    }

    // public function getStatuses()
    // {
    //     // Fetch and sort statuses alphabetically by status_name
    //     return Status::orderBy('status_name', 'asc')->get()->values();
    // }    

    // Get recipient count
    public function getRecipientCount(
        $tab,
        $campusId = null,
        $collegeId = null,
        $programId = null,
        $majorId = null,
        $yearId = null,
        $officeId = null,
        $typeId = null,
        $statusId = null
    ) {
        $query = $tab === 'students' ? Student::query() : Employee::query();

        // Apply filters dynamically
        $filters = [
            'campus_id' => $campusId,
            'college_id' => $tab === 'students' ? $collegeId : null,
            'program_id' => $tab === 'students' ? $programId : null,
            'major_id' => $tab === 'students' ? $majorId : null,
            'year_id' => $tab === 'students' ? $yearId : null,
            'office_id' => $tab === 'employees' ? $officeId : null,
            'type_id' => $tab === 'employees' ? $typeId : null,
            'status_id' => $tab === 'employees' ? $statusId : null,
        ];

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        // Exclude records without contact numbers
        $contactField = $tab === 'students' ? 'stud_contact' : 'emp_contact';
        $query->whereNotNull($contactField)->where($contactField, '!=', '');

        // Exclude inactive students
        if ($tab === 'students') {
            $query->where('enrollment_stat', 'active');
        }

        // Exclude inactive employees
        if ($tab === 'employees') {
            $query->where('is_active', 1);
        }

        // Exclude both students and employees if the tab is 'all'
        if ($tab === 'all') {
            $query->where(function ($query) {
                $query->where('enrollment_stat', '!=', 'active')
                    ->orWhere('is_active', '!=', 1);
            });
        }

        return $query->count();
    }
}
