<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'assignee' => UserResource::make($this->whenLoaded('assignee')),

            'comments_count' => $this->when(isset($this->comments_count), $this->comments_count),
            'unread_comments_count' => $this->when(isset($this->unread_comments_count), $this->unread_comments_count),

            'comments' => TicketCommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
