<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Services\FilterService;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    protected $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    // Fetch colleges by campus
    public function getCollegesByCampus($campusId)
    {
        $campusId = $campusId === 'all' ? null : $campusId;
        return response()->json($this->filterService->getCollegesByCampus($campusId));
    }

    // Fetch programs by college
    public function getPrograms($collegeId)
    {
        $collegeId = $collegeId === 'all' ? null : $collegeId;
        return response()->json($this->filterService->getProgramsByCollege($collegeId));
    }

    // Fetch majors by program
    public function getMajors($programId)
    {
        $programId = $programId === 'all' ? null : $programId;
        return response()->json($this->filterService->getMajorsByProgram($programId));
    }

    // Fetch all academic years
    public function getYears()
    {
        return response()->json($this->filterService->getYears());
    }
    
    // Fetch offices by campus
    public function getOfficesByCampus($campusId)
    {
        $campusId = $campusId === 'all' ? null : $campusId;
        return response()->json($this->filterService->getOfficesByCampus($campusId));
    }

    // Fetch types by office
    public function getTypes($officeId)
    {
        $officeId = $officeId === 'all' ? null : $officeId;
        return response()->json($this->filterService->getTypesByOffice($officeId));
    }

    // Fetch all statuses
    // public function getStatuses()
    // {
    //     // Use the FilterService to fetch sorted statuses
    //     return response()->json($this->filterService->getStatuses());
    // }       

    // Get recipient count
    public function getRecipientCount(Request $request)
    {
        $filters = [
            'campusId' => $request->get('campus', 'all'),
            'collegeId' => $request->get('college', 'all'),
            'programId' => $request->get('program', 'all'),
            'majorId' => $request->get('major', 'all'),
            'yearId' => $request->get('year', 'all'),
            'officeId' => $request->get('office', 'all'),
            'typeId' => $request->get('type', 'all'),
            'statusId' => $request->get('status', 'all'),
            'tab' => $request->get('tab', 'all'),
        ];

        // Convert "all" to null for filtering logic
        $filteredValues = array_map(fn($value) => $value === 'all' ? null : $value, $filters);

        $totalRecipients = $this->filterService->getRecipientCount(
            $filteredValues['tab'],
            $filteredValues['campusId'],
            $filteredValues['collegeId'],
            $filteredValues['programId'],
            $filteredValues['majorId'],
            $filteredValues['yearId'],
            $filteredValues['officeId'],
            $filteredValues['typeId'],
            $filteredValues['statusId']
        );

        return response()->json(['totalRecipients' => $totalRecipients]);
    }
}
