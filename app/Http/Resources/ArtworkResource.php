<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\ArtworkMediaService;

class ArtworkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $thumbnailUrl = $this->is_private
            ? null
            : app(ArtworkMediaService::class)->thumbnailUrl($this->image_path);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->is_private ? null : '/storage/' . ltrim($this->image_path, '/'),
            'thumbnail_url' => $thumbnailUrl,
            'category' => $this->category,
            'is_private' => $this->is_private,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
