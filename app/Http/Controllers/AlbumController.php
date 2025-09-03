<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\TrainingSession;
use App\Services\FalAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AlbumController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $albums = auth()->user()->albums()->withCount('photos')->latest()->get();
        
        return view('albums.index', compact('albums'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('albums.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240' // 10MB max per photo
        ]);

        // Create the album
        $album = Album::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'trigger_word' => $request->trigger_word,
            'status' => 'pending'
        ]);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                $path = $file->store('photos', 'public');

                $album->photos()->create([
                    'name' => $file->getClientOriginalName(),
                    'description' => null,
                    'image_path' => $path,
                    'status' => 'pending'
                ]);
            }
        }

        $photoCount = $album->photos()->count();
        $message = $photoCount === 0 
            ? 'Album created successfully! Add some photos to get started.'
            : "Album created successfully with {$photoCount} photos! You can now train your AI model.";

        return redirect()->route('albums.show', $album)
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Album $album)
    {
        $this->authorize('view', $album);
        
        $album->load(['photos']);
        
        return view('albums.show', compact('album'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Album $album)
    {
        $this->authorize('update', $album);
        
        return view('albums.edit', compact('album'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Album $album)
    {
        $this->authorize('update', $album);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        $album->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('albums.show', $album)
            ->with('success', 'Album updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album)
    {
        $this->authorize('delete', $album);

        // Delete all associated photos and their files
        foreach ($album->photos as $photo) {
            if (Storage::disk('public')->exists($photo->image_path)) {
                Storage::disk('public')->delete($photo->image_path);
            }
        }

        $album->delete();

        return redirect()->route('albums.index')
            ->with('success', 'Album deleted successfully!');
    }

    /**
     * Start training the AI model with the album photos
     */
    public function startTraining(Album $album, FalAIService $falService)
    {
        $this->authorize('update', $album);

        // Check if album has photos
        if ($album->photos->isEmpty()) {
            return back()->with('error', 'Cannot train an empty album. Please add some photos first.');
        }

        // Check if album has enough photos (minimum 4 recommended)
        if ($album->photos->count() < 4) {
            return back()->with('error', 'Please add at least 4 photos to the album for optimal training results.');
        }

        // Check if album is already training
        if ($album->status === 'training') {
            return back()->with('error', 'This album is already being trained.');
        }

        // Check if album is already trained
        if ($album->status === 'completed' && $album->model_id) {
            return back()->with('error', 'This album is already trained and ready for use.');
        }

        try {
            // Get all photo paths from the album
            $photoPaths = $album->photos->pluck('image_path')->toArray();

            // Create and upload zip archive
            $archiveResult = $falService->createAndUploadPhotoArchive($photoPaths, $album->name);
            
            if (!$archiveResult['success']) {
                return back()->with('error', 'Failed to create photo archive: ' . $archiveResult['error']);
            }

            // Start training with FAL AI using the zip archive URL
            $trainingResult = $falService->startTraining($archiveResult['url'], $album->name, $album->trigger_word);
            
            if (!$trainingResult['success']) {
                return back()->with('error', 'Failed to start training: ' . $trainingResult['error']);
            }

            // Create training session
            $trainingSession = TrainingSession::create([
                'user_id' => auth()->id(),
                'album_id' => $album->id,
                'session_id' => $trainingResult['session_id'],
                'status' => 'running',
                'training_config' => [
                    'model_name' => $album->name,
                    'photo_count' => count($photoPaths),
                    'archive_url' => $archiveResult['url'],
                    'archive_path' => $archiveResult['local_path']
                ],
                'started_at' => now()
            ]);

            // Update album status
            $album->update([
                'status' => 'training'
            ]);

            // Update all photos status
            $album->photos()->update(['status' => 'training']);

            return redirect()->route('training.show', $trainingSession)
                ->with('success', 'AI training started successfully! This may take several minutes. You can monitor the progress here.');

        } catch (\Exception $e) {
            return back()->with('error', 'Training failed: ' . $e->getMessage());
        }
    }
}
