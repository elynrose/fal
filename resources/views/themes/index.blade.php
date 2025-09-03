@extends('layouts.app')

@section('content')
<div class="py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center">
                <h1 class="h2 fw-bold mb-2">Available Themes</h1>
                <p class="text-muted">Choose from our collection of themes to generate unique AI images.</p>
            </div>
        </div>
    </div>

    <!-- Themes Grid -->
    <div class="row">
        @foreach($themes as $theme)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <!-- Theme Header -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                                @if($theme->icon === 'briefcase')
                                    <i class="fas fa-briefcase"></i>
                                @elseif($theme->icon === 'plane')
                                    <i class="fas fa-plane"></i>
                                @elseif($theme->icon === 'tshirt')
                                    <i class="fas fa-tshirt"></i>
                                @elseif($theme->icon === 'smile')
                                    <i class="fas fa-smile"></i>
                                @elseif($theme->icon === 'palette')
                                    <i class="fas fa-palette"></i>
                                @elseif($theme->icon === 'trophy')
                                    <i class="fas fa-trophy"></i>
                                @elseif($theme->icon === 'heart')
                                    <i class="fas fa-heart"></i>
                                @elseif($theme->icon === 'tree')
                                    <i class="fas fa-tree"></i>
                                @else
                                    <i class="fas fa-image"></i>
                                @endif
                            </div>
                            <h3 class="h5 fw-semibold mb-0">{{ $theme->name }}</h3>
                        </div>
                        
                        <!-- Theme Description -->
                        <p class="text-muted mb-3">{{ $theme->description }}</p>
                        
                        <!-- Prompt Template -->
                        <div class="bg-light rounded-3 p-3 mb-4">
                            <p class="text-muted small fw-semibold mb-1">Prompt Template:</p>
                            <p class="small mb-0">{{ Str::limit($theme->prompt_template, 100) }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <a href="{{ route('generations.create') }}?theme={{ $theme->id }}" 
                               class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-magic me-1"></i>Generate
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
