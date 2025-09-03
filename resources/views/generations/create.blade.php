@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-5">
                <h1 class="h2 fw-bold mb-2">Generate New Image</h1>
                <p class="text-muted">Create a new AI-generated image using your trained models and themes.</p>
            </div>

            @if($photoModels->count() === 0)
                <!-- No Models Warning -->
                <div class="alert alert-warning border-0 mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-semibold mb-2">No trained models available</h6>
                            <p class="mb-0">You need to create an album and upload photos to train your AI model before you can generate images. 
                                <a href="{{ route('albums.create') }}" class="fw-semibold text-decoration-none">Create an album</a> to get started.</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Generation Form -->
                <div class="card">
                    <div class="card-body p-4">
                        <form action="{{ route('generations.store') }}" method="POST">
                            @csrf
                            
                            <!-- Photo Model Selection -->
                            <div class="mb-4">
                                <label for="photo_model_id" class="form-label">Select Photo Model</label>
                                <select name="photo_model_id" 
                                        id="photo_model_id" 
                                        required 
                                        class="form-select @error('photo_model_id') is-invalid @enderror">
                                    <option value="">Choose a trained model...</option>
                                    @foreach($photoModels as $photoModel)
                                        <option value="{{ $photoModel->id }}" 
                                                {{ old('photo_model_id') == $photoModel->id ? 'selected' : '' }}>
                                            {{ $photoModel->name }} ({{ ucfirst($photoModel->status) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('photo_model_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Theme Selection -->
                            <div class="mb-4">
                                <label for="theme_id" class="form-label">Select Theme</label>
                                <select name="theme_id" 
                                        id="theme_id" 
                                        required 
                                        class="form-select @error('theme_id') is-invalid @enderror">
                                    <option value="">Choose a theme...</option>
                                    @foreach($themes as $theme)
                                        <option value="{{ $theme->id }}" 
                                                {{ old('theme_id') == $theme->id ? 'selected' : '' }}>
                                            {{ $theme->name }} - {{ $theme->description }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('theme_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Custom Prompt -->
                            <div class="mb-4">
                                <label for="prompt" class="form-label">Custom Prompt</label>
                                <textarea name="prompt" 
                                          id="prompt" 
                                          rows="4" 
                                          required 
                                          placeholder="Describe what you want to see in the generated image..."
                                          class="form-control @error('prompt') is-invalid @enderror">{{ old('prompt') }}</textarea>
                                <div class="form-text">Be specific about what you want to see in the image. The theme will be automatically applied to enhance your prompt.</div>
                                @error('prompt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tips Section -->
                            <div class="alert alert-info border-0 mb-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-lightbulb text-info"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="fw-semibold mb-2">Tips for better results:</h6>
                                        <ul class="mb-0 small">
                                            <li>Be specific about poses, expressions, and settings</li>
                                            <li>Mention lighting, style, and mood</li>
                                            <li>Include details about clothing or accessories</li>
                                            <li>Specify background or environment</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('generations.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-magic me-2"></i>Generate Image
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeSelect = document.getElementById('theme_id');
    const promptTextarea = document.getElementById('prompt');
    
    // Auto-fill prompt with theme description when theme is selected
    themeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value && !promptTextarea.value) {
            const themeName = selectedOption.text.split(' - ')[0];
            promptTextarea.value = `Generate a ${themeName.toLowerCase()} style image`;
        }
    });
});
</script>
@endsection
