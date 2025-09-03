@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-5">
                <h1 class="h2 fw-bold mb-2">Edit Album</h1>
                <p class="text-muted">Update your album details and information.</p>
            </div>

            <!-- Edit Form -->
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('albums.update', $album) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Album Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label">Album Name</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $album->name) }}" 
                                   required 
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Enter a name for your album">
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
                                      placeholder="Describe the purpose of this album or the types of photos it will contain">{{ old('description', $album->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Album Info -->
                        <div class="alert alert-info border-0 mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-info"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-2">Album Information</h6>
                                    <ul class="mb-0 small">
                                        <li>This album contains {{ $album->photos->count() }} photos</li>
                                        <li>Created {{ $album->created_at->diffForHumans() }}</li>
                                        <li>Status: {{ ucfirst($album->getOverallStatus()) }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('albums.show', $album) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Album
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
