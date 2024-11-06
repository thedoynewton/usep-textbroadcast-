<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\College;
use App\Models\Major;
use App\Models\Program;
use App\Models\Student;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataImportController extends Controller
{
    public function showImportForm()
    {
        return view('data-import.simple');
    }

    public function showDBConnection()
    {
        $campuses = Campus::all();
        return view('app-management.index', compact('campuses'));
    }

    public function importCollegeData()
    {
        $esObreroColleges = DB::connection('es_obrero')->table('vw_college_TB')->get();
        foreach ($esObreroColleges as $college) {
            College::updateOrCreate(
                ['college_id' => $college->CollegeID],
                [
                    'campus_id' => 1,
                    'college_name' => $college->CollegeName
                ]
            );
        }
        return redirect()->back()->with('success', 'Colleges imported successfully!');
    }

    public function importProgramData()
    {
        $esObreroPrograms = DB::connection('es_obrero')->table('vw_es_programs_TB')->get();
        foreach ($esObreroPrograms as $program) {
            Program::updateOrCreate(
                ['program_id' => $program->ProgID],
                [
                    'campus_id' => 1,
                    'college_id' => $program->CollegeID,
                    'program_name' => $program->ProgName
                ]
            );
        }
        return redirect()->back()->with('success', 'Programs imported successfully!');
    }

    public function importMajorData()
    {
        $esObreroMajors = DB::connection('es_obrero')->table('vw_ProgramMajors_TB')->get();
        $programIds = Program::pluck('program_id')->toArray();

        foreach ($esObreroMajors as $major) {
            $programId = in_array($major->ProgID, $programIds) ? $major->ProgID : null;

            if (!$programId) {
                $this->logMissingForeignKey('Major', $major->MajorDiscID, 'program_id', $major->ProgID);
            }

            Major::updateOrCreate(
                ['major_id' => $major->MajorDiscID],
                [
                    'campus_id' => 1,
                    'college_id' => $major->CollegeID,
                    'program_id' => $programId,
                    'major_name' => $major->Major
                ]
            );
        }
        return redirect()->back()->with('success', 'Majors imported successfully!');
    }

    public function importYearData()
    {
        $esObreroYears = DB::connection('es_obrero')->table('vw_YearLevel_TB')->get();
        foreach ($esObreroYears as $year) {
            Year::updateOrCreate(
                ['year_id' => $year->Yearlevelid],
                [
                    'year_name' => $year->Yearlevel
                ]
            );
        }
        return redirect()->back()->with('success', 'Years imported successfully!');
    }

    public function importStudentData()
    {
        $esObreroStudents = DB::connection('es_obrero')->table('vw_Students_TB')->get();
        $existingIds = $this->fetchExistingIds();

        foreach ($esObreroStudents as $student) {
            if ($this->isValidStudentData($student, $existingIds)) {
                Student::updateOrCreate(
                    ['stud_id' => $student->StudentNo],
                    [
                        'stud_fname' => $student->FirstName,
                        'stud_lname' => $student->LastName,
                        'stud_contact' => $student->MobileNo,
                        'stud_email' => $student->Email,
                        'campus_id' => 1,
                        'college_id' => $student->CollegeID,
                        'program_id' => $student->ProgID,
                        'major_id' => $student->MajorID,
                        'year_id' => $student->YearLevelID,
                        'enrollment_stat' => 'Enrolled'
                    ]
                );
            } else {
                $this->logMissingStudentData($student);
            }
        }
        return redirect()->back()->with('success', 'Students imported successfully!');
    }

    private function fetchExistingIds()
    {
        return [
            'colleges' => College::pluck('college_id')->toArray(),
            'programs' => Program::pluck('program_id')->toArray(),
            'majors' => Major::pluck('major_id')->toArray(),
            'years' => Year::pluck('year_id')->toArray(),
        ];
    }

    private function isValidStudentData($student, $existingIds)
    {
        return in_array($student->CollegeID, $existingIds['colleges']) &&
               in_array($student->ProgID, $existingIds['programs']) &&
               in_array($student->MajorID, $existingIds['majors']) &&
               in_array($student->YearLevelID, $existingIds['years']) &&
               !empty($student->Email);
    }

    private function logMissingForeignKey($entity, $entityId, $missingKey, $missingId)
    {
        Log::warning("$entity with ID '$entityId' added with NULL $missingKey due to missing $missingKey '$missingId' in related table.");
    }

    private function logMissingStudentData($student)
    {
        Log::warning("Student '{$student->FirstName} {$student->LastName}' skipped due to missing related data (college_id '{$student->CollegeID}', program_id '{$student->ProgID}', major_id '{$student->MajorID}', year_id '{$student->YearLevelID}', or email).");
    }

    public function addCampus(Request $request)
    {
        $request->validate(['campus_name' => 'required|string|max:100']);
        $campus = Campus::create(['campus_name' => $request->campus_name]);
        return response()->json(['campus' => $campus], 201);
    }

    public function updateCampus(Request $request)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,campus_id',
            'campus_name' => 'required|string|max:100'
        ]);

        $campus = Campus::findOrFail($request->campus_id);
        $campus->update(['campus_name' => $request->campus_name]);

        return response()->json(['campus' => $campus], 200);
    }
}
