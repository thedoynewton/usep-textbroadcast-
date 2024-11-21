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
        // Get the campus_id from the request
        $campusId = $request->input('campus_id');

        // Define database connection based on campus ID
        $databaseConnection = $campusId == 1 ? 'es_obrero' : 'es_mintal';

        // Import college data from the specified connection
        $colleges = DB::connection($databaseConnection)->table('vw_college_TB')->get();

        foreach ($colleges as $college) {
            College::updateOrCreate(
                ['college_id' => $college->CollegeID],
                [
                    'campus_id' => $campusId,
                    'college_name' => $college->CollegeName
                ]
            );
        }

        return redirect()->back()->with('success', 'Colleges imported successfully!');
    }

    public function importProgramData(Request $request)
    {
        // Get the campus_id from the request
        $campusId = $request->input('campus_id');

        // Define database connection based on campus ID
        $databaseConnection = $campusId == 1 ? 'es_obrero' : 'es_mintal';

        // if (!$campusId) {
        //     return redirect()->back()->with('error', 'Campus ID is missing.');
        // }

        $programs = DB::connection($databaseConnection)->table('vw_es_programs_TB')->get();

        foreach ($programs as $program) {
            Program::updateOrCreate(
                ['program_id' => $program->ProgID],
                [
                    'campus_id' => $campusId,
                    'college_id' => $program->CollegeID,
                    'program_name' => $program->ProgName
                ]
            );
        }
        return redirect()->back()->with('success', 'Programs imported successfully!');
    }

    public function importMajorData(Request $request)
    {
        $campusId = $request->input('campus_id');
        $databaseConnection = $campusId == 1 ? 'es_obrero' : 'es_mintal'; // Adjust as needed for additional campuses

        $majors = DB::connection($databaseConnection)->table('vw_ProgramMajors_TB')->get();
        $programIds = Program::pluck('program_id')->toArray();

        foreach ($majors as $major) {
            $programId = in_array($major->ProgID, $programIds) ? $major->ProgID : null;

            if (!$programId) {
                $this->logMissingForeignKey('Major', $major->IndexID, 'program_id', $major->ProgID);
            }

            Major::updateOrCreate(
                ['major_id' => $major->IndexID],
                [
                    'campus_id' => $campusId,
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

    public function importStudentData(Request $request)
    {
        $campusId = $request->input('campus_id');
        $databaseConnection = $campusId == 1 ? 'es_obrero' : 'es_mintal'; // Adjust as needed for additional campuses

        // Step 1: Set all students to inactive for the selected campus
        Student::where('enrollment_stat', 'active')->where('campus_id', $campusId)->update(['enrollment_stat' => 'inactive']);

        // Step 2: Fetch the majors and programs mapping from the specified campus database
        $majorsMapping = DB::connection($databaseConnection)
            ->table('vw_ProgramMajors_TB')
            ->pluck('IndexID', 'MajorDiscID')
            ->toArray();

        $programIds = Program::pluck('program_id')->toArray();

        // Step 3: Process students in batches
        DB::connection($databaseConnection)->table('vw_Students_TB')
            ->distinct()
            ->orderBy('StudentNo')
            ->chunk(50, function ($students) use ($majorsMapping, $programIds, $campusId) {
                $batchData = [];
                $existingStudents = Student::whereIn('stud_id', $students->pluck('StudentNo')->toArray())
                    ->get()
                    ->keyBy('stud_id');

                foreach ($students as $student) {
                    $email = !empty($student->Email) ? $student->Email : "noEmail{$student->StudentNo}@usep.edu.ph";
                    $majorId = $majorsMapping[$student->MajorID] ?? null;
                    $programId = in_array($student->ProgID, $programIds) ? $student->ProgID : null;

                    if (is_null($majorId)) {
                        $this->logMissingForeignKey('Student', $student->StudentNo, 'major_id', $student->MajorID);
                    }
                    if (is_null($programId)) {
                        $this->logMissingForeignKey('Student', $student->StudentNo, 'program_id', $student->ProgID);
                    }

                    $existingStudent = $existingStudents->get($student->StudentNo);
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

                    if ($existingStudent) {
                        $needsUpdate = (
                            $existingStudent->stud_lname !== $student->LastName ||
                            $existingStudent->stud_fname !== $student->FirstName ||
                            $existingStudent->stud_mname !== null ||
                            $existingStudent->stud_contact !== $student->MobileNo ||
                            $existingStudent->stud_email !== $email ||
                            $existingStudent->college_id !== $student->CollegeID ||
                            $existingStudent->program_id !== $programId ||
                            $existingStudent->major_id !== $majorId ||
                            $existingStudent->year_id !== $student->YearLevelID ||
                            $existingStudent->campus_id !== $campusId ||
                            $existingStudent->enrollment_stat !== 'active'
                        );

                        if ($needsUpdate) {
                            $batchData[] = $studentData;
                        }
                    } else {
                        $batchData[] = $studentData;
                    }
                }

                // Step 4: Upsert batch data (update existing or insert new)
                Student::upsert($batchData, ['stud_id'], [
                    'stud_lname',
                    'stud_fname',
                    'stud_mname',
                    'stud_contact',
                    'stud_email',
                    'college_id',
                    'program_id',
                    'major_id',
                    'year_id',
                    'campus_id',
                    'enrollment_stat'
                ]);
            });

        return redirect()->back()->with('success', 'Students imported successfully in batches!');
    }

    private function logMissingForeignKey($entity, $entityId, $missingKey, $missingId)
    {
        Log::warning("$entity with ID '$entityId' added with NULL $missingKey due to missing $missingKey '$missingId' in related table.");
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

        // public function updateCampus(Request $request)
    // {
    //     $request->validate([
    //         'campus_id' => 'required|exists:campuses,campus_id',
    //         'campus_name' => 'required|string|max:100'
    //     ]);

    //     $campus = Campus::findOrFail($request->campus_id);
    //     $campus->update(['campus_name' => $request->campus_name]);

    //     return response()->json(['campus' => $campus], 200);
    // }
    
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
    
            if ($employees->isEmpty()) {
                return redirect()->back()->with('error', 'No employees found in the HRIS database.');
            }
    
            // Insert or update employees in the local employees table
            foreach ($employees as $employee) {
                DB::connection('sqlsrv')->table('employees')->updateOrInsert(
                    ['emp_id' => $employee->EmployeeID], // Match by HRIS EmployeeID
                    [
                        'emp_fname' => $employee->FirstName,
                        'emp_lname' => $employee->LastName,
                        'emp_mname' => $employee->MiddleName,
                        'emp_contact' => $employee->{'Contact #'}, // Note: Special characters like # require braces
                        'emp_email' => $employee->Email,
                        'campus_id' => $employee->CampusID,
                        'office_id' => $employee->OfficeID,
                        'status_id' => $employee->StatusID,
                        'type_id' => $employee->TypeID,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
    
            return redirect()->back()->with('success', 'Employees imported successfully!');
        } catch (\Exception $e) {
            Log::error('Error importing employees: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during the import.');
        }
    }    

}
