<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $atelier = $request->user()->ateliers()->first();

        if (!$atelier) {
            return redirect()->route('ateliers.create');
        }

        $categories = $atelier->categories()
            ->withCount('products')
            ->orderBy('position')
            ->get();

        return Inertia::render('categories/Index', [
            'categories' => $categories,
            'atelier' => $atelier,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $atelier = $request->user()->ateliers()->first();

        if (!$atelier) {
            return redirect()->route('ateliers.create');
        }

        return Inertia::render('categories/Create', [
            'atelier' => $atelier,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $atelier = $request->user()->ateliers()->first();

        if (!$atelier) {
            return redirect()->route('ateliers.create');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;

        while (Category::where('atelier_id', $atelier->id)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['atelier_id'] = $atelier->id;
        $validated['position'] = $validated['position'] ?? 0;
        $validated['is_active'] = $validated['is_active'] ?? true;

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // Not needed for now
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Category $category)
    {
        // Ensure user owns this category's atelier
        if ($category->atelier->user_id !== $request->user()->id) {
            abort(403);
        }

        return Inertia::render('categories/Edit', [
            'category' => $category->load('atelier'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // Ensure user owns this category's atelier
        if ($category->atelier->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // Regenerate slug if name changed
        if ($category->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;

            while (Category::where('atelier_id', $category->atelier_id)
                ->where('slug', $slug)
                ->where('id', '!=', $category->id)
                ->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;
        }

        $validated['position'] = $validated['position'] ?? $category->position;
        $validated['is_active'] = $validated['is_active'] ?? $category->is_active;

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        // Ensure user owns this category's atelier
        if ($category->atelier->user_id !== $request->user()->id) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}
