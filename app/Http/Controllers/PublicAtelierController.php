<?php

namespace App\Http\Controllers;

use App\Models\Atelier;
use App\Models\AtelierLpConfig;
use App\Models\Product;
use Inertia\Inertia;

class PublicAtelierController extends Controller
{
    public function show(string $slug)
    {
        $atelier = Atelier::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $featured = Product::query()
            ->where('atelier_id', $atelier->id)
            ->with(['photos:id,product_id,url,file_path', 'category:id,name,slug'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($p) {
                return [
                    'id'       => $p->id,
                    'name'     => $p->name,
                    'slug'     => $p->slug,
                    'price'    => $p->price,
                    'currency' => 'BRL',
                    'badge'    => optional($p->category)->name,
                    'image'    => optional($p->photos->first())->url ?? \Illuminate\Support\Facades\Storage::url(optional($p->photos->first())->file_path),
                    'excerpt'  => $p->short_desc ?? '',
                ];
            });

        $config = $atelier->lpConfig ?? AtelierLpConfig::query()->first();

        $payload = [
            'atelier' => [
                'id'          => $atelier->id,
                'slug'        => $atelier->slug,
                'name'        => $atelier->name ?? 'Artisan Workshop',
                'tagline'     => $atelier->tagline ?? 'Peças únicas com técnicas atemporais.',
                'story_title' => optional($config)->data['story_title'] ?? 'Nossa História',
                'story_html'  => optional($config)->data['story_html'] ?? '<p>Feito à mão com propósito.</p>',
                'hero_image'  => optional($config)->data['hero_image_url'] ?? null,
                'logo_url'    => $atelier->logo_url ?? null,
                'city'        => $atelier->city ?? null,
                'state'       => $atelier->state ?? null,
                'cta_label'   => optional($config)->data['cta_label'] ?? 'Explorar Produtos',
                'cta_link'    => optional($config)->data['cta_link']  ?? '/products',
            ],
            'contact' => [
                'whatsapp' => $atelier->phone_whatsapp ?? '(84) 99999-9999',
                'email'    => $atelier->email ?? 'contato@atelier.com',
                'instagram'=> $atelier->instagram_handle ?? '@atelier',
                'address'  => $atelier->city && $atelier->state ? "{$atelier->city} / {$atelier->state}" : 'Rua Tal, 123 — Cidade/UF',
            ],
            'theme' => optional($config)->data['theme'] ?? [
                'primaryColor' => '#000000',
                'secondaryColor' => '#ffffff',
                'accentColor' => '#3b82f6',
                'backgroundColor' => '#ffffff',
                'textColor' => '#1f2937',
            ],
            'featured_products' => $featured,
            'year'   => now()->year,
            'social' => [
                'facebook'  => optional($config)->data['facebook'] ?? null,
                'instagram' => optional($config)->data['instagram_url'] ?? null,
            ],
        ];

        return Inertia::render('Public/AtelierLP', $payload);
    }

    public function showProduct(string $atelierSlug, string $productSlug)
    {
        $atelier = Atelier::where('slug', $atelierSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $product = Product::with(['photos', 'category'])
            ->where('slug', $productSlug)
            ->where('atelier_id', $atelier->id)
            ->where('is_available', true)
            ->firstOrFail();

        $photos = $product->photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url ?: \Illuminate\Support\Facades\Storage::url($photo->file_path),
                'thumb' => $photo->url ?: \Illuminate\Support\Facades\Storage::url($photo->thumb_path ?? $photo->file_path),
                'is_cover' => $photo->is_cover,
            ];
        });

        return Inertia::render('Public/ProductDetail', [
            'atelier' => [
                'id' => $atelier->id,
                'slug' => $atelier->slug,
                'name' => $atelier->name,
                'logo_url' => $atelier->logo_url,
            ],
            'product' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'short_desc' => $product->short_desc,
                'long_desc' => $product->long_desc,
                'price' => $product->price,
                'currency' => 'BRL',
                'is_personalizable' => $product->is_personalizable,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
                'photos' => $photos->values(),
            ],
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
                'title' => null, // usar atelier.name
                'subtitle' => null, // usar atelier.tagline
                'backgroundType' => 'color', // color | gradient | image
                'backgroundColor' => '#f9fafb',
                'gradientFrom' => '#f9fafb',
                'gradientTo' => '#e5e7eb',
                'backgroundImage' => null,
                'textColor' => '#1f2937',
            ],
            'products' => [
                'layout' => 'grid', // grid | list | masonry
                'columns' => 3, // 2, 3, 4
                'showCategory' => true,
                'showPrice' => true,
                'showDescription' => true,
                'cardStyle' => 'elevated', // flat | outlined | elevated
                'imageAspectRatio' => 'square', // square | portrait | landscape
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
