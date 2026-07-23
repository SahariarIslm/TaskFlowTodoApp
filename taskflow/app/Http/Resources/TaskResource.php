<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'assignee' => $this->assignee,
            'project' => $this->project,
            'tags' => $this->tags ?? [],
            'comments_count' => $this->comments_count ?? $this->comments()->count(),
            'comments' => TaskCommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
