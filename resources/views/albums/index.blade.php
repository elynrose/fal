@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 fw-bold mb-2">My Albums</h1>
                <p class="text-muted">Organize your photos into albums for better AI training</p>
            </div>
            <a href="{{ route('albums.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create New Album
            </a>
        </div>

        <!-- Albums Grid -->
        @if($albums->count() > 0)
            <div class="row g-4">
                @foreach($albums as $album)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 album-card">
                            <div class="card-body p-4">
                                <!-- Album Header -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title fw-semibold mb-1">{{ $album->name }}</h5>
                                        <p class="text-muted small mb-0">{{ $album->photos_count }} photos</p>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('albums.show', $album) }}">
                                                <i class="fas fa-eye me-2"></i>View Album
                                            </a></li>
                                            <li><a class="dropdown-item" href="{{ route('albums.edit', $album) }}">
                                                <i class="fas fa-edit me-2"></i>Edit Album
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('albums.destroy', $album) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('Are you sure you want to delete this album? This will also delete all photos in the album.')">
                                                        <i class="fas fa-trash me-2"></i>Delete Album
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Album Description -->
                                @if($album->description)
                                    <p class="card-text text-muted mb-3">{{ Str::limit($album->description, 100) }}</p>
                                @endif

                                <!-- Album Status -->
                                <div class="mb-3">
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
                                    <span class="badge bg-{{ $statusColors[$status] }}">{{ $statusLabels[$status] }}</span>
                                </div>

                                <!-- Album Actions -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('albums.show', $album) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>View Album
                                    </a>
                                    
                                    @if($album->photos_count > 0)
                                        <a href="{{ route('albums.photos.create', $album) }}" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-plus me-1"></i>Add Photos
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Album Footer -->
                            <div class="card-footer bg-light border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Created {{ $album->created_at->diffForHumans() }}
                                    </small>
                                    @if($album->model_id)
                                        <small class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>Trained
                                        </small>
                                    @endif
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
                    <i class="fas fa-images fa-3x text-muted"></i>
                </div>
                <h4 class="fw-semibold mb-3">No albums yet</h4>
                <p class="text-muted mb-4">Create your first album to start organizing photos for AI training</p>
                <a href="{{ route('albums.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Create Your First Album
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
