<?php

namespace App\Http\Controllers\API\V1\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Traits\ApiResponse;

class CompanyController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::with('job_types')->get();

        return $this->formatSuccessResponse($companies);
    }
}
