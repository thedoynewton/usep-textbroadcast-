<?php

namespace App\Http\Controllers;

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
        // Get combined contacts
        $contacts = $this->service->getContacts($request->search, $request->campus_id);

        $messageTemplates = $this->service->getMessageTemplates();
        $counts = $this->service->getTotalCounts();
        $campuses = $this->service->getCampuses();

        // Check if the request is an AJAX call
        if ($request->ajax()) {
            return view('partials.contacts-table', compact('contacts'))->render();
        }

        // Pass contacts to the view
        return view('app-management.index', [
            'contacts' => $contacts,
            'messageTemplates' => $messageTemplates,
            'totalStudents' => $counts['totalStudents'],
            'totalEmployees' => $counts['totalEmployees'],
            'campuses' => $campuses,
        ]);
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

}
