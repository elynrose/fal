@extends('layouts.app')

@section('content')
<div class="py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold mb-2">Photo Model Details</h1>
                    <p class="text-muted mb-0">View and manage your uploaded photo for AI training.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('photos.edit', $photo) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <form action="{{ route('photos.destroy', $photo) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn btn-outline-danger"
                                onclick="return confirm('Are you sure you want to delete this photo model? This action cannot be undone.')">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Image Display -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 fw-bold mb-0">Photo Preview</h3>
                </div>
                <div class="card-body text-center p-4">
                    <img src="{{ Storage::url($photo->image_path) }}" 
                         alt="Photo Model" 
                         class="img-fluid rounded shadow-sm"
                         style="max-height: 400px;">
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 fw-bold mb-0">Model Details</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Status</label>
                            <div>
                                @if($photo->status === 'pending')
                                    <span class="status-badge bg-warning bg-opacity-10 text-warning">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                @elseif($photo->status === 'training')
                                    <span class="status-badge bg-primary bg-opacity-10 text-primary">
                                        <i class="fas fa-cog fa-spin me-1"></i>Training
                                    </span>
                                @elseif($photo->status === 'completed')
                                    <span class="status-badge bg-success bg-opacity-10 text-success">
                                        <i class="fas fa-check me-1"></i>Completed
                                    </span>
                                @elseif($photo->status === 'failed')
                                    <span class="status-badge bg-danger bg-opacity-10 text-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Failed
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Name</label>
                            <p class="mb-0">{{ $photo->name }}</p>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <p class="mb-0">{{ $photo->description ?? 'No description provided' }}</p>
                        </div>

                        @if($photo->model_id)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Model ID</label>
                            <p class="mb-0 font-monospace">{{ $photo->model_id }}</p>
                        </div>
                        @endif

                        <div class="col-12">
                            <label class="form-label fw-semibold">Created</label>
                            <p class="mb-0">{{ $photo->created_at ? $photo->created_at->format('M j, Y g:i A') : 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('photos.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                        @if($photo->status === 'pending')
                            <form action="{{ route('albums.train', $photo->album) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-play me-2"></i>Train Model
                                </button>
                            </form>
                        @elseif($photo->status === 'completed')
                            <a href="{{ route('generations.create') }}" class="btn btn-success">
                                <i class="fas fa-magic me-2"></i>Generate Images
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Training Sessions -->
    @if(false)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 fw-bold mb-0">Training Sessions</h3>
                </div>
                <div class="card-body p-0">
                    <!-- Removed in album-based training -->
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Generated Images -->
    @if($photo->generatedImages->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 fw-bold mb-0">Generated Images</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($photo->generatedImages as $generatedImage)
                        <div class="col-md-4 col-sm-6">
                            <div class="photo-card">
                                @if($generatedImage->status === 'completed' && $generatedImage->image_path)
                                    <img src="{{ Storage::url($generatedImage->image_path) }}" 
                                         alt="Generated Image" 
                                         class="photo-image">
                                @else
                                    <div class="photo-image bg-light d-flex align-items-center justify-content-center">
                                        <div class="text-center">
                                            <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                            <p class="text-muted small mb-0">{{ ucfirst($generatedImage->status) }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="photo-content">
                                    <h5 class="fw-semibold mb-2">{{ $generatedImage->theme->name }}</h5>
                                    <p class="text-muted small mb-3">{{ Str::limit($generatedImage->prompt_used, 50) }}</p>
                                    <a href="{{ route('generations.show', $generatedImage) }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
