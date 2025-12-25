<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Dividends\DividendService;

class DividendController extends Controller
{
    public function __construct(
        private readonly DividendService $dividendService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perYear = $this->dividendService->getDividendSumPerYear();

        return response()->json([
            'labels' => array_keys($perYear),
            'datasets' => [
                'data' => array_values($perYear),
            ],
        ]);
    }
}
