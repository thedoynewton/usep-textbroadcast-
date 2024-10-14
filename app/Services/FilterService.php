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

    public function getRecipientCount(
        $tab,
        $campusId,
        $collegeId = 'all',
        $programId = 'all',
        $majorId = 'all',
        $yearId = 'all',
        $officeId = 'all',
        $typeId = 'all',
        $statusId = 'all'
    ) {
        $studentsQuery = Student::query();
        $employeesQuery = Employee::query();

        // Check if "All" is selected and skip the filter if so
        if ($campusId !== 'all') {
            $studentsQuery->where('campus_id', $campusId);
            $employeesQuery->where('campus_id', $campusId);
        }

        if ($tab === 'students') {
            if ($collegeId !== 'all') {
                $studentsQuery->where('college_id', $collegeId);
            }

            if ($programId !== 'all') {
                $studentsQuery->where('program_id', $programId);
            }

            if ($majorId !== 'all') {
                $studentsQuery->where('major_id', $majorId);
            }

            if ($yearId !== 'all') {
                $studentsQuery->where('year_id', $yearId);
            }

            return $studentsQuery->count();
        }

        if ($tab === 'employees') {
            if ($officeId !== 'all') {
                $employeesQuery->where('office_id', $officeId);
            }

            if ($typeId !== 'all') {
                $employeesQuery->where('type_id', $typeId);
            }

            if ($statusId !== 'all') {
                $employeesQuery->where('status_id', $statusId);
            }

            return $employeesQuery->count();
        }

        // For the "all" tab, sum the students and employees count
        return $studentsQuery->count() + $employeesQuery->count();
    }

}
