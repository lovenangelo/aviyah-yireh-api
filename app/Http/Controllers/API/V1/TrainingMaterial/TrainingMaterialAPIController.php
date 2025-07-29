<?php

namespace App\Http\Controllers\API\V1\TrainingMaterial;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Repositories\TrainingMaterialRepository;
use getID3;

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
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         description="Set to true when filtering",
     *         @OA\Schema(type="string"),
     *         style="form",
     *         explode=false
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         required=false,
     *         description="Filter type",
     *         @OA\Schema(type="string", enum={"pdf","video","image","audio","english","tagalog"}),
     *         style="form",
     *         explode=false
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         required=false,
     *         description="Sort to true when sorting",
     *         @OA\Schema(type="string"),
     *         style="form",
     *         explode=false
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         required=false,
     *         description="Sort by field (used when sort is set)",
     *         @OA\Schema(type="string", enum={"popularity", "dateUploaded"}),
     *         style="form",
     *         explode=false
     *     ),
     *     @OA\Response(response=200, description="List of training materials"),
     *     @OA\Response(response=400, description="Invalid filter type specified in query parameters.")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $trainingMaterials = null;
            $filter = $request->query('filter');
            $sort = $request->query('sort');

            // Retrieve training material based on filter
            if ($filter) {
                $type = $request->query('type');
                switch ($type) {
                    case "pdf":
                        $trainingMaterials = $this->trainingMaterialRepository->getAllPdf();
                        break;
                    case "video":
                        $trainingMaterials = $this->trainingMaterialRepository->getAllVideos();
                        break;
                    case "image":
                        $trainingMaterials = $this->trainingMaterialRepository->getAllImage();
                        break;
                    case "audio":
                        $trainingMaterials = $this->trainingMaterialRepository->getAllAudio();
                        break;
                    case "english":
                        $trainingMaterials = $this->trainingMaterialRepository->getAllEnglish();
                        break;
                    case "tagalog":
                        $trainingMaterials = $this->trainingMaterialRepository->getAllTagalog();
                        break;
                    default:
                        return $this->formatErrorResponse(400, 'Invalid filter type specified in query parameters.', [], 400);
                        break;
                }
                // Retrieve training material based on sort
            } elseif ($sort) {
                $sortBy = $request->query('sortBy');
                switch ($sortBy) {
                    case 'popularity':
                        $trainingMaterials = $this->trainingMaterialRepository->getVideosByPopularity();
                        break;
                    case 'dateUploaded':
                        $trainingMaterials = $this->trainingMaterialRepository->getVideosByDateUploaded();
                        break;
                    default:
                        return $this->formatErrorResponse(400, 'Invalid sort type specified in query parameters.', [], 400);
                        break;
                }
            } else {
                // Otherview return all list of training material
                $trainingMaterials = $this->trainingMaterialRepository->getAll();
            }
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

            // Upload the file data
            $this->trainingMaterialRepository->upload($request->all());

            return $this->formatSuccessResponse($request->all(), "Training material uploaded successfully!", 201, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request);
        }
    }


    /**
     * Display the specified training material.
     * @OA\Get(
     *     path="/api/v1/training-material/{id}",
     *     summary="Get a training material by ID",
     *     tags={"Training Materials"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Training material details"),
     *     @OA\Response(response=404, description="Training material not found")
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $trainingMaterial = $this->trainingMaterialRepository->find($id);

            // Check if training material exists
            if (!$trainingMaterial) {
                return $this->formatErrorResponse("404", "Training material not found.", [], 404);
            }

            return $this->formatSuccessResponse($trainingMaterial, "Training material retrieved successfully!", 200, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request);
        }
    }

    /**
     * Update a training material
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

    /**
     * Remove the specified training material.
     * @OA\Delete(
     *     path="/api/v1/training-materials/{id}",
     *     summary="Delete a training material",
     *     tags={"Training Materials"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Training material deleted successfully!"),
     *     @OA\Response(response=404, description="Training material not found."),
     *     @OA\Response(response=403, description="Unauthorized action."),
     * )
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is authorized
            $this->authorize("delete", self::TRAINING_MATERIAL);

            // Get training material to delete
            $trainingMaterial = $this->trainingMaterialRepository->find($id);

            if (!$trainingMaterial) {
                return $this->formatErrorResponse("404", "Training material not found.", [], 404);
            }

            $this->trainingMaterialRepository->delete($trainingMaterial);
            return $this->formatSuccessResponse(null, "Training material deleted successfully!", 200, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request);
        }
    }
}
