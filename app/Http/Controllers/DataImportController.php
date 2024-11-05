<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\College;
use App\Models\Major;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class DataImportController extends Controller
{
    /**
     * Show a simple heading for the data import page.
     *
     * @return \Illuminate\View\View
     */
    public function showImportForm()
    {
        return view('data-import.simple');
    }

    public function showDBConnection()
    {
        // Fetch all campuses from the database
        $campuses = Campus::all();

        return view('app-management.index', compact('campuses'));
    }

    /**
     * Import college data from ES_Obrero database into usep-tbc database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importCollegeData()
    {
        // Fetch data from the ES_Obrero database's vw_college_TB view
        $esObreroColleges = DB::connection('es_obrero')->table('vw_college_TB')->get();

        // Insert or update data in the usep-tbc database's colleges table
        foreach ($esObreroColleges as $college) {
            College::updateOrCreate(
                ['college_name' => $college->CollegeName], // Use college_name as the unique identifier for matching
                [
                    'campus_id' => 1, // Set campus_id as appropriate
                    'college_name' => $college->CollegeName,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        return redirect()->back()->with('success', 'Colleges imported successfully!');
    }

    /**
     * Import program data from ES_Obrero database into usep-tbc database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importProgramData()
    {
        // Fetch data from ES_Obrero database's vw_es_programs_TB view
        $esObreroPrograms = DB::connection('es_obrero')->table('vw_es_programs_TB')->get();

        // Insert or update data in the usep-tbc database's programs table
        foreach ($esObreroPrograms as $program) {
            // Check if the college_id exists in the colleges table
            $collegeExists = DB::table('colleges')->where('college_id', $program->CollegeID)->exists();

            if ($collegeExists) {
                // Only insert or update if the college_id exists
                Program::updateOrCreate(
                    ['program_name' => $program->ProgName], // Use program_name or another unique field for matching
                    [
                        'campus_id' => 1, // Set campus_id as appropriate
                        'college_id' => $program->CollegeID, // Map CollegeID from ES_Obrero to college_id in usep-tbc
                        'program_name' => $program->ProgName,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            } else {
                // Log or handle programs with non-existent college_id
                // You can log this for future reference or take any other action as needed
                Log::warning("Program '{$program->ProgName}' skipped due to missing college_id '{$program->CollegeID}' in colleges table.");
            }
        }

        return redirect()->back()->with('success', 'Programs imported successfully!');
    }

    public function importMajorData()
    {
        // Fetch data from ES_Obrero database's vw_ProgramMajors_TB view
        $esObreroMajors = DB::connection('es_obrero')->table('vw_ProgramMajors_TB')->get();

        // Insert or update data in the usep-tbc database's majors table
        foreach ($esObreroMajors as $major) {
            // Check if the college_id and program_id exist in their respective tables
            $collegeExists = DB::table('colleges')->where('college_id', $major->CollegeID)->exists();
            $programExists = DB::table('programs')->where('program_id', $major->ProgID)->exists();

            if ($collegeExists && $programExists) {
                // Insert or update the major if both college_id and program_id are valid
                Major::updateOrCreate(
                    ['major_name' => $major->Major], // Use major_name or another unique field for matching
                    [
                        'campus_id' => 1, // Set campus_id as appropriate; adjust if needed
                        'college_id' => $major->CollegeID,
                        'program_id' => $major->ProgID,
                        'major_name' => $major->Major,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } else {
                // Log or handle majors with non-existent college_id or program_id
                Log::warning("Major '{$major->Major}' skipped due to missing college_id '{$major->CollegeID}' or program_id '{$major->ProgID}' in colleges or programs table.");
            }
        }

        return redirect()->back()->with('success', 'Majors imported successfully!');
    }

    public function importYearData()
    {
        // Fetch data from ES_Obrero database's vw_YearLevel_TB view
        $esObreroYears = DB::connection('es_obrero')->table('vw_YearLevel_TB')->get();
    
        // Insert or update data in the usep-tbc database's years table
        foreach ($esObreroYears as $year) {
            \App\Models\Year::updateOrCreate(
                ['year_name' => $year->Yearlevel], // Use year_name as the unique identifier
                [
                    'year_name' => $year->Yearlevel,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    
        return redirect()->back()->with('success', 'Years imported successfully!');
    }
    
    public function importStudentData()
    {
        // Fetch data from ES_Obrero database's vw_Students_TB view
        $esObreroStudents = DB::connection('es_obrero')->table('vw_Students_TB')->get();
    
        // Insert or update data in the usep-tbc database's students table
        foreach ($esObreroStudents as $student) {
            // Check for foreign key existence and non-empty email
            $collegeExists = DB::table('colleges')->where('college_id', $student->CollegeID)->exists();
            $programExists = DB::table('programs')->where('program_id', $student->ProgID)->exists();
            $majorExists = DB::table('majors')->where('major_id', $student->MajorID)->exists();
            $yearExists = DB::table('years')->where('year_id', $student->YearLevelID)->exists();
            
            if ($collegeExists && $programExists && $majorExists && $yearExists && !empty($student->Email)) {
                // Insert or update the student if all foreign keys are valid and email is present
                Student::updateOrCreate(
                    ['stud_id' => $student->StudentNo], // Match on StudentNo as unique identifier
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
                        'enrollment_stat' => 'Enrolled', // Set a default value; adjust as needed
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } else {
                // Log missing foreign keys or email
                Log::warning("Student '{$student->FirstName} {$student->LastName}' skipped due to missing college_id '{$student->CollegeID}', program_id '{$student->ProgID}', major_id '{$student->MajorID}', year_id '{$student->YearLevelID}', or email.");
            }
        }
    
        return redirect()->back()->with('success', 'Students imported successfully!');
    }
    
    // If you want to include students without emails but avoid duplicate entries, you can check for duplicate emails before inserting:
//     public function importStudentData()
// {
//     // Fetch data from ES_Obrero database's vw_Students_TB view
//     $esObreroStudents = DB::connection('es_obrero')->table('vw_Students_TB')->get();

//     // Insert or update data in the usep-tbc database's students table
//     foreach ($esObreroStudents as $student) {
//         $collegeExists = DB::table('colleges')->where('college_id', $student->CollegeID)->exists();
//         $programExists = DB::table('programs')->where('program_id', $student->ProgID)->exists();
//         $majorExists = DB::table('majors')->where('major_id', $student->MajorID)->exists();
//         $yearExists = DB::table('years')->where('year_id', $student->YearLevelID)->exists();

//         // Check for duplicate email if email is provided
//         $duplicateEmail = !empty($student->Email) && DB::table('students')->where('stud_email', $student->Email)->exists();

//         if ($collegeExists && $programExists && $majorExists && $yearExists && !$duplicateEmail) {
//             \App\Models\Student::updateOrCreate(
//                 ['stud_id' => $student->StudentNo],
//                 [
//                     'stud_fname' => $student->FirstName,
//                     'stud_lname' => $student->LastName,
//                     'stud_contact' => $student->MobileNo,
//                     'stud_email' => $student->Email,
//                     'campus_id' => 1,
//                     'college_id' => $student->CollegeID,
//                     'program_id' => $student->ProgID,
//                     'major_id' => $student->MajorID,
//                     'year_id' => $student->YearLevelID,
//                     'enrollment_stat' => 'Enrolled',
//                     'created_at' => now(),
//                     'updated_at' => now(),
//                 ]
//             );
//         } else {
//             \Log::warning("Student '{$student->FirstName} {$student->LastName}' skipped due to missing foreign keys or duplicate email '{$student->Email}'.");
//         }
//     }

//     return redirect()->back()->with('success', 'Students imported successfully!');
// }

public function addCampus(Request $request)
{
    $request->validate([
        'campus_name' => 'required|string|max:100'
    ]);

    $campus = Campus::create([
        'campus_name' => $request->campus_name
    ]);

    return response()->json(['campus' => $campus], 201);
}

public function updateCampus(Request $request)
{
    $request->validate([
        'campus_id' => 'required|exists:campuses,campus_id',
        'campus_name' => 'required|string|max:100'
    ]);

    $campus = Campus::find($request->campus_id);
    $campus->update(['campus_name' => $request->campus_name]);

    return response()->json(['campus' => $campus], 200);
}


}
