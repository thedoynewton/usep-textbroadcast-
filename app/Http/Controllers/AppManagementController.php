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
        // Use the service to fetch filtered data
        $students = $this->service->getStudents($request->search, $request->campus_id);
        $employees = $this->service->getEmployees($request->search, $request->campus_id);
        $messageTemplates = $this->service->getMessageTemplates();
        $counts = $this->service->getTotalCounts();
        $campuses = $this->service->getCampuses();

        // Return the view with the data
        return view('app-management.index', [
            'students' => $students,
            'employees' => $employees,
            'messageTemplates' => $messageTemplates,
            'totalStudents' => $counts['totalStudents'],
            'totalEmployees' => $counts['totalEmployees'],
            'campuses' => $campuses,
        ]);
    }
}
