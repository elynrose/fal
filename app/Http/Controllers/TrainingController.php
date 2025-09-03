<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Services\FalAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TrainingController extends Controller
{
    use AuthorizesRequests;
    
    protected $falService;

    public function __construct(FalAIService $falService)
    {
        $this->falService = $falService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trainingSessions = Auth::user()->trainingSessions()->with('photoModel')->latest()->get();
        return view('training.index', compact('trainingSessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('training.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // This method is not used as training is initiated from PhotoController
        return redirect()->route('training.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainingSession $trainingSession)
    {
        $this->authorize('view', $trainingSession);
        
        // Check training status if still running
        if ($trainingSession->status === 'running' && $trainingSession->session_id) {
            $statusResult = $this->falService->checkTrainingStatus($trainingSession->session_id);
            
            if ($statusResult['success']) {
                if ($statusResult['status'] === 'completed') {
                    $trainingSession->update([
                        'status' => 'completed',
                        'training_results' => $statusResult['data'],
                        'completed_at' => now()
                    ]);
                    
                    // Update album with model ID
                    $album = $trainingSession->album;
                    $album->update([
                        'status' => 'completed',
                        'model_id' => $statusResult['data']['model_id'] ?? null
                    ]);
                    
                    // Update all photos in the album
                    $album->photos()->update(['status' => 'completed']);
                } elseif ($statusResult['status'] === 'failed') {
                    $trainingSession->update([
                        'status' => 'failed',
                        'error_message' => 'Training failed on fal AI side',
                        'completed_at' => now()
                    ]);
                    
                    // Update album status
                    $album = $trainingSession->album;
                    $album->update(['status' => 'failed']);
                    
                    // Update all photos in the album
                    $album->photos()->update(['status' => 'failed']);
                }
            }
        }
        
        return view('training.show', compact('trainingSession'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrainingSession $trainingSession)
    {
        $this->authorize('update', $trainingSession);
        return view('training.edit', compact('trainingSession'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrainingSession $trainingSession)
    {
        $this->authorize('update', $trainingSession);

        $request->validate([
            'training_config' => 'nullable|array'
        ]);

        $trainingSession->update([
            'training_config' => $request->training_config
        ]);

        return redirect()->route('training.show', $trainingSession)
            ->with('success', 'Training session updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainingSession $trainingSession)
    {
        $this->authorize('delete', $trainingSession);

        $trainingSession->delete();

        return redirect()->route('training.index')
            ->with('success', 'Training session deleted successfully!');
    }

    /**
     * Check training status via AJAX
     */
    public function checkStatus(TrainingSession $trainingSession)
    {
        $this->authorize('view', $trainingSession);

        if ($trainingSession->status === 'running' && $trainingSession->session_id) {
            $statusResult = $this->falService->checkTrainingStatus($trainingSession->session_id);
            
            if ($statusResult['success']) {
                if ($statusResult['status'] === 'completed') {
                    $trainingSession->update([
                        'status' => 'completed',
                        'training_results' => $statusResult['data'],
                        'completed_at' => now()
                    ]);
                    
                    // Update album with model ID
                    $album = $trainingSession->album;
                    $album->update([
                        'status' => 'completed',
                        'model_id' => $statusResult['data']['model_id'] ?? null
                    ]);
                    
                    // Update all photos in the album
                    $album->photos()->update(['status' => 'completed']);
                } elseif ($statusResult['status'] === 'failed') {
                    $trainingSession->update([
                        'status' => 'failed',
                        'error_message' => 'Training failed on fal AI side',
                        'completed_at' => now()
                    ]);
                    
                    // Update album status
                    $album = $trainingSession->album;
                    $album->update(['status' => 'failed']);
                    
                    // Update all photos in the album
                    $album->photos()->update(['status' => 'failed']);
                }
            }
        }

        return response()->json([
            'status' => $trainingSession->status,
            'message' => $trainingSession->error_message
        ]);
    }
}
