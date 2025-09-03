@extends('layouts.app')

@section('content')
<div class="py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold mb-2">My Photo Models</h1>
                    <p class="text-muted mb-0">Manage and train your AI photo models</p>
                </div>
                <a href="{{ route('photos.create') }}" class="btn btn-primary">
                    <i class="fas fa-upload me-2"></i>Upload New Photo
                </a>
            </div>
        </div>
    </div>

    @if($photos->count() > 0)
        <!-- Photos Grid -->
        <div class="row">
            @foreach($photos as $photoModel)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="photo-card">
                        <div class="position-relative">
                            <img src="{{ Storage::url($photoModel->image_path) }}" 
                                 alt="{{ $photoModel->name }}" 
                                 class="photo-image">
                            
                            <!-- Status Badge Overlay -->
                            <div class="position-absolute top-0 end-0 m-3">
                                @if($photoModel->status === 'pending')
                                    <span class="status-badge bg-warning bg-opacity-10 text-warning">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                @elseif($photoModel->status === 'training')
                                    <span class="status-badge bg-primary bg-opacity-10 text-primary">
                                        <i class="fas fa-cog fa-spin me-1"></i>Training
                                    </span>
                                @elseif($photoModel->status === 'completed')
                                    <span class="status-badge bg-success bg-opacity-10 text-success">
                                        <i class="fas fa-check me-1"></i>Ready
                                    </span>
                                @elseif($photoModel->status === 'failed')
                                    <span class="status-badge bg-danger bg-opacity-10 text-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Failed
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="photo-content">
                            <h3 class="h5 fw-semibold mb-2">{{ $photoModel->name }}</h3>
                            
                            @if($photoModel->description)
                                <p class="text-muted small mb-3">{{ $photoModel->description }}</p>
                            @endif
                            
                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mb-3">
                                <a href="{{ route('photos.show', $photoModel) }}" 
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                
                                @if($photoModel->status === 'pending')
                                    <form action="{{ route('photos.train', $photoModel) }}" method="POST" class="flex-fill">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-play me-1"></i>Train
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Edit/Delete Buttons -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('photos.edit', $photoModel) }}" 
                                   class="btn btn-outline-secondary btn-sm flex-fill">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                
                                <form action="{{ route('photos.destroy', $photoModel) }}" method="POST" class="flex-fill">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger btn-sm w-100"
                                            onclick="return confirm('Are you sure you want to delete this photo model? This action cannot be undone.')">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-images fa-3x text-muted"></i>
                        </div>
                        <h3 class="h4 fw-semibold mb-2">No photo models yet</h3>
                        <p class="text-muted mb-4">Get started by uploading your first photo to train your AI model.</p>
                        <a href="{{ route('photos.create') }}" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload Your First Photo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
