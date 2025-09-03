<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class FalAIService
{
    private $client;
    private $apiKey;
    private $baseUrl = 'https://fal.run/fal-ai';

    public function __construct()
    {
        $this->apiKey = config('services.fal.key');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Key ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Create a zip archive from multiple photos and upload to cloud storage
     */
    public function createAndUploadPhotoArchive($photoPaths, $albumName)
    {
        try {
            // Create a temporary zip file
            $zipPath = storage_path('app/temp/' . uniqid() . '_' . $albumName . '.zip');
            $zipDir = dirname($zipPath);
            
            if (!is_dir($zipDir)) {
                mkdir($zipDir, 0755, true);
            }

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Could not create zip file');
            }

            // Add each photo to the zip
            foreach ($photoPaths as $photoPath) {
                if (Storage::disk('public')->exists($photoPath)) {
                    $photoContent = Storage::disk('public')->get($photoPath);
                    $fileName = basename($photoPath);
                    $zip->addFromString($fileName, $photoContent);
                }
            }

            $zip->close();

            // Upload the zip file to cloud storage (using public disk for now)
            $cloudPath = 'training-archives/' . basename($zipPath);
            Storage::disk('public')->put($cloudPath, file_get_contents($zipPath));

            // Get the public URL
            $publicUrl = Storage::disk('public')->url($cloudPath);

            // Clean up temporary zip file
            unlink($zipPath);

            return [
                'success' => true,
                'url' => $publicUrl,
                'local_path' => $cloudPath
            ];

        } catch (\Exception $e) {
            Log::error('Fal AI Archive Creation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Start training a model with a zip archive of photos
     */
    public function startTraining($zipArchiveUrl, $modelName, $triggerWord = null)
    {
        try {
            $payload = [
                'input' => [
                    'images_data_url' => $zipArchiveUrl,
                    'create_masks' => true,
                    'steps' => 1000
                ]
            ];

            // Add trigger word if provided
            if ($triggerWord) {
                $payload['input']['trigger_word'] = $triggerWord;
            }

            $response = $this->client->post('/fal-ai/flux-lora-fast-training', [
                'json' => $payload
            ]);

            $data = json_decode($response->getBody(), true);
            return [
                'success' => true,
                'session_id' => $data['session_id'] ?? null,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Fal AI Training Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check training status
     */
    public function checkTrainingStatus($sessionId)
    {
        try {
            $response = $this->client->get("/fast-sdxl/train/{$sessionId}");
            $data = json_decode($response->getBody(), true);
            
            return [
                'success' => true,
                'status' => $data['status'] ?? 'unknown',
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Fal AI Status Check Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate image using trained model
     */
    public function generateImage($modelId, $prompt, $theme = null)
    {
        try {
            $payload = [
                'input' => [
                    'model_id' => $modelId,
                    'prompt' => $prompt,
                    'num_inference_steps' => 30,
                    'guidance_scale' => 7.5,
                ]
            ];

            // Add theme-specific parameters if provided
            if ($theme) {
                $payload['input']['prompt'] = $theme->prompt_template . ' ' . $prompt;
            }

            $response = $this->client->post('/fast-sdxl/inference', [
                'json' => $payload
            ]);

            $data = json_decode($response->getBody(), true);
            
            return [
                'success' => true,
                'generation_id' => $data['generation_id'] ?? null,
                'image_url' => $data['image']['url'] ?? null,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Fal AI Generation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check generation status
     */
    public function checkGenerationStatus($generationId)
    {
        try {
            $response = $this->client->get("/fast-sdxl/inference/{$generationId}");
            $data = json_decode($response->getBody(), true);
            
            return [
                'success' => true,
                'status' => $data['status'] ?? 'unknown',
                'image_url' => $data['image']['url'] ?? null,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Fal AI Generation Status Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Upload image to fal and get URL
     */
    public function uploadImage($imagePath)
    {
        try {
            $imageContent = Storage::get($imagePath);
            $base64Image = base64_encode($imageContent);
            
            $response = $this->client->post('/upload', [
                'json' => [
                    'image' => $base64Image
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            return [
                'success' => true,
                'url' => $data['url'] ?? null,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Fal AI Upload Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
