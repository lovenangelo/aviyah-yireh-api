<?php

namespace App\Http\Controllers\API\V1\TrainingMaterial;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingMaterial\BulkDestroyTrainingMaterialsRequest;
use App\Http\Requests\TrainingMaterial\StoreTrainingMaterialRequest;
use App\Http\Requests\TrainingMaterial\UpdateTrainingMaterialRequest;
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
    private const TRAINING_MATERIAL_NOT_FOUND = 'Training material not found.';
    private const TRAINING_MATERIAL = 'App\Models\TrainingMaterial';
    private TrainingMaterialRepository $trainingMaterialRepository;

    public function __construct(TrainingMaterialRepository $trainingMaterialRepository)
    {
        $this->trainingMaterialRepository = $trainingMaterialRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $trainingMaterials = null;
            $filter = $request->query('filter');
            $sort = $request->query('sort');
            $withAuthor = $request->query('with_author');
            $response = null;

            // Retrieve training material based on filter
            if ($filter) {
                $type = $request->query('type');
                switch ($type) {
                    case "document":
                        $trainingMaterials = $this->trainingMaterialRepository->getAllDocuments();
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
                        $response = $this->formatErrorResponse(400, 'Invalid filter type specified in query parameters.', [], 400);
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
                        $response = $this->formatErrorResponse(400, 'Invalid sort type specified in query parameters.', [], 400);
                        break;
                }
            } else {
                $trainingMaterials = $this->trainingMaterialRepository->getAll($withAuthor);
            }

            if ($response === null) {
                $response = $this->formatSuccessResponse($trainingMaterials, "Training materials retrieved successfully.", 200, $request);
            }
            return $response;
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request);
        }
    }

    public function show(Request $request, $id): JsonResponse
    {
        try {
            $trainingMaterial = $this->trainingMaterialRepository->find($id);

            // Check if training material exists
            if (!$trainingMaterial) {
                return $this->formatErrorResponse("404", self::TRAINING_MATERIAL_NOT_FOUND, [], 404);
            }

            return $this->formatSuccessResponse($trainingMaterial, "Training material retrieved successfully!", 200, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is authorized
            $this->authorize("delete", self::TRAINING_MATERIAL);

            // Get training material to delete
            $trainingMaterial = $this->trainingMaterialRepository->find($id);

            if (!$trainingMaterial) {
                return $this->formatErrorResponse("404", self::TRAINING_MATERIAL_NOT_FOUND, [], 404);
            }

            $this->trainingMaterialRepository->delete($trainingMaterial);
            return $this->formatSuccessResponse(null, "Training material deleted successfully!", 200, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request);
        }
    }

    public function bulkDestroy(BulkDestroyTrainingMaterialsRequest $request): JsonResponse
    {
        try {
            // Check if user is authorized
            $this->authorize("bulkDelete", self::TRAINING_MATERIAL);

            // Get validated data
            $ids = $request->validated('ids');

            // Delete multiple roles
            $result = $this->trainingMaterialRepository->bulkDestroy($ids);

            $message = $result['deleted'] . ' training materials deleted successfully';
            $data = [
                'deleted' => $result['deleted'],
                'failed' => $result['failed'],
                'total_attempted' => $result['attempted'],
                'roles_with_users' => $result['has_users'],
            ];

            return $this->formatSuccessResponse($data, $message, 200, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request, "Training material delete many");
        }
    }
}
