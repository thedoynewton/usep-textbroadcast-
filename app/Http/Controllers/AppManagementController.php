<?php

namespace App\Http\Controllers;

use App\Models\CreditBalance;
use App\Models\Employee;
use App\Models\Student;
use App\Services\AppManagementService;
use Illuminate\Http\Request;

class AppManagementController extends Controller
{
    protected $service;

    public function __construct(AppManagementService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // Get the requested section, default to 'contacts'
        $section = $request->input('section', 'contacts');
    
        // Check the section and fetch the appropriate data
        if ($section === 'import-employees') {
            // Fetch data for the "Import Employees" section (if any)
            $employees = []; // Replace with actual employee fetching logic if needed
            return view('app-management.index', [
                'section' => $section,
                'employees' => $employees,
            ]);
        }
    
        // For 'contacts' section
        if ($section === 'contacts') {
            $contacts = $this->service->getContacts($request->search, $request->campus_id, $request->type);
    
            if ($request->ajax()) {
                return view('partials.contacts-table', compact('contacts'))->render();
            }
    
            $messageTemplates = $this->service->getMessageTemplates();
            $messageCategories = $this->service->getMessageCategories();
            $counts = $this->service->getTotalCounts();
            $campuses = $this->service->getCampuses();
    
            return view('app-management.index', [
                'section' => $section,
                'contacts' => $contacts,
                'messageTemplates' => $messageTemplates,
                'messageCategories' => $messageCategories,
                'totalStudents' => $counts['totalStudents'],
                'totalEmployees' => $counts['totalEmployees'],
                'campuses' => $campuses,
            ]);
        }
    
        // For 'credit-balance' section
        if ($section === 'credit-balance') {
            $creditBalance = CreditBalance::first()->balance ?? 0;
    
            return view('app-management.index', [
                'section' => $section,
                'creditBalance' => $creditBalance,
            ]);
        }
    
        // For 'db-connection' section
        if ($section === 'db-connection') {
            $campuses = $this->service->getCampuses();
    
            return view('app-management.index', [
                'section' => $section,
                'campuses' => $campuses,
            ]);
        }
    
        // Default to contacts if no valid section is provided
        return redirect()->route('app-management.index', ['section' => 'contacts']);
    }
    
    public function search(Request $request)
    {
        $search = $request->input('search');
        $campusId = $request->input('campus_id');

        // Ensure students and employees are arrays
        $students = $this->service->getStudents($search, $campusId, false)->toArray();
        $employees = $this->service->getEmployees($search, $campusId, false)->toArray();

        return response()->json([
            'students' => $students,
            'employees' => $employees,
        ]);
    }

    public function updateContactNumber(Request $request, $id)
    {
        $request->validate([
            'contact_number' => 'required|string|max:15',
            'type' => 'required|string', // Pass type as 'stud_id' or 'emp_id' to differentiate
        ]);

        $contactNumber = $request->input('contact_number');
        $type = $request->input('type'); // 'stud_id' or 'emp_id'

        if ($type === 'stud_id') {
            // Update student contact
            $contact = Student::where('stud_id', $id)->first();
            if ($contact) {
                $contact->stud_contact = $contactNumber; // Use stud_contact column
                $contact->save();
            }
        } elseif ($type === 'emp_id') {
            // Update employee contact
            $contact = Employee::where('emp_id', $id)->first();
            if ($contact) {
                $contact->emp_contact = $contactNumber; // Use emp_contact column
                $contact->save();
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid contact type'], 400);
        }

        return response()->json(['success' => true, 'message' => 'Contact number updated successfully']);
    }

}
