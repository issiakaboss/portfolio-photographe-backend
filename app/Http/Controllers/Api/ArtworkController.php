<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArtworkResource;
use App\Models\Artwork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Artwork::where('is_private', false)
            ->latest();

        $limit = $request->integer('limit');
        $artworks = $limit > 0
            ? $query->limit(min($limit, 24))->get()
            : $query->get();
        $response = ArtworkResource::collection($artworks)->response();
        $response->header('Cache-Control', 'public, max-age=60, stale-while-revalidate=300');

        return $response;
    }

    public function show(int $id): ArtworkResource
    {
        return new ArtworkResource(
            Artwork::query()
                ->where('is_private', false)
                ->findOrFail($id),
        );
    }
}
