<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\ReviewServiceInterface as ReviewService;


class ReviewController extends Controller
{

    protected $ReviewService;

    public function __construct(ReviewService $ReviewService)
    {
        $this->ReviewService = $ReviewService;
    }

    public function create(Request $request)
    {
        $response = $this->ReviewService->create($request);

        return response()->json($response);
    }
}
