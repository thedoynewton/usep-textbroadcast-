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
        if ($value === null) {
            return $model::all(); // If no value is provided, return all results
        }
        return $model::where($field, $value)->get();
    }

    public function getCollegesByCampus($campusId)
    {
        return $campusId === null ? College::all() : $this->fetchDataByField(College::class, 'campus_id', $campusId);
    }

    public function getProgramsByCollege($collegeId)
    {
        return $collegeId === null ? Program::all() : $this->fetchDataByField(Program::class, 'college_id', $collegeId);
    }

    public function getMajorsByProgram($programId)
    {
        return $programId === null ? Major::all() : $this->fetchDataByField(Major::class, 'program_id', $programId);
    }

    public function getOfficesByCampus($campusId)
    {
        return $campusId === null ? Office::all() : $this->fetchDataByField(Office::class, 'campus_id', $campusId);
    }

    public function getTypesByOffice($officeId)
    {
        return $officeId === null ? Type::all() : $this->fetchDataByField(Type::class, 'office_id', $officeId);
    }

    public function getYears()
    {
        return Year::all();
    }

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
        $studentsQuery = Student::query();
        $employeesQuery = Employee::query();

        // Apply filters based on selected options, skip the filter if "all" or null
        if ($campusId !== null) {
            $studentsQuery->where('campus_id', $campusId);
            $employeesQuery->where('campus_id', $campusId);
        }

        if ($tab === 'students') {
            if ($collegeId !== null) {
                $studentsQuery->where('college_id', $collegeId);
            }

            if ($programId !== null) {
                $studentsQuery->where('program_id', $programId);
            }

            if ($majorId !== null) {
                $studentsQuery->where('major_id', $majorId);
            }

            if ($yearId !== null) {
                $studentsQuery->where('year_id', $yearId);
            }

            // Exclude students without contact numbers
            $studentsQuery->whereNotNull('stud_contact')->where('stud_contact', '!=', '');

            return $studentsQuery->count();
        }

        if ($tab === 'employees') {
            if ($officeId !== null) {
                $employeesQuery->where('office_id', $officeId);
            }

            if ($typeId !== null) {
                $employeesQuery->where('type_id', $typeId);
            }

            if ($statusId !== null) {
                $employeesQuery->where('status_id', $statusId);
            }

            // Exclude employees without contact numbers
            $employeesQuery->whereNotNull('emp_contact')->where('emp_contact', '!=', '');

            return $employeesQuery->count();
        }

        // For the "all" tab, include both students and employees
        $studentsQuery->whereNotNull('stud_contact')->where('stud_contact', '!=', '');
        $employeesQuery->whereNotNull('emp_contact')->where('emp_contact', '!=', '');

        return $studentsQuery->count() + $employeesQuery->count();
    }
}
