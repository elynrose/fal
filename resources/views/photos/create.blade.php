@extends('layouts.app')

@section('content')
<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-5">
                <h1 class="h2 fw-bold mb-2">Upload Photos</h1>
                <p class="text-muted">Upload one or multiple photos to train your AI models. Choose clear, high-quality images for best results.</p>
            </div>

            <!-- Upload Form -->
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('photos.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        
                        <!-- Photo Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label">Base Name for Photos</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Enter a base name for your photos">
                            <div class="form-text">Each photo will be named with this base name plus the original filename.</div>
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
                                      placeholder="Add a description to help identify these photos">{{ old('description') }}</textarea>
                            <div class="form-text">This description will apply to all uploaded photos.</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="mb-4">
                            <label for="photos" class="form-label">Select Photos</label>
                            <div class="border-2 border-dashed border-light rounded-3 p-5 text-center" id="dropZone">
                                <div class="mb-3">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                </div>
                                <h5 class="fw-semibold mb-2">Upload your photos</h5>
                                <p class="text-muted mb-3">Drag and drop multiple images here, or click to browse</p>
                                <input type="file" 
                                       id="photos" 
                                       name="photos[]" 
                                       accept="image/*" 
                                       multiple
                                       required 
                                       class="form-control @error('photos.*') is-invalid @enderror">
                                <p class="text-muted small mt-2 mb-0">PNG, JPG, JPEG up to 10MB each. You can select multiple files.</p>
                                @error('photos.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- File Preview -->
                        <div class="mb-4" id="filePreview" style="display: none;">
                            <label class="form-label">Selected Photos</label>
                            <div class="row g-3" id="previewContainer">
                                <!-- Preview images will be inserted here -->
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
                                        <li>Use clear, high-resolution photos</li>
                                        <li>Ensure good lighting and contrast</li>
                                        <li>Choose photos with your face clearly visible</li>
                                        <li>Avoid group photos or heavily edited images</li>
                                        <li>Upload multiple photos from different angles for better training</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('photos.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-upload me-2"></i>Upload Photos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('photos');
    const dropZone = document.getElementById('dropZone');
    const filePreview = document.getElementById('filePreview');
    const previewContainer = document.getElementById('previewContainer');
    const submitBtn = document.getElementById('submitBtn');

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // Drag and drop functionality
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-primary');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-primary');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-primary');
        
        const files = e.dataTransfer.files;
        fileInput.files = files;
        handleFiles(files);
    });

    // Handle selected files
    function handleFiles(files) {
        if (files.length === 0) {
            filePreview.style.display = 'none';
            return;
        }

        // Clear previous previews
        previewContainer.innerHTML = '';
        
        // Create preview for each file
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'col-md-4 col-sm-6';
                    previewDiv.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Preview">
                            <div class="card-body p-2">
                                <p class="card-text small text-muted mb-0">${file.name}</p>
                                <p class="card-text small text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                            </div>
                        </div>
                    `;
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            }
        });

        filePreview.style.display = 'block';
        
        // Update submit button text
        if (files.length === 1) {
            submitBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Upload Photo';
        } else {
            submitBtn.innerHTML = `<i class="fas fa-upload me-2"></i>Upload ${files.length} Photos`;
        }
    }

    // Form validation
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        const files = fileInput.files;
        if (files.length === 0) {
            e.preventDefault();
            alert('Please select at least one photo to upload.');
            return;
        }

        // Check file sizes
        for (let file of files) {
            if (file.size > 10 * 1024 * 1024) { // 10MB
                e.preventDefault();
                alert(`File "${file.name}" is too large. Maximum size is 10MB.`);
                return;
            }
        }
    });
});
</script>
@endsection
