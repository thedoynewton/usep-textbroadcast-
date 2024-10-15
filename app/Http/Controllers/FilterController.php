<?php

namespace App\Http\Controllers;

use App\Services\FilterService;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    protected $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    public function getCollegesByCampus($campusId)
    {
        return response()->json($this->filterService->getCollegesByCampus($campusId === 'all' ? null : $campusId));
    }

    public function getPrograms($collegeId)
    {
        return response()->json($this->filterService->getProgramsByCollege($collegeId === 'all' ? null : $collegeId));
    }

    public function getMajors($programId)
    {
        return response()->json($this->filterService->getMajorsByProgram($programId === 'all' ? null : $programId));
    }

    public function getOfficesByCampus($campusId)
    {
        return response()->json($this->filterService->getOfficesByCampus($campusId === 'all' ? null : $campusId));
    }

    public function getTypes($officeId)
    {
        return response()->json($this->filterService->getTypesByOffice($officeId === 'all' ? null : $officeId));
    }

    public function getYears()
    {
        return response()->json($this->filterService->getYears());
    }

    public function getRecipientCount(Request $request)
    {
        $campusId = $request->get('campus', 'all');
        $collegeId = $request->get('college', 'all');
        $programId = $request->get('program', 'all');
        $majorId = $request->get('major', 'all');
        $yearId = $request->get('year', 'all');

        $officeId = $request->get('office', 'all');
        $typeId = $request->get('type', 'all');
        $statusId = $request->get('status', 'all');

        $tab = $request->get('tab', 'all');

        // Convert "all" values to null to indicate no filter
        $totalRecipients = $this->filterService->getRecipientCount(
            $tab,
            $campusId === 'all' ? null : $campusId,
            $collegeId === 'all' ? null : $collegeId,
            $programId === 'all' ? null : $programId,
            $majorId === 'all' ? null : $majorId,
            $yearId === 'all' ? null : $yearId,
            $officeId === 'all' ? null : $officeId,
            $typeId === 'all' ? null : $typeId,
            $statusId === 'all' ? null : $statusId
        );

        return response()->json(['totalRecipients' => $totalRecipients]);
    }

}
