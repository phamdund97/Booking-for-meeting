<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'image'      => $this->image ? url($this->image) : '',
            'full_name'  => $this->full_name,
            'email'      => $this->email,
            'phone'      => $this->phone_number,
            'department' => $this->department->name,
            'role'       => $this->role->name,
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
        ];
    }
}
