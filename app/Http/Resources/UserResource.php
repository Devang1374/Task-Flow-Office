<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\TaskCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        JsonResource::withoutWrapping();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'tasks' => new TaskCollection($this->task()->Paginate(2)),
            'total_post' => $this->whenCounted('task'),
        ];
    }
}
