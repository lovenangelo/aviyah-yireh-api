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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"user_id", "category_id", "language_id", "title", "description", "path", "thumbnail_path", "is_visible", "duration"},
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="language_id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="path", type="string", format="binary"),
     *                 @OA\Property(property="thumbnail_path", type="string", format="binary"),
     *                 @OA\Property(property="is_visible", type="boolean"),
     *                 @OA\Property(property="duration", type="integer"),
     *                 @OA\Property(property="expiration_date", type="string", format="date")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Training material uploaded successfully!"),
     *     @OA\Response(response=403, description="Unauthorized to perform action.")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Check if user is authorized
            $this->authorize("create", self::TRAINING_MATERIAL);

            $trainingMaterial = $this->trainingMaterialRepository->upload($request->all());

            return $this->formatSuccessResponse($trainingMaterial, "Training material uploaded successfully!", 201, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request);
        }
    }

    /**
     * Upload a training material
     *
     * @OA\Put(
     * path="/api/v1/training-materials/{id}",
     *     summary="Edit a training material",
     *     tags={"Training Materials"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"user_id", "category_id", "language_id", "title", "description", "path", "thumbnail_path", "is_visible", "duration"},
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="language_id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="path", type="string", format="binary"),
     *                 @OA\Property(property="thumbnail_path", type="string", format="binary"),
     *                 @OA\Property(property="is_visible", type="boolean"),
     *                 @OA\Property(property="duration", type="string", format="date")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Training material updated successfully!"),
     *     @OA\Response(response=403, description="Unauthorized to perform action."),
     *     @OA\Response(response=404, description="Training material not found."),
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is authorized
            $this->authorize("update", self::TRAINING_MATERIAL);

            $trainingMaterial = $this->trainingMaterialRepository->find($id);

            // Check if trainng material exists
            if (!$trainingMaterial) {
                return $this->formatErrorResponse("404", "Training material not found.", [], 400);
            }

            // Prepare update data
            $data = $request->all();

            $this->trainingMaterialRepository->update($data, $id);

            return $this->formatSuccessResponse($data, "Training material updated successfully!", 200, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request);
        }
    }
}
