@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="container">
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-lg-8">
                <h1 class="h2 fw-bold mb-3">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="text-muted mb-0">Manage your AI photo training albums and generate amazing images with your trained models.</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ auth()->user()->albums()->count() }}</h3>
                        <p class="stats-label">Total Albums</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ auth()->user()->photos()->count() }}</h3>
                        <p class="stats-label">Total Photos</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ auth()->user()->albums()->where('status', 'completed')->count() }}</h3>
                        <p class="stats-label">Trained Models</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-magic"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ auth()->user()->generatedImages()->count() }}</h3>
                        <p class="stats-label">Generated Images</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="action-content">
                        <h4 class="action-title">Create New Album</h4>
                        <p class="action-description">Start a new photo collection for AI training</p>
                        <a href="{{ route('albums.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Album
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-magic"></i>
                    </div>
                    <div class="action-content">
                        <h4 class="action-title">Generate Images</h4>
                        <p class="action-description">Create new images with your trained models</p>
                        <a href="{{ route('generations.create') }}" class="btn btn-primary">
                            <i class="fas fa-magic me-2"></i>Generate Images
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Albums -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h4 fw-semibold mb-0">Recent Albums</h3>
                    <a href="{{ route('albums.index') }}" class="btn btn-outline-primary btn-sm">
                        View All Albums
                    </a>
                </div>
                
                @php
                    $recentAlbums = auth()->user()->albums()->withCount('photos')->latest()->take(3)->get();
                @endphp
                
                @if($recentAlbums->count() > 0)
                    <div class="row g-4">
                        @foreach($recentAlbums as $album)
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title fw-semibold mb-0">{{ $album->name }}</h6>
                                            <span class="badge bg-{{ $album->getOverallStatus() === 'completed' ? 'success' : 'warning' }}">
                                                {{ $album->photos_count }} photos
                                            </span>
                                        </div>
                                        @if($album->description)
                                            <p class="card-text small text-muted mb-2">{{ Str::limit($album->description, 60) }}</p>
                                        @endif
                                        <a href="{{ route('albums.show', $album) }}" class="btn btn-outline-primary btn-sm">
                                            View Album
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-images fa-2x text-muted"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">No albums yet</h5>
                        <p class="text-muted mb-3">Create your first album to start organizing photos for AI training</p>
                        <a href="{{ route('albums.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Your First Album
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
