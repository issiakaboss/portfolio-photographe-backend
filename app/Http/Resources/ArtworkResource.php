<?php

namespace App\Http\Resources;

use App\Services\ArtworkMediaService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtworkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $disk = $this->is_private ? 'private' : 'public';
        $thumbnailUrl = $this->is_private
            ? null
            : app(ArtworkMediaService::class)->thumbnailUrl($this->image_path, $disk);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->is_private ? null : '/storage/' . ltrim($this->image_path, '/'),
            'thumbnail_url' => $thumbnailUrl,
            'category' => $this->category,
            'price' => $this->price,
            'is_for_sale' => $this->is_for_sale,
            'status' => $this->status,
            'dimensions' => $this->dimensions,
            'materials' => $this->materials,
            'is_private' => $this->is_private,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
