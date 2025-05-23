<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ProjectLogs;
use App\Models\User;

class Project extends JsonResource
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
            'deadline' => $this->deadline,
            'client_id' => $this->client_id,
            'freelancer_id' => $this->freelancer_id,
            'client_user' => new User($this->whenLoaded('clientUser')),
            'freelancer_user' => new User($this->whenLoaded('freelancerUser')),
            'project_logs' => ProjectLogs::collection($this->whenLoaded('projectLogs')),
        ];
    }
}
