<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $themes = Theme::where('is_active', true)->get();
        return view('themes.index', compact('themes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('themes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:themes',
            'description' => 'required|string',
            'prompt_template' => 'required|string|max:1000',
            'icon' => 'nullable|string|max:50'
        ]);

        Theme::create([
            'name' => $request->name,
            'description' => $request->description,
            'prompt_template' => $request->prompt_template,
            'icon' => $request->icon,
            'is_active' => true
        ]);

        return redirect()->route('themes.index')
            ->with('success', 'Theme created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Theme $theme)
    {
        $generatedImages = $theme->generatedImages()->with(['user', 'photoModel'])->latest()->paginate(12);
        return view('themes.show', compact('theme', 'generatedImages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Theme $theme)
    {
        return view('themes.edit', compact('theme'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Theme $theme)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:themes,name,' . $theme->id,
            'description' => 'required|string',
            'prompt_template' => 'required|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ]);

        $theme->update([
            'name' => $request->name,
            'description' => $request->description,
            'prompt_template' => $request->prompt_template,
            'icon' => $request->icon,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('themes.index')
            ->with('success', 'Theme updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theme $theme)
    {
        // Check if theme has generated images
        if ($theme->generatedImages()->count() > 0) {
            return back()->with('error', 'Cannot delete theme that has generated images.');
        }

        $theme->delete();

        return redirect()->route('themes.index')
            ->with('success', 'Theme deleted successfully!');
    }
}
