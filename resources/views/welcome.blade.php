@extends('layouts.app')

@section('content')
<div class="py-5">
    <!-- Hero Section -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 text-center">
            <div class="mb-4">
                <i class="fas fa-camera fa-3x text-primary mb-3"></i>
            </div>
            <h1 class="display-5 fw-bold mb-3">Train Your Photos with AI</h1>
            <p class="lead text-muted mb-4">Upload your photos, train AI models, and generate stunning images in different themes like corporate, travel, fashion, and more.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket me-2"></i>Get Started
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="h3 fw-bold mb-3">AI-Powered</h2>
                    <h3 class="h5 text-muted mb-3">Everything you need to create amazing AI-generated images.</h3>
                    <p class="text-muted mb-4">Our platform uses advanced AI models to train on your photos and generate stunning images in various themes and styles.</p>
                </div>
                <div class="col-md-6">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="h6 fw-semibold mb-2">Upload & Train</h4>
                                    <p class="text-muted small mb-0">Upload your photos and let our AI train a personalized model based on your unique style and features.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-success bg-opacity-10 text-success">
                                        <i class="fas fa-palette"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="h6 fw-semibold mb-2">Multiple Themes</h4>
                                    <p class="text-muted small mb-0">Generate images in various themes including corporate, travel, fashion, casual, artistic, and more.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="h6 fw-semibold mb-2">High Quality</h4>
                                    <p class="text-muted small mb-0">Get high-quality, professional-looking images that maintain your unique characteristics across all themes.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="h4 fw-bold mb-0">How It Works</h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3 text-center">
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                                <i class="fas fa-camera fa-lg"></i>
                            </div>
                            <h5 class="fw-semibold mb-2">1. Upload Photo</h5>
                            <p class="text-muted small">Upload a clear, high-quality photo of yourself</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="stats-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                                <i class="fas fa-brain fa-lg"></i>
                            </div>
                            <h5 class="fw-semibold mb-2">2. Train Model</h5>
                            <p class="text-muted small">Our AI learns your unique features and style</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                                <i class="fas fa-palette fa-lg"></i>
                            </div>
                            <h5 class="fw-semibold mb-2">3. Choose Theme</h5>
                            <p class="text-muted small">Select from various professional themes</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="stats-icon bg-info bg-opacity-10 text-info mx-auto mb-3">
                                <i class="fas fa-magic fa-lg"></i>
                            </div>
                            <h5 class="fw-semibold mb-2">4. Generate</h5>
                            <p class="text-muted small">Create stunning AI-generated images</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
