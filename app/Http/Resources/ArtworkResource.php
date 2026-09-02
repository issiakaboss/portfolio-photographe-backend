<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArtworkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => Storage::disk($this->is_private ? 'private' : 'public')->url($this->image_path),
            'category' => $this->category,
            'is_private' => $this->is_private,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
