<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'active' => (bool) $this->active,
            'foto_perfil' => $this->getFotoPerfilUrl(),
            'country' => $this->country,
            'birth_date' => $this->birth_date ? $this->birth_date->format('Y-m-d') : null,
            'address' => $this->address,
            'gender' => $this->gender,
            'preferred_language' => $this->preferred_language,
            'last_login' => $this->last_login ? $this->last_login->format('Y-m-d H:i:s') : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
    
    /**
     * Get the profile photo URL
     * 
     * @return string|null
     */
    protected function getFotoPerfilUrl(): ?string
    {
        if (!$this->foto_perfil) {
            return null;
        }
        
        // Check if it's already a URL
        if (filter_var($this->foto_perfil, FILTER_VALIDATE_URL)) {
            return $this->foto_perfil;
        }
        
        // Generate URL for stored images
        return Storage::disk('public')->url($this->foto_perfil);
    }
}