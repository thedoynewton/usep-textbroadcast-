<?php

namespace App\Http\Controllers;

use App\Services\TemplatesService;
use Illuminate\Http\Request;

class TemplatesController extends Controller
{
    protected $service;

    public function __construct(TemplatesService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {

        $messageTemplates = $this->service->getMessageTemplates();
        $messageCategories = $this->service->getMessageCategories();

        // Pass contacts to the view
        return view('templates.index', [
            'messageTemplates' => $messageTemplates,
            'messageCategories' => $messageCategories
        ]);
    }
}
