<?php

namespace App\Http\Controllers;

use App\Models\AtelierLpConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AtelierLpConfigController extends Controller
{
    public function edit()
    {
        $atelier = auth()->user()->atelier;

        if (!$atelier) {
            return redirect()->route('ateliers.create')
                ->with('error', 'Você precisa criar um ateliê primeiro para configurar a Landing Page.');
        }

        $lpConfig = $atelier->lpConfig;

        if (!$lpConfig) {
            // Create default config
            $lpConfig = AtelierLpConfig::create([
                'atelier_id' => $atelier->id,
                'data' => $this->getDefaultLpConfig(),
                'status' => 'published',
                'published_at' => now(),
                'updated_by' => auth()->id(),
            ]);
        }

        return Inertia::render('AtelierLP/Edit', [
            'atelier' => $atelier,
            'lpConfig' => [
                'id' => $lpConfig->id,
                'data' => $lpConfig->data,
                'draft' => $lpConfig->draft,
                'status' => $lpConfig->status,
                'published_at' => $lpConfig->published_at,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $atelier = auth()->user()->atelier;

        if (!$atelier) {
            return redirect()->route('ateliers.create')
                ->with('error', 'Você precisa criar um ateliê primeiro.');
        }

        $lpConfig = $atelier->lpConfig;

        if (!$lpConfig) {
            $lpConfig = new AtelierLpConfig(['atelier_id' => $atelier->id]);
        }

        $validated = $request->validate([
            'data' => 'required|array',
            'status' => 'required|in:published,draft',
        ]);

        $lpConfig->data = $validated['data'];
        $lpConfig->status = $validated['status'];
        $lpConfig->updated_by = auth()->id();

        if ($validated['status'] === 'published') {
            $lpConfig->published_at = now();
        }

        $lpConfig->save();

        return redirect()->back()->with('success', 'Configurações da LP atualizadas com sucesso!');
    }

    public function saveDraft(Request $request)
    {
        $atelier = auth()->user()->atelier;

        if (!$atelier) {
            return response()->json(['error' => 'Ateliê não encontrado'], 404);
        }

        $lpConfig = $atelier->lpConfig;

        if (!$lpConfig) {
            $lpConfig = new AtelierLpConfig(['atelier_id' => $atelier->id]);
        }

        $validated = $request->validate([
            'draft' => 'required|array',
        ]);

        $lpConfig->draft = $validated['draft'];
        $lpConfig->updated_by = auth()->id();
        $lpConfig->save();

        return response()->json(['message' => 'Rascunho salvo com sucesso!']);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'type' => 'required|in:hero,logo',
        ]);

        $atelier = auth()->user()->atelier;

        if (!$atelier) {
            return response()->json(['error' => 'Ateliê não encontrado'], 404);
        }
        $type = $request->input('type');
        $image = $request->file('image');

        // Store image in public disk under atelier folder
        $path = $image->store("ateliers/{$atelier->id}/lp", 'public');
        $url = \Illuminate\Support\Facades\Storage::url($path);

        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $path,
            'type' => $type,
        ]);
    }

    private function getDefaultLpConfig(): array
    {
        return [
            'theme' => [
                'primaryColor' => '#000000',
                'secondaryColor' => '#ffffff',
                'accentColor' => '#3b82f6',
                'backgroundColor' => '#ffffff',
                'textColor' => '#1f2937',
                'mutedTextColor' => '#6b7280',
                'borderColor' => '#e5e7eb',
                'cardBackgroundColor' => '#ffffff',
                'fontFamily' => 'Inter, sans-serif',
            ],
            'header' => [
                'showLogo' => false,
                'logoUrl' => null,
                'showTagline' => true,
                'showBio' => true,
                'backgroundColor' => '#ffffff',
                'textColor' => '#1f2937',
            ],
            'hero' => [
                'enabled' => true,
                'title' => null,
                'subtitle' => null,
                'backgroundType' => 'color',
                'backgroundColor' => '#f9fafb',
                'gradientFrom' => '#f9fafb',
                'gradientTo' => '#e5e7eb',
                'backgroundImage' => null,
                'textColor' => '#1f2937',
            ],
            'products' => [
                'layout' => 'grid',
                'columns' => 3,
                'showCategory' => true,
                'showPrice' => true,
                'showDescription' => true,
                'cardStyle' => 'elevated',
                'imageAspectRatio' => 'square',
            ],
            'filters' => [
                'enabled' => true,
                'showSearch' => true,
                'showCategories' => true,
            ],
            'contact' => [
                'enabled' => true,
                'title' => 'Entre em contato',
                'description' => 'Tire suas dúvidas ou faça seu pedido',
                'showWhatsApp' => true,
                'showInstagram' => true,
                'showEmail' => true,
                'showLocation' => true,
                'backgroundColor' => '#f9fafb',
            ],
            'footer' => [
                'enabled' => true,
                'text' => null,
                'showSocialMedia' => true,
                'backgroundColor' => '#1f2937',
                'textColor' => '#ffffff',
            ],
            'seo' => [
                'metaTitle' => null,
                'metaDescription' => null,
                'metaKeywords' => null,
            ],
        ];
    }
}
