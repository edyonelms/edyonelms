<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'basic_info' => $this->formatBasicInfo(),
            'professional_info' => $this->formatProfessionalInfo(),
            'contact_info' => $this->formatContactInfo(),
            'assignments' => $this->getAllAssignmentsFormatted(),
            'organization_info' => $this->formatOrganizationInfo()
        ];
    }

    protected function formatBasicInfo()
    {
        return [
            'name' => $this->user->name ?? null,
            'email' => $this->user->email ?? null,
            'mobile_number' => $this->user->mobile_number ?? null,
            'image_url' => $this->user->image ??  null,
            'gender' => $this->user->gender ?? null,
            'dob' => optional($this->user->dob)->format('Y-m-d'),
            'role' => $this->user->role ?? null,
            'is_active' => $this->user->is_active ?? null
        ];
    }

    protected function formatProfessionalInfo()
    {
        return [
            'employee_id' => $this->employee_id ?? null,
            'date_of_joining' => $this->date_of_joining,
            'qualification' => $this->qualification ?? null,
            'total_subjects_assigned' => $this->assignedSubjects->count(),
            'total_classes_assigned' => $this->assignedClasses->count(),
            'total_sections_assigned' => $this->teacherSections->count()
        ];
    }

    protected function formatContactInfo()
    {
        return [
            'phone' => $this->phone ?? null,
            'emergency_contact' => $this->emergency_contact ?? null,
            'address' => $this->address ?? null,
            'city' => $this->city ?? null,
            'state' => $this->state ?? null,
            'pincode' => $this->pincode ?? null
        ];
    }

    protected function getAllAssignmentsFormatted()
    {
        $assignments = collect();

        // Source 1: TeacherSubject table (subjects with class/section)
        $subjects = $this->assignedSubjects->map(function ($subject) {
            return [
                'assignment_type' => 'subject',
                'subject_id' => $subject->subject_id ?? null,
                'subject_name' => optional($subject->subject)->name ?? null,
                'standard_id' => $subject->standard_id ?? null,
                'standard_name' => optional($subject->standard)->name ?? null,
                'section_id' => $subject->section_id ?? null,
                'section_name' => optional($subject->section)->name ?? null,
                'source' => 'teacher_subjects'
            ];
        });

        // Source 2: AssignTeacherStandard table (class/section assignments from Attendance)
        $classes = $this->assignedClasses->map(function ($class) {
            return [
                'assignment_type' => 'class',
                'standard_id' => $class->standard_id ?? null,
                'standard_name' => optional($class->standard)->name ?? null,
                'section_id' => $class->section_id ?? null,
                'section_name' => optional($class->section)->name ?? null,
                'source' => 'assign_teacher_standard'
            ];
        });

        // Source 3: TeacherSection table (only sections)
        $sections = $this->teacherSections->map(function ($teacherSection) {
            return [
                'assignment_type' => 'section',
                'section_id' => $teacherSection->section_id ?? null,
                'section_name' => optional($teacherSection->section)->name ?? null,
                'standard_id' => optional($teacherSection->section)->standard_id ?? null,
                'standard_name' => optional($teacherSection->section->standard)->name ?? null,
                'source' => 'teacher_sections'
            ];
        })->unique();

        return [
            'subjects' => $subjects,
            'classes' => $classes,
            'sections' => $sections,
            'summary' => [
                'total_assignments' => $subjects->count() + $classes->count() + $sections->count(),
                'has_data' => $subjects->isNotEmpty() || $classes->isNotEmpty() || $sections->isNotEmpty()
            ]
        ];
    }

    protected function formatOrganizationInfo()
    {
        return [
            'name' => $this->organization->name ?? null,
            'code' => $this->organization->code ?? null,
            'logo_url' => $this->organization->logo ?? null
        ];
    }
}
