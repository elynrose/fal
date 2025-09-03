<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\PhotoModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PhotoController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $photos = auth()->user()->photos()->with('album')->latest()->get();
        
        return view('photos.index', compact('photos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $albums = auth()->user()->albums()->get();
        
        return view('photos.create', compact('albums'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'album_id' => 'required|exists:albums,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'photos' => 'required|array|min:1',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $album = Album::findOrFail($request->album_id);
        $this->authorize('update', $album);

        $uploadedPhotos = [];
        $files = $request->file('photos');

        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $path = $file->store('photos', 'public');

            $photoModel = $album->photos()->create([
                'name' => $request->name . ' - ' . $file->getClientOriginalName(),
                'description' => $request->description,
                'image_path' => $path,
                'status' => 'pending'
            ]);

            $uploadedPhotos[] = $photoModel;
        }

        $count = count($uploadedPhotos);
        $message = $count === 1 
            ? 'Photo added to album successfully!'
            : "{$count} photos added to album successfully!";

        return redirect()->route('albums.show', $album)
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(PhotoModel $photo)
    {
        $this->authorize('view', $photo->album);
        
        return view('photos.show', compact('photo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhotoModel $photo)
    {
        $this->authorize('update', $photo->album);
        
        return view('photos.edit', compact('photo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PhotoModel $photo)
    {
        $this->authorize('update', $photo->album);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description
        ];

        // Handle photo replacement
        if ($request->hasFile('photo')) {
            // Delete the old image file
            if (Storage::disk('public')->exists($photo->image_path)) {
                Storage::disk('public')->delete($photo->image_path);
            }

            // Store the new image
            $file = $request->file('photo');
            $path = $file->store('photos', 'public');
            $data['image_path'] = $path;

            // Reset status to pending since we have a new photo
            $data['status'] = 'pending';
        }

        $photo->update($data);

        $message = $request->hasFile('photo') 
            ? 'Photo updated successfully! The new photo will need to be trained.'
            : 'Photo updated successfully!';

        return redirect()->route('photos.show', $photo)
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PhotoModel $photo)
    {
        $this->authorize('delete', $photo->album);

        // Delete the image file
        if (Storage::disk('public')->exists($photo->image_path)) {
            Storage::disk('public')->delete($photo->image_path);
        }

        $album = $photo->album;
        $photo->delete();

        return redirect()->route('albums.show', $album)
            ->with('success', 'Photo deleted successfully!');
    }
}
