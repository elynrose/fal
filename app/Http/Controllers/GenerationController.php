<?php

namespace App\Http\Controllers;

use App\Models\GeneratedImage;
use App\Models\PhotoModel;
use App\Models\Theme;
use App\Services\FalAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GenerationController extends Controller
{
    use AuthorizesRequests;

    protected $falService;

    public function __construct(FalAIService $falService)
    {
        $this->falService = $falService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $generatedImages = auth()->user()->generatedImages()->with(['photoModel.album', 'theme'])->latest()->get();
        return view('generations.index', compact('generatedImages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get completed photos from user's albums
        $photoModels = auth()->user()->photos()->where('photo_models.status', 'completed')->get();
        $themes = Theme::where('is_active', true)->get();
        
        return view('generations.create', compact('photoModels', 'themes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'photo_model_id' => 'required|exists:photo_models,id',
            'theme_id' => 'required|exists:themes,id',
            'prompt' => 'required|string|max:500'
        ]);

        $photoModel = PhotoModel::findOrFail($request->photo_model_id);
        $theme = Theme::findOrFail($request->theme_id);

        // Check if user owns the album that contains the photo
        if ($photoModel->album->user_id !== auth()->id()) {
            return back()->with('error', 'You can only generate images with photos from your own albums.');
        }

        // Check if photo model is trained
        if ($photoModel->status !== 'completed') {
            return back()->with('error', 'Photo model must be trained before generating images.');
        }

        // Create the prompt using theme template
        $prompt = str_replace('{prompt}', $request->prompt, $theme->prompt_template);

        // Create generated image record
        $generatedImage = GeneratedImage::create([
            'user_id' => auth()->id(),
            'photo_model_id' => $photoModel->id,
            'theme_id' => $theme->id,
            'image_path' => null,
            'prompt_used' => $prompt,
            'generation_parameters' => [
                'prompt' => $request->prompt,
                'theme' => $theme->name,
                'model_id' => $photoModel->model_id
            ],
            'status' => 'pending'
        ]);

        // Generate image using fal AI
        $generationResult = $this->falService->generateImage(
            $photoModel->model_id,
            $request->prompt,
            $theme
        );

        if (!$generationResult['success']) {
            $generatedImage->update([
                'status' => 'failed',
                'error_message' => $generationResult['error']
            ]);
            return back()->with('error', 'Failed to generate image: ' . $generationResult['error']);
        }

        $generatedImage->update([
            'generation_id' => $generationResult['generation_id'],
            'status' => 'generating'
        ]);

        return redirect()->route('generations.show', $generatedImage)
            ->with('success', 'Image generation started! This may take a few moments.');
    }

    /**
     * Display the specified resource.
     */
    public function show(GeneratedImage $generatedImage)
    {
        $this->authorize('view', $generatedImage);
        
        // Check generation status if still processing
        if ($generatedImage->status === 'generating' && $generatedImage->generation_id) {
            $statusResult = $this->falService->checkGenerationStatus($generatedImage->generation_id);
            
            if ($statusResult['success']) {
                if ($statusResult['status'] === 'completed' && $statusResult['image_url']) {
                    // Download and store the image
                    $imageContent = Http::get($statusResult['image_url'])->body();
                    $imagePath = 'generated/' . uniqid() . '.jpg';
                    Storage::disk('public')->put($imagePath, $imageContent);
                    
                    $generatedImage->update([
                        'image_path' => $imagePath,
                        'status' => 'completed',
                        'generated_at' => now()
                    ]);
                } elseif ($statusResult['status'] === 'failed') {
                    $generatedImage->update([
                        'status' => 'failed',
                        'error_message' => 'Generation failed on fal AI side'
                    ]);
                }
            }
        }
        
        return view('generations.show', compact('generatedImage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GeneratedImage $generatedImage)
    {
        $this->authorize('update', $generatedImage);
        return view('generations.edit', compact('generatedImage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GeneratedImage $generatedImage)
    {
        $this->authorize('update', $generatedImage);

        $request->validate([
            'prompt_used' => 'required|string|max:500'
        ]);

        $generatedImage->update([
            'prompt_used' => $request->prompt_used
        ]);

        return redirect()->route('generations.show', $generatedImage)
            ->with('success', 'Generated image updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeneratedImage $generatedImage)
    {
        $this->authorize('delete', $generatedImage);

        // Delete the image file
        if (Storage::disk('public')->exists($generatedImage->image_path)) {
            Storage::disk('public')->delete($generatedImage->image_path);
        }

        $generatedImage->delete();

        return redirect()->route('generations.index')
            ->with('success', 'Generated image deleted successfully!');
    }

    /**
     * Check generation status via AJAX
     */
    public function checkStatus(GeneratedImage $generatedImage)
    {
        $this->authorize('view', $generatedImage);

        if ($generatedImage->status === 'generating' && $generatedImage->generation_id) {
            $statusResult = $this->falService->checkGenerationStatus($generatedImage->generation_id);
            
            if ($statusResult['success']) {
                if ($statusResult['status'] === 'completed' && $statusResult['image_url']) {
                    // Download and store the image
                    $imageContent = Http::get($statusResult['image_url'])->body();
                    $imagePath = 'generated/' . uniqid() . '.jpg';
                    Storage::disk('public')->put($imagePath, $imageContent);
                    
                    $generatedImage->update([
                        'image_path' => $imagePath,
                        'status' => 'completed',
                        'generated_at' => now()
                    ]);
                } elseif ($statusResult['status'] === 'failed') {
                    $generatedImage->update([
                        'status' => 'failed',
                        'error_message' => 'Generation failed on fal AI side'
                    ]);
                }
            }
        }

        return response()->json([
            'status' => $generatedImage->status,
            'image_url' => $generatedImage->status === 'completed' ? Storage::url($generatedImage->image_path) : null,
            'error_message' => $generatedImage->error_message,
            'generated_at' => $generatedImage->generated_at
        ]);
    }
}
