<?php

namespace App\Http\Controllers;

use App\Models\Atelier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AtelierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ateliers = $request->user()->ateliers()
            ->with(['categories', 'products'])
            ->latest()
            ->get();

        return Inertia::render('ateliers/Index', [
            'ateliers' => $ateliers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('ateliers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'tagline' => 'nullable|string|max:160',
            'bio' => 'nullable|string',
            'phone_whatsapp' => 'nullable|string|max:20',
            'instagram_handle' => 'nullable|string|max:60',
            'email' => 'nullable|email|max:150',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|size:2',
        ]);

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;

        while (Atelier::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['user_id'] = $request->user()->id;
        $validated['is_active'] = true;

        $atelier = Atelier::create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Ateliê criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Atelier $atelier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Atelier $atelier): Response
    {
        // Ensure user owns this atelier
        if ($atelier->user_id !== $request->user()->id) {
            abort(403, 'Você não tem permissão para editar este ateliê.');
        }

        return Inertia::render('ateliers/Edit', [
            'atelier' => $atelier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Atelier $atelier)
    {
        // Ensure user owns this atelier
        if ($atelier->user_id !== $request->user()->id) {
            abort(403, 'Você não tem permissão para editar este ateliê.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'tagline' => 'nullable|string|max:160',
            'bio' => 'nullable|string',
            'phone_whatsapp' => 'nullable|string|max:20',
            'instagram_handle' => 'nullable|string|max:60',
            'email' => 'nullable|email|max:150',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|size:2',
            'is_active' => 'nullable|boolean',
        ]);

        // Update slug only if name changed
        if ($validated['name'] !== $atelier->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;

            while (Atelier::where('slug', $slug)->where('id', '!=', $atelier->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;
        }

        $atelier->update($validated);

        return redirect()->route('ateliers.index')
            ->with('success', 'Ateliê atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Atelier $atelier)
    {
        //
    }
}
