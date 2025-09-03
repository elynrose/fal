@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold">Training Session Details</h1>
                    <p class="text-gray-600 mt-2">Monitor your AI model training progress.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Training Status -->
                    <div>
                        @if($trainingSession->status === 'completed')
                            <div class="bg-green-50 border border-green-200 rounded-lg p-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-green-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-medium text-green-900 mb-2">Training Completed!</h3>
                                <p class="text-green-700">Your AI model is ready for image generation.</p>
                                @if($trainingSession->completed_at)
                                    <p class="text-sm text-green-600 mt-2">Completed at {{ $trainingSession->completed_at->format('M j, Y g:i A') }}</p>
                                @endif
                            </div>
                        @elseif($trainingSession->status === 'running')
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                                <h3 class="text-lg font-medium text-blue-900 mb-2">Training in Progress...</h3>
                                <p class="text-blue-700">Your AI model is being trained. This may take several minutes.</p>
                                <div class="mt-4 text-sm text-blue-600" id="status-message">
                                    Checking status...
                                </div>
                            </div>
                        @elseif($trainingSession->status === 'failed')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <h3 class="text-lg font-medium text-red-900 mb-2">Training Failed</h3>
                                <p class="text-red-700">{{ $trainingSession->error_message ?? 'An error occurred during training.' }}</p>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-yellow-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-medium text-yellow-900 mb-2">Training Pending</h3>
                                <p class="text-yellow-700">Training session is queued and will start soon.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">Training Details</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <div class="mt-1">
                                        @if($trainingSession->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @elseif($trainingSession->status === 'running')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Running
                                            </span>
                                        @elseif($trainingSession->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        @elseif($trainingSession->status === 'failed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Failed
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Photo Model</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $trainingSession->photoModel->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Session ID</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $trainingSession->session_id }}</p>
                                </div>

                                @if($trainingSession->started_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Started At</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $trainingSession->started_at->format('M j, Y g:i A') }}</p>
                                </div>
                                @endif

                                @if($trainingSession->completed_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Completed At</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $trainingSession->completed_at->format('M j, Y g:i A') }}</p>
                                </div>
                                @endif

                                @if($trainingSession->error_message)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Error Message</label>
                                    <p class="mt-1 text-sm text-red-600">{{ $trainingSession->error_message }}</p>
                                </div>
                                @endif
                            </div>

                            <div class="mt-6 flex space-x-3">
                                <a href="{{ route('training.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Back to List
                                </a>
                                @if($trainingSession->status === 'completed')
                                    <a href="{{ route('generations.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Generate Images
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Training Configuration -->
                @if($trainingSession->training_config)
                <div class="mt-8">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Training Configuration</h3>
                        <pre class="bg-white p-4 rounded border text-sm overflow-x-auto">{{ json_encode($trainingSession->training_config, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif

                <!-- Training Results -->
                @if($trainingSession->training_results)
                <div class="mt-8">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Training Results</h3>
                        <pre class="bg-white p-4 rounded border text-sm overflow-x-auto">{{ json_encode($trainingSession->training_results, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($trainingSession->status === 'running')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusMessage = document.getElementById('status-message');
    let pollCount = 0;
    const maxPolls = 120; // 10 minutes with 5-second intervals

    function checkStatus() {
        fetch('{{ route("training.status", $trainingSession) }}')
            .then(response => response.json())
            .then(data => {
                pollCount++;
                
                if (data.status === 'completed') {
                    // Reload the page to show the completed status
                    window.location.reload();
                } else if (data.status === 'failed') {
                    // Reload the page to show the error
                    window.location.reload();
                } else if (pollCount < maxPolls) {
                    // Continue polling
                    setTimeout(checkStatus, 5000); // Poll every 5 seconds
                } else {
                    // Stop polling after max attempts
                    statusMessage.textContent = 'Training is taking longer than expected. Please refresh the page.';
                }
            })
            .catch(error => {
                console.error('Error checking status:', error);
                if (pollCount < maxPolls) {
                    setTimeout(checkStatus, 5000);
                }
            });
    }

    // Start polling after 2 seconds
    setTimeout(checkStatus, 2000);
});
</script>
@endif
@endsection
