<?php

namespace App\Http\Controllers\v1;

use App\Models\Admin\ExamCopy;
use App\Models\Admin\ReportCard;
use App\Models\Student\StudentDetail;
use Illuminate\Http\Request;

class ReportCardController extends ApiController
{
    /**
     * GET /api/v1/report-card
     *
     * Returns the student's report cards (list, latest first).
     * Filters: academic_year
     * Only students (role=user) can access their own report card.
     */
    public function index(Request $request)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        if ($err = $this->requireRole('user')) return $err;

        $student = StudentDetail::where('user_id', $user->id)
            ->where('organization_id', $user->organization_id)
            ->first();

        if (!$student) {
            return $this->error('Student profile not found.', 404);
        }

        $cards = ReportCard::with(['standard:id,name', 'section:id,name', 'issuedBy:id,name'])
            ->where('student_detail_id', $student->id)
            ->where('organization_id', $user->organization_id)
            ->when($request->filled('academic_year'), fn($q) => $q->where('academic_year', $request->academic_year))
            ->latest('issued_at')
            ->paginate((int) $request->get('per_page', 10));

        $items = $cards->getCollection()->map(fn($c) => [
            'id'            => $c->id,
            'academic_year' => $c->academic_year,
            'class'         => ($c->standard?->name ?? '') . ($c->section ? ' - ' . $c->section->name : ''),
            'issued_at'     => $c->issued_at?->format('Y-m-d'),
            'issued_by'     => $c->issuedBy?->name,
            'status'        => $c->status,
        ]);

        return $this->paginated($items, $this->paginationMeta($cards), 'Report cards fetched successfully.');
    }

    /**
     * GET /api/v1/report-card/{id}
     *
     * Full report card — includes all exam results for the academic year.
     */
    public function show(int $id)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        if ($err = $this->requireRole('user')) return $err;

        $student = StudentDetail::where('user_id', $user->id)
            ->where('organization_id', $user->organization_id)
            ->first();

        if (!$student) {
            return $this->error('Student profile not found.', 404);
        }

        $card = ReportCard::with(['standard:id,name', 'section:id,name', 'issuedBy:id,name'])
            ->where('student_detail_id', $student->id)
            ->where('organization_id', $user->organization_id)
            ->find($id);

        if (!$card) {
            return $this->error('Report card not found.', 404);
        }

        // Fetch all exam copies for this student in the same academic year
        $examResults = ExamCopy::with(['subject:id,name,code', 'exam:id,exam_name,exam_type'])
            ->where('student_detail_id', $student->id)
            ->where('organization_id', $user->organization_id)
            ->whereHas('exam', fn($q) => $q->where('academic_year', $card->academic_year))
            ->get()
            ->map(fn($ec) => [
                'exam'          => $ec->exam?->exam_name,
                'exam_type'     => $ec->exam?->exam_type,
                'subject'       => $ec->subject?->name,
                'subject_code'  => $ec->subject?->code,
                'marks_obtained' => $ec->marks_obtained,
                'max_marks'     => $ec->max_marks,
                'percentage'    => $ec->percentage,
                'grade'         => $ec->grade,
                'is_absent'     => (bool) $ec->is_absent,
                'remarks'       => $ec->remarks,
            ]);

        return $this->success([
            'id'            => $card->id,
            'academic_year' => $card->academic_year,
            'class'         => ($card->standard?->name ?? '') . ($card->section ? ' - ' . $card->section->name : ''),
            'issued_at'     => $card->issued_at?->format('Y-m-d'),
            'issued_by'     => $card->issuedBy?->name,
            'status'        => $card->status,
            'results'       => $examResults,
        ], 'Report card fetched successfully.');
    }
}
