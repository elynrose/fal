@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <!-- Register Card -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-user-plus fa-2x text-primary"></i>
                            </div>
                            <h1 class="h3 fw-bold">Create Account</h1>
                            <p class="text-muted">Join AI Photo Trainer and start creating amazing images</p>
                        </div>

                        <!-- Register Form -->
                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            
                            <!-- Name Field -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input id="name" 
                                       name="name" 
                                       type="text" 
                                       autocomplete="name" 
                                       required 
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter your full name"
                                       value="{{ old('name') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input id="email" 
                                       name="email" 
                                       type="email" 
                                       autocomplete="email" 
                                       required 
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Enter your email"
                                       value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" 
                                       name="password" 
                                       type="password" 
                                       autocomplete="new-password" 
                                       required 
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Create a password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input id="password_confirmation" 
                                       name="password_confirmation" 
                                       type="password" 
                                       autocomplete="new-password" 
                                       required 
                                       class="form-control"
                                       placeholder="Confirm your password">
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>
                            </div>

                            <!-- Login Link -->
                            <div class="text-center">
                                <p class="text-muted mb-0">
                                    Already have an account? 
                                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Sign in</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
