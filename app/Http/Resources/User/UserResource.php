<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
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
        $permissions = $this->permissions;
        if ($this->role && $this->role->relationLoaded('permissions')) {
            $permissions = $permissions->union($this->role->permissions)->unique('name');
            unset($this->role->permissions);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'active_project_id' => $this->active_project_id,
            'active_company_id' => $this->active_company_id,
            'type' => $this->type,
            'job_title' => $this->job_title,
            'api_token' => $this->api_token,
            'provider_id' => $this->provider_id,
            'provider_name' => $this->provider_name,
            'avatar' => $this->avatar,
            'onboarding_tutorial_status' => $this->onboarding_tutorial_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,

            'company' => $this->whenLoaded('company'),
            'projects' => $this->whenLoaded('projects'),
            'active_company' => $this->whenLoaded('activeCompany'),
            'role' => $this->whenLoaded('role'),
            'permissions' => $permissions,
            'company_memberships' => $this->whenLoaded('companyMemberships'),
        ];
    }
}
