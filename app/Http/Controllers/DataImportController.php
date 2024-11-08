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
                $this->logMissingForeignKey('Major', $major->IndexID, 'program_id', $major->ProgID);
            }

            Major::updateOrCreate(
                ['major_id' => $major->IndexID],
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
        // Retrieve students data from the es_obrero database
        $esObreroStudents = DB::connection('es_obrero')->table('vw_Students_TB')->get();
    
        // Retrieve major mappings from vw_ProgramMajors_TB to match MajorID with IndexID
        $majorsMapping = DB::connection('es_obrero')
            ->table('vw_ProgramMajors_TB')
            ->pluck('IndexID', 'MajorDiscID') // Creates a map with MajorDiscID as key and IndexID as value
            ->toArray();
    
        // Retrieve program IDs from your main database for validation
        $programIds = Program::pluck('program_id')->toArray();
    
        foreach ($esObreroStudents as $student) {
            // Use a unique placeholder email if email is missing
            $email = !empty($student->Email) ? $student->Email : "noEmail{$student->StudentNo}@usep.edu.ph";
    
            // Get the correct major_id based on MajorDiscID from the mapping
            $majorId = isset($majorsMapping[$student->MajorID]) ? $majorsMapping[$student->MajorID] : null;
    
            // Check if the student's program exists
            $programId = in_array($student->ProgID, $programIds) ? $student->ProgID : null;
    
            // Log missing foreign keys if major_id or program_id is null
            if (is_null($majorId)) {
                $this->logMissingForeignKey('Student', $student->StudentNo, 'major_id', $student->MajorID);
            }
            if (is_null($programId)) {
                $this->logMissingForeignKey('Student', $student->StudentNo, 'program_id', $student->ProgID);
            }
    
            // Insert or update student data in the students table
            Student::updateOrCreate(
                ['stud_id' => $student->StudentNo],
                [
                    'stud_lname' => $student->LastName,
                    'stud_fname' => $student->FirstName,
                    'stud_mname' => null, // Or assign actual middle name if provided
                    'stud_contact' => $student->MobileNo,
                    'stud_email' => $email, // Use placeholder if missing
                    'college_id' => $student->CollegeID,
                    'program_id' => $programId,
                    'major_id' => $majorId, // Set to null if not found, avoiding foreign key conflict
                    'year_id' => $student->YearLevelID,
                    'campus_id' => 1, // Assuming a campus_id of 1, update as needed
                    'enrollment_stat' => 'active',
                ]
            );
        }
    
        return redirect()->back()->with('success', 'Students imported successfully!');
    }
    
    private function logMissingForeignKey($entity, $entityId, $missingKey, $missingId)
    {
        Log::warning("$entity with ID '$entityId' added with NULL $missingKey due to missing $missingKey '$missingId' in related table.");
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
