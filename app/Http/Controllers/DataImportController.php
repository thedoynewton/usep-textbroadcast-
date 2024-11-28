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


    public function importCollegeData(Request $request)
    {
        // Validate the request to ensure campus_id is provided and valid
        $request->validate([
            'campus_id' => 'required|integer|exists:campuses,campus_id',
        ]);
    
        // Define mapping of campus_id to database connections
        $campusConnections = [
            1 => 'es_obrero',
            2 => 'es_tagum',
            3 => 'es_mintal',
            4 => 'es_mabini',
        ];
    
        // Get the campus_id from the request
        $campusId = $request->input('campus_id');
    
        // Retrieve the database connection for the given campus_id
        $databaseConnection = $campusConnections[$campusId] ?? null;
    
        // If no database connection is found for the given campus_id, return an error
        if (!$databaseConnection) {
            return redirect()->back()->with('error', 'Invalid campus selected!');
        }
    
        try {
            // Fetch college data from the campus-specific database
            $colleges = DB::connection($databaseConnection)->table('vw_college_TB')->get();
    
            if ($colleges->isEmpty()) {
                return redirect()->back()->with('error', 'No colleges found in the selected campus database.');
            }
    
            // Initialize counters for inserted and updated records
            $inserted = 0;
            $updated = 0;
    
            foreach ($colleges as $college) {
                // Check if a record with the same college_id and campus_id already exists
                $existingCollege = College::where('college_id', $college->CollegeID)
                                          ->where('campus_id', $campusId)
                                          ->first();
    
                if (!$existingCollege) {
                    // If no existing record found, insert a new one
                    $newCollege = new College([
                        'campus_id' => $campusId,
                        'college_id' => $college->CollegeID,
                        'college_name' => $college->CollegeName,
                    ]);
                    $newCollege->save();
                    $inserted++;
                } else {
                    // If a record exists with the same college_id and campus_id, update it
                    $existingCollege->update([
                        'college_name' => $college->CollegeName,
                        'updated_at' => now(),
                    ]);
                    $updated++;
                }
            }
    
            return redirect()->back()->with(
                'success',
                "Colleges imported successfully! $inserted added, $updated updated."
            );
        } catch (\Exception $e) {
            // Log the error and return an error response
            Log::error('Error importing colleges: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during the import process.');
        }
    }
    

    public function importProgramData(Request $request)
    {
        // Validate the request to ensure campus_id is provided and valid
        $request->validate([
            'campus_id' => 'required|integer|exists:campuses,campus_id',
        ]);
    
        // Define mapping of campus_id to database connections
        $campusConnections = [
            1 => 'es_obrero',
            2 => 'es_tagum',
            3 => 'es_mintal',
            4 => 'es_mabini',
        ];
    
        // Get the campus_id from the request
        $campusId = $request->input('campus_id');
    
        // Retrieve the database connection for the given campus_id
        $databaseConnection = $campusConnections[$campusId] ?? null;
    
        // If no database connection is found for the given campus_id, return an error
        if (!$databaseConnection) {
            return redirect()->back()->with('error', 'Invalid campus selected!');
        }
    
        try {
            // Fetch program data from the campus-specific database
            $programs = DB::connection($databaseConnection)->table('vw_es_programs_TB')->get();
    
            if ($programs->isEmpty()) {
                return redirect()->back()->with('error', 'No programs found in the selected campus database.');
            }
    
            // Initialize counters for inserted and updated records
            $inserted = 0;
            $updated = 0;
    
            foreach ($programs as $program) {
                // Validate and retrieve college_id for the program
                $college = College::where([
                    'campus_id' => $campusId,
                    'college_id' => $program->CollegeID, // Assume CollegeID is available in the view
                ])->first();
    
                if (!$college) {
                    // Skip the program if its associated college doesn't exist
                    Log::warning("Skipped program import: No matching college for ProgramID {$program->ProgID} in campus {$campusId}.");
                    continue;
                }
    
                // Use composite primary key (campus_id + college_id + program_id) for uniqueness
                $programRecord = Program::updateOrCreate(
                    [
                        'campus_id' => $campusId, // Part of composite key
                        'college_id' => $college->college_id, // Part of composite key
                        'program_id' => $program->ProgID, // Part of composite key
                    ],
                    [
                        'program_name' => $program->ProgName,
                        'updated_at' => now(), // Always update timestamps
                    ]
                );
    
                // Track insertions and updates
                if ($programRecord->wasRecentlyCreated) {
                    $inserted++;
                } else {
                    $updated++;
                }
            }
    
            return redirect()->back()->with(
                'success',
                "Programs imported successfully! $inserted added, $updated updated."
            );
        } catch (\Exception $e) {
            // Log the error and return an error response
            Log::error('Error importing programs: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during the import process.');
        }
    }
    

    public function importMajorData(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'campus_id' => 'required|integer|exists:campuses,campus_id',
        ]);
    
        // Define mapping of campus_id to database connections
        $campusConnections = [
            1 => 'es_obrero',
            2 => 'es_tagum',
            3 => 'es_mintal',
            4 => 'es_mabini',
        ];
    
        // Get the campus_id from the request
        $campusId = $request->input('campus_id');
    
        // Retrieve the database connection for the given campus_id
        $databaseConnection = $campusConnections[$campusId] ?? null;
    
        // If no database connection is found for the given campus_id, return an error
        if (!$databaseConnection) {
            return redirect()->back()->with('error', 'Invalid campus selected!');
        }
    
        try {
            // Fetch majors from the specified database connection
            $majors = DB::connection($databaseConnection)->table('vw_ProgramMajors_TB')->get();
    
            if ($majors->isEmpty()) {
                return redirect()->back()->with('error', 'No majors found in the selected campus database.');
            }
    
            $inserted = 0;
            $updated = 0;
    
            foreach ($majors as $major) {
                // Validate associated program and college
                $program = Program::where([
                    'campus_id' => $campusId,
                    'college_id' => $major->CollegeID,
                    'program_id' => $major->ProgID,
                ])->first();
    
                if (!$program) {
                    Log::warning("Skipped major import: No matching program for MajorID {$major->IndexID}, ProgramID {$major->ProgID}, CollegeID {$major->CollegeID} in campus {$campusId}.");
                    continue;
                }
    
                // Use composite primary key (campus_id + college_id + program_id + major_id) for uniqueness
                $majorRecord = Major::updateOrCreate(
                    [
                        'campus_id' => $campusId, // Part of composite key
                        'college_id' => $program->college_id, // Part of composite key
                        'program_id' => $program->program_id, // Part of composite key
                        'major_id' => $major->IndexID, // Part of composite key
                    ],
                    [
                        'major_name' => $major->Major,
                        'updated_at' => now(), // Always update timestamps
                    ]
                );
    
                // Track insertions and updates
                if ($majorRecord->wasRecentlyCreated) {
                    $inserted++;
                } else {
                    $updated++;
                }
            }
    
            return redirect()->back()->with(
                'success',
                "Majors imported successfully! $inserted added, $updated updated."
            );
        } catch (\Exception $e) {
            // Log the error and return an error response
            Log::error('Error importing majors: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while importing majors.');
        }
    }
    

    public function importYearData(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'campus_id' => 'required|integer|exists:campuses,campus_id',
        ]);
    
        // Define mapping of campus_id to database connections
        $campusConnections = [
            1 => 'es_obrero',
            2 => 'es_tagum',
            3 => 'es_mintal',
            4 => 'es_mabini',
        ];
    
        // Get the campus_id from the request
        $campusId = $request->input('campus_id');
    
        // Retrieve the database connection for the given campus_id
        $databaseConnection = $campusConnections[$campusId] ?? null;
    
        // If no database connection is found for the given campus_id, return an error
        if (!$databaseConnection) {
            return redirect()->back()->with('error', 'Invalid campus selected!');
        }
    
        try {
            // Fetch years from the specified campus database connection
            $years = DB::connection($databaseConnection)->table('vw_YearLevel_TB')->get();
            
            if ($years->isEmpty()) {
                return redirect()->back()->with('error', 'No years found in the selected campus database.');
            }
    
            $inserted = 0;
            $updated = 0;
    
            // Loop through the years and insert or update them in the main database
            foreach ($years as $year) {
                $yearData = Year::updateOrCreate(
                    ['year_id' => $year->Yearlevelid],
                    [
                        'year_name' => $year->Yearlevel,
                        'campus_id' => $campusId,
                    ]
                );
    
                // Track insertions and updates
                if ($yearData->wasRecentlyCreated) {
                    $inserted++;
                } else {
                    $updated++;
                }
            }
    
            return redirect()->back()->with('success', "$inserted years added, $updated years updated.");
        } catch (\Exception $e) {
            Log::error('Error importing years: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while importing years.');
        }
    }
    

    public function importStudentData(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'campus_id' => 'required|integer|exists:campuses,campus_id',
        ]);
    
        // Define mapping of campus_id to database connections
        $campusConnections = [
            1 => 'es_obrero',
            2 => 'es_tagum',
            3 => 'es_mintal',
            4 => 'es_mabini',
        ];
    
        // Get the campus_id from the request
        $campusId = $request->input('campus_id');
    
        // Retrieve the database connection for the given campus_id
        $databaseConnection = $campusConnections[$campusId] ?? null;
    
        if (!$databaseConnection) {
            return redirect()->back()->with('error', 'Invalid campus selected!');
        }
    
        try {
            // Step 1: Set all students to inactive for the selected campus
            Student::where('enrollment_stat', 'active')
                ->where('campus_id', $campusId)
                ->update(['enrollment_stat' => 'inactive']);
    
            // Step 2: Fetch majors and programs mapping from the specified campus database
            $majorsMapping = DB::connection($databaseConnection)
                ->table('vw_ProgramMajors_TB')
                ->pluck('IndexID', 'MajorDiscID')
                ->toArray();
    
            $programIds = Program::pluck('program_id')->toArray();
            $majorIds = Major::pluck('major_id')->toArray();
    
            // Step 3: Process students in batches from the specified campus
            DB::connection($databaseConnection)->table('vw_Students_TB')
                ->distinct()
                ->orderBy('StudentNo')
                ->chunk(50, function ($students) use ($majorsMapping, $programIds, $majorIds, $campusId) {
                    $batchData = [];
    
                    foreach ($students as $student) {
                        // Prepare the email
                        $email = !empty($student->Email) ? $student->Email : "noEmail{$student->StudentNo}@usep.edu.ph";
    
                        // Map foreign keys for major_id and program_id
                        $majorId = $majorsMapping[$student->MajorID] ?? null;
                        $programId = in_array($student->ProgID, $programIds) ? $student->ProgID : null;
    
                        // Log missing foreign keys
                        if ($majorId && !in_array($majorId, $majorIds)) {
                            Log::warning("Student {$student->StudentNo}: Invalid major_id {$student->MajorID}");
                            continue;
                        }
                        if (is_null($programId)) {
                            Log::warning("Student {$student->StudentNo}: Invalid program_id {$student->ProgID}");
                            continue;
                        }
    
                        // Prepare the student data
                        $studentData = [
                            'stud_id' => $student->StudentNo,
                            'stud_lname' => $student->LastName,
                            'stud_fname' => $student->FirstName,
                            'stud_mname' => null,
                            'stud_contact' => $student->MobileNo,
                            'stud_email' => $email,
                            'college_id' => $student->CollegeID,
                            'program_id' => $programId,
                            'major_id' => $majorId,
                            'year_id' => $student->YearLevelID,
                            'campus_id' => $campusId,
                            'enrollment_stat' => 'active',
                        ];
    
                        // Add to batch for upsert
                        $batchData[] = $studentData;
                    }
    
                    // Step 4: Upsert batch data
                    if (!empty($batchData)) {
                        Student::upsert($batchData, ['stud_id', 'campus_id'], [
                            'stud_lname',
                            'stud_fname',
                            'stud_mname',
                            'stud_contact',
                            'stud_email',
                            'college_id',
                            'program_id',
                            'major_id',
                            'year_id',
                            'enrollment_stat',
                        ]);
                    }
                });
    
            return redirect()->back()->with('success', 'Students imported successfully in batches!');
        } catch (\Exception $e) {
            Log::error('Error importing students: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while importing students.');
        }
    }       

    public function addCampus(Request $request)
    {
        // Validate the incoming request
        $request->validate(['campus_name' => 'required|string|max:100']);

        // Create a new campus in the database
        $campus = Campus::create(['campus_name' => $request->campus_name]);

        // Generate the HTML for the new campus card using the Blade partial
        $cardHtml = view('partials.campus-card', compact('campus'))->render();

        // Return the campus data and the generated card HTML
        return response()->json(['campus' => $campus, 'cardHtml' => $cardHtml], 201);
    }
    
    public function importOfficeData(Request $request)
    {
        try {
            // Fetch data from HRIS database
            $offices = DB::connection('mysql_hris')->table('vw_office_tb')->get(['id', 'name']);

            if ($offices->isEmpty()) {
                return redirect()->back()->with('error', 'No offices found in the HRIS database.');
            }

            // Insert data into the MSSQL database
            foreach ($offices as $office) {
                DB::connection('sqlsrv')->table('offices')->updateOrInsert(
                    ['office_id' => $office->id], // Use HRIS id as office_id
                    [
                        'office_name' => $office->name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            return redirect()->back()->with('success', 'Offices imported successfully!');
        } catch (\Exception $e) {
            Log::error('Error importing offices: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during the import.');
        }
    }

    public function importEmploymentTypes(Request $request)
    {
        try {
            // Fetch data from HRIS database
            $employmentTypes = DB::connection('mysql_hris')->table('vw_employmenttype_tb')->get(['id', 'name']);

            if ($employmentTypes->isEmpty()) {
                return redirect()->back()->with('error', 'No employment types found in the HRIS database.');
            }

            // Insert or update employment types in local MSSQL database
            foreach ($employmentTypes as $employmentType) {
                DB::connection('sqlsrv')->table('types')->updateOrInsert(
                    ['type_id' => $employmentType->id], // Match by HRIS id
                    [
                        'type_name' => $employmentType->name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            return redirect()->back()->with('success', 'Employment types imported successfully!');
        } catch (\Exception $e) {
            Log::error('Error importing employment types: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during the import.');
        }
    }

    public function importEmploymentStatuses(Request $request)
    {
        try {
            // Fetch employment statuses from HRIS database
            $employmentStatuses = DB::connection('mysql_hris')->table('vw_employmentstatus_tb')->get(['id', 'name']);

            if ($employmentStatuses->isEmpty()) {
                return redirect()->back()->with('error', 'No employment statuses found in the HRIS database.');
            }

            // Insert or update employment statuses in the local statuses table
            foreach ($employmentStatuses as $status) {
                DB::connection('sqlsrv')->table('statuses')->updateOrInsert(
                    ['status_id' => $status->id], // Match by HRIS id
                    [
                        'status_name' => $status->name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            return redirect()->back()->with('success', 'Employment statuses imported successfully!');
        } catch (\Exception $e) {
            Log::error('Error importing employment statuses: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during the import.');
        }
    }

    public function importEmployees(Request $request)
    {
        try {
            // Fetch employees from HRIS database
            $employees = DB::connection('mysql_hris')->table('vw_employee_tb')->get([
                'EmployeeID', 'FirstName', 'LastName', 'MiddleName', 'Contact #', 'Email',
                'CampusID', 'OfficeID', 'TypeID', 'StatusID'
            ]);
    
            // Check if employees data exists
            if ($employees->isEmpty()) {
                return redirect()->back()->with('error', 'No employees found in the HRIS database.');
            }
    
            foreach ($employees as $employee) {
    
                // Skip if status_id or type_id is NULL
                if (empty($employee->StatusID) || empty($employee->TypeID)) {
                    Log::warning("Skipping employee {$employee->EmployeeID} due to NULL status_id or type_id.");
                    continue;
                }
    
                // Check if employee with the same email exists but with a different emp_id
                $existingEmployee = DB::connection('sqlsrv')->table('employees')
                    ->where('emp_email', $employee->Email)
                    ->where('emp_id', '!=', $employee->EmployeeID) // Make sure emp_id is unique
                    ->first();
    
                // Insert or update employee (Insert even if email is the same but different emp_id)
                DB::connection('sqlsrv')->table('employees')->updateOrInsert(
                    ['emp_id' => $employee->EmployeeID], // Using emp_id as the unique identifier
                    [
                        'emp_fname' => $employee->FirstName,
                        'emp_lname' => $employee->LastName,
                        'emp_mname' => $employee->MiddleName,
                        'emp_contact' => $employee->{'Contact #'},
                        'emp_email' => $employee->Email,
                        'campus_id' => $employee->CampusID,
                        'office_id' => $employee->OfficeID,
                        'status_id' => $employee->StatusID,
                        'type_id' => $employee->TypeID,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
    
                // Log if a duplicate email is found, but we allow the insertion to proceed
                if ($existingEmployee) {
                    Log::info("Employee {$employee->EmployeeID} inserted with duplicate email: {$employee->Email}");
                }
            }
    
            return redirect()->back()->with('success', 'Employees imported successfully!');
        } catch (\Exception $e) {
            Log::error('Error importing employees: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during the import.');
        }
    }
          

}
