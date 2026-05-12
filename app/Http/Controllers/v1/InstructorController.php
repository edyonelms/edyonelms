<?php

namespace App\Http\Controllers\v1;

use App\Models\Teacher\TeacherDetail;
use Illuminate\Http\Request;

class InstructorController extends ApiController
{
    /**
     * GET /api/v1/instructors
     */
    public function index(Request $request)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $query = TeacherDetail::with([
            'user:id,name,email,image,is_active',
            'assignedSubjects.subject:id,name,code,image',
            'assignedSubjects.standard:id,name',
            'assignedSubjects.section:id,name',
            'assignedClasses.standard:id,name',
            'assignedClasses.section:id,name',
        ])
            ->where('organization_id', $user->organization_id)
            ->whereHas('user', fn($q) => $q->where('is_active', true));

        if ($request->filled('subject_id')) {
            $query->whereHas(
                'assignedSubjects',
                fn($q) => $q->where('subject_id', $request->subject_id)
            );
        }

        if ($request->filled('standard_id')) {
            $query->whereHas(
                'assignedClasses',
                fn($q) => $q->where('standard_id', $request->standard_id)
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas(
                'user',
                fn($q) => $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
            );
        }

        $instructors = $query->latest()->paginate((int) $request->get('per_page', 20));

        $items = $instructors->getCollection()->map(fn($t) => $this->formatInstructor($t));

        return $this->paginated($items, $this->paginationMeta($instructors), 'Instructors fetched successfully.');
    }

    /**
     * GET /api/v1/instructors/{id}
     */
    public function show(int $id)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $teacher = TeacherDetail::with([
            'user:id,name,email,image,is_active',
            'assignedSubjects.subject:id,name,code,image',
            'assignedSubjects.standard:id,name',
            'assignedSubjects.section:id,name',
            'assignedClasses.standard:id,name',
            'assignedClasses.section:id,name',
        ])
            ->where('organization_id', $user->organization_id)
            ->find($id);

        if (!$teacher) {
            return $this->error('Instructor not found.', 404);
        }

        return $this->success(
            $this->formatInstructor($teacher, full: true),
            'Instructor fetched successfully.'
        );
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function formatInstructor(TeacherDetail $t, bool $full = false): array
    {
        $data = [
            'id'          => $t->id,
            'name'        => $t->user?->name,
            'email'       => $t->user?->email,
            'avatar'      => $t->user?->image,
            'employee_id' => $t->employee_id,

            // Unique subjects across all assignments
            'subjects' => $t->assignedSubjects
                ->filter(fn($s) => $s->subject !== null)
                ->map(fn($s) => [
                    'id'    => $s->subject->id,
                    'name'  => $s->subject->name,
                    'code'  => $s->subject->code,
                    'image' => $s->subject->image,
                ])
                ->unique('id')
                ->values(),

            // Unique classes (standard + section) across all assignments
            'classes' => $t->assignedSubjects
                ->filter(fn($s) => $s->standard !== null)
                ->map(fn($s) => [
                    'standard_id'   => $s->standard?->id,
                    'standard_name' => $s->standard?->name,
                    'section_id'    => $s->section?->id,
                    'section_name'  => $s->section?->name,
                ])
                ->unique(fn($c) => $c['standard_id'] . '-' . $c['section_id'])
                ->values(),

            // From assignedClasses (AssignTeacherStandard)
            'assigned_classes' => $t->assignedClasses
                ->filter(fn($c) => $c->standard !== null)
                ->map(fn($c) => [
                    'standard_id'   => $c->standard?->id,
                    'standard_name' => $c->standard?->name,
                    'section_id'    => $c->section?->id,
                    'section_name'  => $c->section?->name,
                ])
                ->unique(fn($c) => $c['standard_id'] . '-' . $c['section_id'])
                ->values(),
        ];

        if ($full) {
            $data['qualification']   = $t->qualification;
            $data['date_of_joining'] = $t->date_of_joining
                ? \Carbon\Carbon::parse($t->date_of_joining)->format('Y-m-d')
                : null;
            $data['phone']           = $t->phone;
            $data['city']            = $t->city;
            $data['state']           = $t->state;
            $data['address']         = $t->address;
        }

        return $data;
    }
}
