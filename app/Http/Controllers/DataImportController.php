<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\College;
use App\Models\Major;
use App\Models\Program;
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
    
}
