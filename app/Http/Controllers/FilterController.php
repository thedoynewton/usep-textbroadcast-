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
        return response()->json($this->filterService->getCollegesByCampus($campusId));
    }

    public function getPrograms($collegeId)
    {
        return response()->json($this->filterService->getProgramsByCollege($collegeId));
    }

    public function getMajors($programId)
    {
        return response()->json($this->filterService->getMajorsByProgram($programId));
    }

    public function getOfficesByCampus($campusId)
    {
        return response()->json($this->filterService->getOfficesByCampus($campusId));
    }

    public function getTypes($officeId)
    {
        return response()->json($this->filterService->getTypesByOffice($officeId));
    }

    public function getYears()
    {
        return response()->json($this->filterService->getYears());
    }

    public function getRecipientCount(Request $request)
    {
        $campusId = $request->get('campus');
        $collegeId = $request->get('college');
        $programId = $request->get('program');
        $majorId = $request->get('major');
        $yearId = $request->get('year');

        $officeId = $request->get('office');
        $typeId = $request->get('type');
        $statusId = $request->get('status');

        $tab = $request->get('tab', 'all');

        $totalRecipients = $this->filterService->getRecipientCount($tab, $campusId, $collegeId, $programId, $majorId, $yearId, $officeId, $typeId, $statusId);

        return response()->json(['totalRecipients' => $totalRecipients]);
    }
}
