<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Student;
use App\Services\AppManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class AppManagementController extends Controller
{
    protected $service;

    public function __construct(AppManagementService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // Get combined contacts
        $contacts = $this->service->getContacts($request->search, $request->campus_id, $request->type);

        $messageTemplates = $this->service->getMessageTemplates();
        $counts = $this->service->getTotalCounts();
        $campuses = $this->service->getCampuses();

        // Check if the request is an AJAX call
        if ($request->ajax()) {
            return view('partials.contacts-table', compact('contacts'))->render();
        }

        // Fetch the current credit balance from cache or database
        $creditBalance = Cache::get('credit_balance', 0); // Default initial balance


        // Pass contacts to the view
        return view('app-management.index', [
            'contacts' => $contacts,
            'messageTemplates' => $messageTemplates,
            'totalStudents' => $counts['totalStudents'],
            'totalEmployees' => $counts['totalEmployees'],
            'campuses' => $campuses,
            'creditBalance' => $creditBalance 
        ]);
    }

    public function updateCreditBalance(Request $request)
    {
        // Validate input
        $request->validate([
            'credit_balance' => 'required|integer|min:0',
        ]);

        // Update the credit balance in cache (you can store it in a database if preferred)
        Cache::put('credit_balance', $request->input('credit_balance'));

        return redirect()->route('app-management.index', ['section' => 'credit-balance'])
                         ->with('success', 'Credit balance updated successfully.');
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
