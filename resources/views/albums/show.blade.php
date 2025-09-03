@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start mb-5">
            <div>
                <h1 class="h2 fw-bold mb-2">{{ $album->name }}</h1>
                @if($album->description)
                    <p class="text-muted mb-0">{{ $album->description }}</p>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('albums.edit', $album) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-edit me-2"></i>Edit Album
                </a>
                <form action="{{ route('albums.destroy', $album) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-outline-danger"
                            onclick="return confirm('Are you sure you want to delete this album? This will also delete all photos in the album.')">
                        <i class="fas fa-trash me-2"></i>Delete Album
                    </button>
                </form>
            </div>
        </div>

        <!-- Album Info -->
        <div class="row mb-5">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                                        <i class="fas fa-images"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-1">{{ $album->photos->count() }}</h4>
                                        <p class="text-muted mb-0">Total Photos</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                                        <i class="fas fa-brain"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-1">{{ $album->photos->where('status', 'completed')->count() }}</h4>
                                        <p class="text-muted mb-0">Trained Photos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-2">Album Status</h6>
                                @php
                                    $status = $album->getOverallStatus();
                                    $statusColors = [
                                        'empty' => 'secondary',
                                        'pending' => 'warning',
                                        'training' => 'info',
                                        'completed' => 'success',
                                        'failed' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'empty' => 'Empty',
                                        'pending' => 'Pending',
                                        'training' => 'Training',
                                        'completed' => 'Completed',
                                        'failed' => 'Failed'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$status] }} fs-6">{{ $statusLabels[$status] }}</span>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-2">Created</h6>
                                <p class="text-muted mb-0">{{ $album->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-3">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('albums.photos.create', $album) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Photos
                            </a>
                            @if($album->photos->count() > 0)
                                <a href="{{ route('generations.create') }}" class="btn btn-outline-success">
                                    <i class="fas fa-magic me-2"></i>Generate Images
                                </a>
                                
                                @if($album->status === 'pending')
                                    <form action="{{ route('albums.train', $album) }}" method="POST" class="d-grid">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-brain me-2"></i>Train AI Model
                                        </button>
                                    </form>
                                @elseif($album->status === 'training')
                                    <button class="btn btn-info" disabled>
                                        <i class="fas fa-spinner fa-spin me-2"></i>Training in Progress
                                    </button>
                                @elseif($album->status === 'completed')
                                    <button class="btn btn-success" disabled>
                                        <i class="fas fa-check me-2"></i>Model Trained
                                    </button>
                                @elseif($album->status === 'failed')
                                    <form action="{{ route('albums.train', $album) }}" method="POST" class="d-grid">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-redo me-2"></i>Retry Training
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photos Section -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h4 fw-semibold mb-0">Photos in Album</h3>
                    <a href="{{ route('albums.photos.create', $album) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Add Photos
                    </a>
                </div>
                
                @if($album->photos->count() > 0)
                    <div class="row g-4">
                        @foreach($album->photos as $photo)
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card h-100">
                                    <img src="{{ Storage::url($photo->image_path) }}" 
                                         class="card-img-top" 
                                         style="height: 200px; object-fit: cover;" 
                                         alt="{{ $photo->name }}">
                                    <div class="card-body p-3">
                                        <h6 class="card-title fw-semibold mb-2">{{ Str::limit($photo->name, 30) }}</h6>
                                        @if($photo->description)
                                            <p class="card-text small text-muted mb-2">{{ Str::limit($photo->description, 60) }}</p>
                                        @endif
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-{{ $photo->status === 'completed' ? 'success' : ($photo->status === 'training' ? 'info' : 'warning') }}">
                                                {{ ucfirst($photo->status) }}
                                            </span>
                                            <small class="text-muted">{{ $photo->created_at->diffForHumans() }}</small>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('photos.show', $photo) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            <a href="{{ route('photos.edit', $photo) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-camera fa-3x text-muted"></i>
                        </div>
                        <h4 class="fw-semibold mb-3">No photos yet</h4>
                        <p class="text-muted mb-4">Add some photos to this album to start training your AI model</p>
                        <a href="{{ route('albums.photos.create', $album) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Add Your First Photo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
