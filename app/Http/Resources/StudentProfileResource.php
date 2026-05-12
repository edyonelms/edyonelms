<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'user_id' => $this->user_id ?? null,
            'standard' => $this->standard->name ?? null,
            'section' => $this->section->name ?? null,
            'full_name' => $this->full_name ?? null,
            'father_name' => $this->father_name ?? null,
            'mother_name' => $this->mother_name ?? null,
            'email' => $this->email ?? null,
            'dob' => $this->dob ? $this->dob->format('Y-m-d') : null,
            'gender' => $this->gender ?? null,
            'religion' => $this->religion ?? null,
            'local_address' => $this->local_address ?? null,
            'permanent_address' => $this->permanent_address ?? null,
            'city' => $this->city ?? null,
            'state' => $this->state ?? null,
            'pincode' => $this->pincode ?? null,
            'admission_no' => $this->admission_no ?? null,
            'date_of_admission' => $this->date_of_admission ? $this->date_of_admission->format('Y-m-d') : null,
            'roll_no' => $this->roll_no ?? null,
            'board' => $this->board ?? null,
            'aadhar_no' => $this->aadhar_no ?? null,
            'phone' => $this->phone ?? null,
            'image' => $this->image ?? null,
            'user_details' => [
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
                'mobile_number' => $this->user->mobile_number ?? null,
                'image' => $this->user->image ?? null,
            ]
        ];
    }
}
