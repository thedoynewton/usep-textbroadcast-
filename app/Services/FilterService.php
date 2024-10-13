<?php

namespace App\Services;

use App\Models\College;
use App\Models\Office;
use App\Models\Program;
use App\Models\Major;
use App\Models\Year;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Type;

class FilterService
{
    // Generic method to fetch data based on a model and field
    public function fetchDataByField($model, $field, $value)
    {
        return $model::where($field, $value)->get();
    }

    public function getCollegesByCampus($campusId)
    {
        return $this->fetchDataByField(College::class, 'campus_id', $campusId);
    }

    public function getProgramsByCollege($collegeId)
    {
        return $this->fetchDataByField(Program::class, 'college_id', $collegeId);
    }

    public function getMajorsByProgram($programId)
    {
        return $this->fetchDataByField(Major::class, 'program_id', $programId);
    }

    public function getOfficesByCampus($campusId)
    {
        return $this->fetchDataByField(Office::class, 'campus_id', $campusId);
    }

    public function getTypesByOffice($officeId)
    {
        return $this->fetchDataByField(Type::class, 'office_id', $officeId);
    }

    public function getYears()
    {
        return Year::all();
    }

    public function getRecipientCount($tab, $campusId, $collegeId, $programId, $majorId, $yearId, $officeId, $typeId, $statusId)
    {
        // Initialize base queries for students and employees
        $studentsQuery = Student::query();
        $employeesQuery = Employee::query();

        // Filter by campus if a campus is selected (for both students and employees)
        if ($campusId) {
            $studentsQuery->where('campus_id', $campusId);
            $employeesQuery->where('campus_id', $campusId);
        }

        // Students filtering logic
        if ($tab === 'students') {
            if ($collegeId) {
                $studentsQuery->where('college_id', $collegeId);
            }

            if ($programId) {
                $studentsQuery->where('program_id', $programId);
            }

            if ($majorId) {
                $studentsQuery->where('major_id', $majorId);
            }

            if ($yearId) {
                $studentsQuery->where('year_id', $yearId);
            }

            return $studentsQuery->count();
        }

        // Employees filtering logic
        if ($tab === 'employees') {
            if ($officeId) {
                $employeesQuery->where('office_id', $officeId);
            }

            if ($typeId) {
                $employeesQuery->where('type_id', $typeId);
            }

            if ($statusId) {
                $employeesQuery->where('status_id', $statusId);
            }

            return $employeesQuery->count();
        }

        // For 'all' tab, sum the students and employees count
        return $studentsQuery->count() + $employeesQuery->count();
    }
}
