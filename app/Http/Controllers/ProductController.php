<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
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

        $products = $atelier->products()
            ->with(['category', 'photos'])
            ->withCount('photos')
            ->when($request->category, function ($query, $category) {
                $query->whereHas('category', fn($q) => $q->where('slug', $category));
            })
            ->latest()
            ->get();

        $categories = $atelier->categories()->active()->ordered()->get();

        return Inertia::render('products/Index', [
            'products' => $products,
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

        $categories = $atelier->categories()->active()->ordered()->get();

        // Require at least one category
        if ($categories->isEmpty()) {
            return redirect()->route('categories.create')
                ->with('error', 'Você precisa criar pelo menos uma categoria antes de adicionar produtos.');
        }

        return Inertia::render('products/Create', [
            'atelier' => $atelier,
            'categories' => $categories,
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

        // Require at least one category
        if ($atelier->categories()->active()->count() === 0) {
            return redirect()->route('categories.create')
                ->with('error', 'Você precisa criar pelo menos uma categoria antes de adicionar produtos.');
        }

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:150',
            'short_desc' => 'nullable|string|max:180',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_personalizable' => 'nullable|boolean',
            'personalization_hint' => 'nullable|string|max:140',
            'is_available' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'position' => 'nullable|integer',
        ]);

        // Verify category belongs to atelier if provided
        if (!empty($validated['category_id'])) {
            $category = $atelier->categories()->findOrFail($validated['category_id']);
        }

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;

        while (Product::where('atelier_id', $atelier->id)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['atelier_id'] = $atelier->id;
        $validated['is_available'] = $validated['is_available'] ?? true;
        $validated['is_featured'] = $validated['is_featured'] ?? false;
        $validated['is_personalizable'] = $validated['is_personalizable'] ?? false;
        $validated['position'] = $validated['position'] ?? 0;

        $product = Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Not needed for now
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Product $product)
    {
        // Ensure user owns this product's atelier
        if ($product->atelier->user_id !== $request->user()->id) {
            abort(403);
        }

        $categories = $product->atelier->categories()->active()->ordered()->get();

        return Inertia::render('products/Edit', [
            'product' => $product->load(['category', 'photos']),
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Ensure user owns this product's atelier
        if ($product->atelier->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:150',
            'short_desc' => 'nullable|string|max:180',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_personalizable' => 'nullable|boolean',
            'personalization_hint' => 'nullable|string|max:140',
            'is_available' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'position' => 'nullable|integer',
        ]);

        // Verify category belongs to atelier if provided
        if (!empty($validated['category_id'])) {
            $category = $product->atelier->categories()->findOrFail($validated['category_id']);
        }

        // Regenerate slug if name changed
        if ($product->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;

            while (Product::where('atelier_id', $product->atelier_id)
                ->where('slug', $slug)
                ->where('id', '!=', $product->id)
                ->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        // Ensure user owns this product's atelier
        if ($product->atelier->user_id !== $request->user()->id) {
            abort(403);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    /**
     * Upload photos for a product
     */
    public function uploadPhotos(Request $request, Product $product)
    {
        // Ensure user owns this product's atelier
        if ($product->atelier->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'photos' => 'required|array|max:10',
            'photos.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB max
        ]);

        $uploadedPhotos = [];

        foreach ($request->file('photos') as $index => $photo) {
            // Generate unique filename
            $filename = Str::uuid() . '.jpg';
            $path = 'product_photos/' . $product->atelier_id;

            // Ensure directories exist
            Storage::disk('public')->makeDirectory($path . '/thumbs');
            Storage::disk('public')->makeDirectory($path . '/medium');
            Storage::disk('public')->makeDirectory($path . '/original');

            // Define paths
            $thumbPath = $path . '/thumbs/' . $filename;
            $mediumPath = $path . '/medium/' . $filename;
            $originalPath = $path . '/original/' . $filename;

            // Get image info
            $imageInfo = getimagesize($photo->getRealPath());
            $mimeType = $imageInfo['mime'];

            // Create image resource from uploaded file
            $sourceImage = match($mimeType) {
                'image/jpeg' => imagecreatefromjpeg($photo->getRealPath()),
                'image/png' => imagecreatefrompng($photo->getRealPath()),
                'image/webp' => imagecreatefromwebp($photo->getRealPath()),
                default => imagecreatefromjpeg($photo->getRealPath()),
            };

            if (!$sourceImage) {
                continue; // Skip if image creation failed
            }

            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            // Save original (max 2000px, quality 85)
            $originalImage = $this->resizeImage($sourceImage, $originalWidth, $originalHeight, 2000, 2000, false);
            ob_start();
            imagejpeg($originalImage, null, 85);
            Storage::disk('public')->put($originalPath, ob_get_clean());
            imagedestroy($originalImage);

            // Save medium (800x800, quality 80)
            $mediumImage = $this->resizeImage($sourceImage, $originalWidth, $originalHeight, 800, 800, false);
            ob_start();
            imagejpeg($mediumImage, null, 80);
            Storage::disk('public')->put($mediumPath, ob_get_clean());
            imagedestroy($mediumImage);

            // Save thumbnail (400x400, quality 75, crop to fit)
            $thumbImage = $this->resizeImage($sourceImage, $originalWidth, $originalHeight, 400, 400, true);
            ob_start();
            imagejpeg($thumbImage, null, 75);
            Storage::disk('public')->put($thumbPath, ob_get_clean());
            imagedestroy($thumbImage);

            imagedestroy($sourceImage);

            // Determine if this is the first photo (set as cover)
            $isCover = $product->photos()->count() === 0 && $index === 0;

            // Save to database
            $productPhoto = ProductPhoto::create([
                'product_id' => $product->id,
                'file_path' => $originalPath,
                'thumb_path' => $thumbPath,
                'medium_path' => $mediumPath,
                'position' => $product->photos()->max('position') + 1 + $index,
                'is_cover' => $isCover,
            ]);

            $uploadedPhotos[] = $productPhoto;
        }

        return back()->with('success', count($uploadedPhotos) . ' foto(s) enviada(s) com sucesso!');
    }

    /**
     * Delete a product photo
     */
    public function deletePhoto(Request $request, Product $product, ProductPhoto $photo)
    {
        // Ensure user owns this product's atelier
        if ($product->atelier->user_id !== $request->user()->id) {
            abort(403);
        }

        // Ensure photo belongs to product
        if ($photo->product_id !== $product->id) {
            abort(404);
        }

        // Delete files from storage
        Storage::disk('public')->delete([
            $photo->file_path,
            $photo->thumb_path,
            $photo->medium_path,
        ]);

        // If deleting cover photo, set next photo as cover
        if ($photo->is_cover) {
            $nextPhoto = $product->photos()
                ->where('id', '!=', $photo->id)
                ->orderBy('position')
                ->first();

            if ($nextPhoto) {
                $nextPhoto->update(['is_cover' => true]);
            }
        }

        $photo->delete();

        return back()->with('success', 'Foto excluída com sucesso!');
    }

    /**
     * Set photo as cover
     */
    public function setCover(Request $request, Product $product, ProductPhoto $photo)
    {
        // Ensure user owns this product's atelier
        if ($product->atelier->user_id !== $request->user()->id) {
            abort(403);
        }

        // Ensure photo belongs to product
        if ($photo->product_id !== $product->id) {
            abort(404);
        }

        // Remove cover from all photos
        $product->photos()->update(['is_cover' => false]);

        // Set this photo as cover
        $photo->update(['is_cover' => true]);

        return back()->with('success', 'Foto de capa definida com sucesso!');
    }

    /**
     * Reorder photos
     */
    public function reorderPhotos(Request $request, Product $product)
    {
        // Ensure user owns this product's atelier
        if ($product->atelier->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'photos' => 'required|array',
            'photos.*.id' => 'required|exists:product_photos,id',
            'photos.*.position' => 'required|integer',
        ]);

        foreach ($request->photos as $photoData) {
            ProductPhoto::where('id', $photoData['id'])
                ->where('product_id', $product->id)
                ->update(['position' => $photoData['position']]);
        }

        return back()->with('success', 'Ordem das fotos atualizada!');
    }

    /**
     * Helper function to resize images
     */
    private function resizeImage($sourceImage, $originalWidth, $originalHeight, $maxWidth, $maxHeight, $crop = false)
    {
        if ($crop) {
            // Crop to fit (for thumbnails)
            $ratio = max($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = $originalWidth * $ratio;
            $newHeight = $originalHeight * $ratio;

            $tmpImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($tmpImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            $destImage = imagecreatetruecolor($maxWidth, $maxHeight);
            imagecopy($destImage, $tmpImage, 0, 0, ($newWidth - $maxWidth) / 2, ($newHeight - $maxHeight) / 2, $maxWidth, $maxHeight);
            imagedestroy($tmpImage);

            return $destImage;
        } else {
            // Resize maintaining aspect ratio
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);

            // Don't upscale
            if ($ratio > 1) {
                $ratio = 1;
            }

            $newWidth = $originalWidth * $ratio;
            $newHeight = $originalHeight * $ratio;

            $destImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            return $destImage;
        }
    }
}
