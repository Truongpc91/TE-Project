<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Repositories\Interfaces\SourceReponsitoryInterface  as SourceReponsitory;

use App\Services\Interfaces\SourceServiceInterface as SourceService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SourceController extends Controller
{
    protected $SourceReponsitory;
    protected $SourceService;

    public function __construct(
        SourceReponsitory $SourceReponsitory,
        SourceService $SourceService,

    ) {
        $this->SourceService = $SourceService;
        $this->SourceReponsitory = $SourceReponsitory;
    }

    public function getAllSource(Request $request)
    {
        try {
            $sources = $this->SourceReponsitory->all();

            return response()->json([
                'data' => $sources,
                'error' => false
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
