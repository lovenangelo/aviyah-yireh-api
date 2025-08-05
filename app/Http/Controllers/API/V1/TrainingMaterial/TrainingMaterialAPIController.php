<?php

namespace App\Http\Controllers\API\V1\TrainingMaterial;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingMaterial\BulkDestroyTrainingMaterialsRequest;
use App\Http\Resources\CustomPaginatedCollection;
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

    private function getTrainingMaterials(Request $request, $perPage = null)
    {
        $filter = $request->query('filter');
        $sort = $request->query('sort');

        if ($filter) {
            return $this->handleFilteredResults($request, $perPage);
        }

        if ($sort) {
            return $this->handleSortedResults($request, $perPage);
        }

        return $this->trainingMaterialRepository->getAll($perPage);
    }

    private function handleFilteredResults(Request $request, $perPage = null)
    {
        $type = $request->query('type');

        return match ($type) {
            'document' => $this->trainingMaterialRepository->getAllDocuments($perPage),
            'video' => $this->trainingMaterialRepository->getAllVideos($perPage),
            'image' => $this->trainingMaterialRepository->getAllImage($perPage),
            'audio' => $this->trainingMaterialRepository->getAllAudio($perPage),
            'english' => $this->trainingMaterialRepository->getAllEnglish($perPage),
            'tagalog' => $this->trainingMaterialRepository->getAllTagalog($perPage),
            default => $this->formatErrorResponse(400, 'Invalid filter type specified in query parameters.', [], 400)
        };
    }

    private function handleSortedResults(Request $request, $perPage = null)
    {
        $sortBy = $request->query('sort_by');

        return match ($sortBy) {
            'popularity' => $this->trainingMaterialRepository->getVideosByPopularity($perPage),
            'dateUploaded' => $this->trainingMaterialRepository->getVideosByDateUploaded($perPage),
            default => $this->formatErrorResponse(400, 'Invalid sort type specified in query parameters.', [], 400)
        };
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page');
            $withAuthor = $request->query('with_author');

            $trainingMaterials = $this->getTrainingMaterials($request, $perPage);

            if ($trainingMaterials instanceof JsonResponse) {
                return $trainingMaterials;
            }

            if ($withAuthor && !$trainingMaterials instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $trainingMaterials = $trainingMaterials->load('user');
            }

            $trainingMaterials = new CustomPaginatedCollection($trainingMaterials, $request->query('include_links', false));
            return $this->formatSuccessResponse($trainingMaterials, "Training materials retrieved successfully.", 200, $request);
        } catch (\Throwable $e) {
            return $this->handleApiException($e, $request);
        }
    }

    public function show(Request $request, $id): JsonResponse
    {
        try {
            $withAuthor = $request->query('with_author');
            $trainingMaterial = $this->trainingMaterialRepository->find($id, $withAuthor);

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
