<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * API Controller for managing reviews.
 * 
 * Handles HTTP requests and delegates business logic to ReviewService.
 */
class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Display a listing of all reviews.
     *
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index()
    {
        try {
            $reviews = $this->reviewService->getAllReviews();

            return ReviewResource::collection($reviews);
        } catch (Exception $e) {
            Log::error('Failed to retrieve reviews', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve reviews',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Store a newly created review with reply suggestions.
     *
     * @param StoreReviewRequest $request
     * @return JsonResponse
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        try {
            $review = $this->reviewService->createReviewWithReplies($request->validated());

            return (new ReviewResource($review))
                ->response()
                ->setStatusCode(201);
        } catch (Exception $e) {
            Log::error('Failed to create review', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'message' => 'Failed to create review',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
