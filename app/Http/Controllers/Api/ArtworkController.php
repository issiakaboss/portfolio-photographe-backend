<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArtworkResource;
use App\Models\Artwork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArtworkController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $artworks = Artwork::where('is_private', false)
            ->latest()
            ->get();
        return ArtworkResource::collection($artworks);
    }
}
