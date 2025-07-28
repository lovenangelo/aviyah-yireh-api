<?php

namespace App\Http\Controllers\API\V1\TrainingMaterial;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Repositories\TrainingMaterialRepository;

/**
 * @OA\Tag(
 *     name="Training Materials",
 *     description="Endpoints for managing training materials, including CRUD operations and sorting actions"
 * )
 */
class TrainingMaterialAPIController extends Controller
{
    use ApiResponse;
    private const TRAINING_MATERIAL = 'App\Models\TrainingMaterial';
    private TrainingMaterialRepository $trainingMaterialRepository;

    public function __construct(TrainingMaterialRepository $trainingMaterialRepository)
    {
        $this->trainingMaterialRepository = $trainingMaterialRepository;
    }

    /**
     * Display a listing of the training materials.
     *
     * @OA\Get(
     *     path="/api/v1/training-materials",
     *     summary="Get list of training materials",
     *     tags={"Training Materials"},
     *     @OA\Response(response=200, description="List of users")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $trainingMaterials = $this->trainingMaterialRepository->getAll();
            return $this->formatSuccessResponse($trainingMaterials, "Training materials retrieved successfully.", 200, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request);
        }
    }

    /**
     * Upload a training material
     *
     * @OA\Post(
     *     path="/api/v1/training-materials",
     *     summary="Upload a training material",
     *     tags={"Training Materials"},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="roles", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Video uploaded successfully.")
     *     @OA\Response(response=403, description="Unauthorized to perform action.")
     * )
     */
}
