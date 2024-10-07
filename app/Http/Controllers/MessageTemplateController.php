<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index()
    {
        $messageTemplates = MessageTemplate::paginate(10);
        return view('message-templates.index', compact('messageTemplates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('message-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'content' => 'required',
        ]);

        MessageTemplate::create($validated);

        // Redirect to the 'Message Templates' section of the 'App Management' page
        return redirect()->route('app-management.index', ['section' => 'templates'])
            ->with('success', 'Message Template created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MessageTemplate $messageTemplate)
    {
        return view('message-templates.edit', compact('messageTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'content' => 'required',
        ]);

        $messageTemplate->update($validated);

        // Redirect to the 'Message Templates' section of the 'App Management' page
        return redirect()->route('app-management.index', ['section' => 'templates'])
            ->with('success', 'Message Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();

        // Redirect to the 'Message Templates' section of the 'App Management' page
        return redirect()->route('app-management.index', ['section' => 'templates'])
            ->with('success', 'Message Template deleted successfully.');
    }
}
    