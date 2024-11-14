<?php

namespace App\Http\Controllers;

use App\Models\MessageCategory;
use Illuminate\Http\Request;

class MessageCategoryController extends Controller
{
    // Display a paginated list of message categories within the App Management page's Categories section
    public function index()
    {
        // This can be called if necessary, but typically the data is loaded in AppManagementController
        $categories = MessageCategory::paginate(10);
        return redirect()->route('templates.index', ['section' => 'categories']);
    }

    // Store a new category (called from AJAX in the create modal)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:message_categories|max:80',
        ]);

        $category = MessageCategory::create(['name' => $request->name]);

        return redirect()->route('templates.index', ['section' => 'categories'])
            ->with('success', 'Category created successfully.');
    }

    // Update an existing category (called from AJAX in the edit modal)
    public function update(Request $request, MessageCategory $messageCategory)
    {
        $request->validate([
            'name' => 'required|max:80|unique:message_categories,name,' . $messageCategory->id,
        ]);

        $messageCategory->update(['name' => $request->name]);

        return redirect()->route('templates.index', ['section' => 'categories'])
            ->with('success', 'Category updated successfully.');
    }

    // Delete a category and redirect back to the Categories section
    public function destroy(MessageCategory $messageCategory)
    {
        $messageCategory->delete();

        return redirect()->route('templates.index', ['section' => 'categories'])
            ->with('success', 'Category deleted successfully.');
    }
}
