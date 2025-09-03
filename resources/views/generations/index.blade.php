@extends('layouts.app')

@section('content')
<div class="py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold mb-2">Generated Images</h1>
                    <p class="text-muted mb-0">View and manage your AI-generated images</p>
                </div>
                <a href="{{ route('generations.create') }}" class="btn btn-primary">
                    <i class="fas fa-magic me-2"></i>Generate New Image
                </a>
            </div>
        </div>
    </div>

    @if($generatedImages->count() > 0)
        <!-- Generated Images Grid -->
        <div class="row">
            @foreach($generatedImages as $generatedImage)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="photo-card">
                        <!-- Image or Status Display -->
                        @if($generatedImage->status === 'completed' && $generatedImage->image_path)
                            <img src="{{ Storage::url($generatedImage->image_path) }}" 
                                 alt="Generated Image" 
                                 class="photo-image">
                        @elseif($generatedImage->status === 'generating')
                            <div class="photo-image bg-primary bg-opacity-10 d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <div class="spinner-border text-primary mb-2" role="status">
                                        <span class="visually-hidden">Generating...</span>
                                    </div>
                                    <p class="text-primary small mb-0">Generating...</p>
                                </div>
                            </div>
                        @elseif($generatedImage->status === 'failed')
                            <div class="photo-image bg-danger bg-opacity-10 d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                                    <p class="text-danger small mb-0">Failed</p>
                                </div>
                            </div>
                        @else
                            <div class="photo-image bg-light d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small mb-0">Pending</p>
                                </div>
                            </div>
                        @endif

                        <div class="photo-content">
                            <!-- Header with Status -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h3 class="h6 fw-semibold mb-0">{{ $generatedImage->photoModel->name }}</h3>
                                <span class="status-badge 
                                    @if($generatedImage->status === 'pending') bg-warning bg-opacity-10 text-warning
                                    @elseif($generatedImage->status === 'generating') bg-primary bg-opacity-10 text-primary
                                    @elseif($generatedImage->status === 'completed') bg-success bg-opacity-10 text-success
                                    @else bg-danger bg-opacity-10 text-danger @endif">
                                    {{ ucfirst($generatedImage->status) }}
                                </span>
                            </div>

                            <!-- Theme and Prompt -->
                            <p class="text-muted small mb-2">
                                <i class="fas fa-palette me-1"></i>{{ $generatedImage->theme->name }} Theme
                            </p>
                            <p class="text-muted small mb-3">{{ Str::limit($generatedImage->prompt_used, 80) }}</p>

                            <!-- Generation Date -->
                            @if($generatedImage->generated_at)
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-calendar me-1"></i>{{ $generatedImage->generated_at->format('M j, Y g:i A') }}
                                </p>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('generations.show', $generatedImage) }}" 
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                @if($generatedImage->status === 'completed')
                                    <a href="{{ Storage::url($generatedImage->image_path) }}" 
                                       download="generated-image-{{ $generatedImage->id }}.jpg"
                                       class="btn btn-success btn-sm flex-fill">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                @endif
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
                            <i class="fas fa-magic fa-3x text-muted"></i>
                        </div>
                        <h3 class="h4 fw-semibold mb-2">No generated images yet</h3>
                        <p class="text-muted mb-4">Start by generating your first AI image using your trained models.</p>
                        <a href="{{ route('generations.create') }}" class="btn btn-primary">
                            <i class="fas fa-magic me-2"></i>Generate Your First Image
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
