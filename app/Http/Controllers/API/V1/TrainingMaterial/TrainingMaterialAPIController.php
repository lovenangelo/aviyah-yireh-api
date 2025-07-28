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
    private TrainingMaterialRepository $trainingMaterialRepository;

    public function __construct(TrainingMaterialRepository $trainingMaterialRepository)
    {
        $this->trainingMaterialRepository = $trainingMaterialRepository;
    }
}
