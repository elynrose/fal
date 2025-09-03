@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-5">
                <h1 class="h2 fw-bold mb-2">Edit Photo Model</h1>
                <p class="text-muted">Update your photo model details and replace the image if needed.</p>
            </div>

            <!-- Edit Form -->
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('photos.update', $photo) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Photo Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label">Photo Name</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $photo->name) }}" 
                                   required 
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Enter a name for your photo">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="3" 
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Add a description to help identify this photo">{{ old('description', $photo->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Photo Preview -->
                        <div class="mb-4">
                            <label class="form-label">Current Photo</label>
                            <div class="border rounded-3 p-3 bg-light">
                                <img src="{{ Storage::url($photo->image_path) }}" 
                                     alt="{{ $photo->name }}" 
                                     class="img-fluid rounded shadow-sm" 
                                     style="max-height: 200px;">
                                <p class="text-muted small mt-2 mb-0">Current photo - you can replace it below if needed.</p>
                            </div>
                        </div>

                        <!-- New Photo Upload -->
                        <div class="mb-4">
                            <label for="photo" class="form-label">Replace Photo (Optional)</label>
                            <div class="border-2 border-dashed border-light rounded-3 p-4 text-center">
                                <div class="mb-3">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                </div>
                                <h6 class="fw-semibold mb-2">Upload new photo</h6>
                                <p class="text-muted mb-3">Choose a new image to replace the current one</p>
                                <input type="file" 
                                       id="photo" 
                                       name="photo" 
                                       accept="image/*" 
                                       class="form-control @error('photo') is-invalid @enderror">
                                <p class="text-muted small mt-2 mb-0">PNG, JPG, JPEG up to 10MB. Leave empty to keep current photo.</p>
                                @error('photo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tips Section -->
                        <div class="alert alert-info border-0 mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-lightbulb text-info"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-2">Tips for best results:</h6>
                                    <ul class="mb-0 small">
                                        <li>Use a clear, high-resolution photo</li>
                                        <li>Ensure good lighting and contrast</li>
                                        <li>Choose a photo with your face clearly visible</li>
                                        <li>Avoid group photos or heavily edited images</li>
                                        <li>If replacing the photo, the model will need to be retrained</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('photos.show', $photo) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Photo Model
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
