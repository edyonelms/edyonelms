<?php

namespace App\Http\Controllers\v1;

use App\Models\Admin\Exam;
use Illuminate\Http\Request;

class ExamController extends ApiController
{
    /**
     * GET /api/v1/exams
     *
     * List published exams for the school.
     * Filters: academic_year, exam_type, search
     */
    public function index(Request $request)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $query = Exam::where('organization_id', $user->organization_id)
            ->where('is_published', true)
            ->when($request->filled('academic_year'), fn($q) => $q->where('academic_year', $request->academic_year))
            ->when($request->filled('exam_type'),     fn($q) => $q->where('exam_type', $request->exam_type))
            ->when($request->filled('search'),        fn($q) => $q->where('exam_name', 'like', '%' . $request->search . '%'))
            ->latest('start_date');

        $exams = $query->paginate((int) $request->get('per_page', 20));

        $items = $exams->getCollection()->map(fn($e) => $this->formatExam($e));

        return $this->paginated($items, $this->paginationMeta($exams), 'Exams fetched successfully.');
    }

    /**
     * GET /api/v1/exams/{id}
     *
     * Single exam detail.
     */
    public function show(int $id)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $exam = Exam::where('organization_id', $user->organization_id)
            ->where('is_published', true)
            ->find($id);

        if (!$exam) {
            return $this->error('Exam not found.', 404);
        }

        return $this->success($this->formatExam($exam, full: true), 'Exam fetched successfully.');
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function formatExam(Exam $e, bool $full = false): array
    {
        $now    = now();
        $status = match (true) {
            $e->start_date > $now  => 'upcoming',
            $e->end_date   < $now  => 'completed',
            default                => 'ongoing',
        };

        $data = [
            'id'            => $e->id,
            'exam_name'     => $e->exam_name,
            'exam_type'     => $e->exam_type,
            'academic_year' => $e->academic_year,
            'start_date'    => $e->start_date?->format('Y-m-d'),
            'end_date'      => $e->end_date?->format('Y-m-d'),
            'status'        => $status,
            'total_marks'   => $e->total_marks,
            'passing_marks' => $e->passing_marks,
        ];

        if ($full) {
            $data['description'] = $e->description;
            $data['status_label'] = $status;
            $data['days_remaining'] = $e->start_date > $now
                ? $now->diffInDays($e->start_date)
                : 0;
        }

        return $data;
    }
}
