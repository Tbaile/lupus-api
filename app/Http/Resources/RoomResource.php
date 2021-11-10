<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Room
 */
class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'private' => $this->private,
            'password' => $this->when($request->user()->can('update', $this->resource), $this->password),
            'owner' => new UserResource($this->owner()->firstOrFail())
        ];
    }
}
