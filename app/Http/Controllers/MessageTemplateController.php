<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MessageCategory;
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
        // Validate inputs, allowing for a nullable category ID or new category name
        $validated = $request->validate([
            'name' => 'required|max:100|unique:message_templates,name',
            'content' => 'required|max:160',
            'category_id' => 'nullable|exists:message_categories,id',
            'new_category' => 'nullable|max:100'
        ]);
    
        // Determine category to use
        if ($request->filled('new_category')) {
            $existingCategory = MessageCategory::where('name', $request->new_category)->first();
    
            if ($existingCategory) {
                // Redirect back with a flash error message
                return redirect()->route('templates.index', ['section' => 'message-templates'])
                    ->with('error', 'This category already exists. Please choose a different name or select an existing category.');
            } else {
                // Create new category if it does not exist
                $category = MessageCategory::create(['name' => $request->new_category]);
            }
    
        } else {
            // Use the selected category
            $category = MessageCategory::find($validated['category_id']);
        }
    
        // Create the message template with the determined category ID
        MessageTemplate::create([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'category_id' => $category->id
        ]);
    
        // Redirect back with a success message
        return redirect()->route('templates.index', ['section' => 'message-templates'])
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
            'category_id' => 'required|exists:message_categories,id',
            'name' => 'required|max:100',
            'content' => 'required|max:160',
        ]);

        $messageTemplate->update($validated);

        // Redirect to the 'Message Templates' section of the 'App Management' page
        return redirect()->route('templates.index', ['section' => 'message-templates'])
            ->with('success', 'Message Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();

        // Redirect to the 'Message Templates' section of the 'App Management' page
        return redirect()->route('templates.index', ['section' => 'message-templates'])
            ->with('success', 'Message Template deleted successfully.');
    }
}
