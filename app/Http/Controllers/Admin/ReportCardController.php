<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Exam;
use App\Models\Admin\ExamCopy;
use App\Models\Admin\ReportCard;
use App\Models\Student\SectionSubject;
use App\Models\Student\StudentAttendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportCardController extends Controller
{
    /**
     * Download report card as PDF.
     */
    public function download(Request $request, $organization, $id)
    {
        $reportCard = $this->getReportCard($id);
        $data = $this->buildReportCardData($reportCard);

        $pdf = Pdf::loadView('admin.report-card-pdf', $data)
            ->setPaper('a4', 'portrait');

        $studentName = str_replace(' ', '_', $reportCard->studentDetail->full_name ?? 'student');

        return $pdf->download("report_card_{$studentName}.pdf");
    }

    /**
     * Print report card (view in browser).
     */
    public function print(Request $request, $organization, $id)
    {
        $reportCard = $this->getReportCard($id);
        $data = $this->buildReportCardData($reportCard);

        return view('admin.report-card-print', $data);
    }

    /**
     * Get report card with authorization check.
     */
    private function getReportCard($id)
    {
        return ReportCard::with([
            'studentDetail',
            'studentDetail.standard',
            'studentDetail.section',
            'standard',
            'section',
            'organization',
            'issuedBy',
        ])
            ->where('organization_id', Auth::user()->organization_id)
            ->where('status', 'issued')
            ->findOrFail($id);
    }

    /**
     * Build data array for report card views.
     */
    private function buildReportCardData(ReportCard $reportCard)
    {
        $orgId = $reportCard->organization_id;
        $studentId = $reportCard->student_detail_id;
        $standardId = $reportCard->standard_id;
        $sectionId = $reportCard->section_id;

        // Get all published exams
        $exams = Exam::where('organization_id', $orgId)
            ->where('is_published', true)
            ->orderBy('start_date')
            ->get();

        // Get section subjects
        $subjectIds = SectionSubject::where('section_id', $sectionId)
            ->where('standard_id', $standardId)
            ->where('organization_id', $orgId)
            ->pluck('subject_id');

        $subjects = \App\Models\Student\Subject::whereIn('id', $subjectIds)->orderBy('name')->get();

        // Get all exam copies for this student
        $examCopies = ExamCopy::where('organization_id', $orgId)
            ->where('student_detail_id', $studentId)
            ->whereIn('exam_id', $exams->pluck('id'))
            ->whereIn('subject_id', $subjectIds)
            ->get()
            ->groupBy('exam_id');

        // Attendance summary (status 1 = present) — split into Term 1 / Term 2
        // by the midpoint date of all attendance records for this student.
        $attendanceRows = StudentAttendance::where('organization_id', $orgId)
            ->where('student_detail_id', $studentId)
            ->orderBy('date')
            ->get(['date', 'status']);

        $overallTotal   = $attendanceRows->count();
        $overallPresent = $attendanceRows->where('status', 1)->count();

        $term1Total = $term1Present = $term2Total = $term2Present = 0;
        if ($overallTotal > 0) {
            $midIndex = intdiv($overallTotal, 2);
            $term1 = $attendanceRows->slice(0, $midIndex);
            $term2 = $attendanceRows->slice($midIndex);
            $term1Total   = $term1->count();
            $term1Present = $term1->where('status', 1)->count();
            $term2Total   = $term2->count();
            $term2Present = $term2->where('status', 1)->count();
        }

        // Co-Scholastic Areas — fixed default subjects, default grade "A" for everyone.
        // Per spec: same set shown for Term 1 and Term 2, both default to A.
        $coScholasticSubjects = ['General Studies', 'Health & Physical Education', 'Work Behaviour'];
        $coScholastic = [
            'term1' => array_map(fn($s) => ['subject' => $s, 'grade' => 'A'], $coScholasticSubjects),
            'term2' => array_map(fn($s) => ['subject' => $s, 'grade' => 'A'], $coScholasticSubjects),
        ];

        return [
            'reportCard'   => $reportCard,
            'student'      => $reportCard->studentDetail,
            'organization' => $reportCard->organization ?? Auth::user()->organization,
            'exams'        => $exams,
            'subjects'     => $subjects,
            'examCopies'   => $examCopies,
            'attendance'   => [
                'term1'   => ['present' => $term1Present, 'total' => $term1Total],
                'term2'   => ['present' => $term2Present, 'total' => $term2Total],
                'overall' => ['present' => $overallPresent, 'total' => $overallTotal],
                // legacy keys (kept for backwards compatibility with old templates)
                'present' => $overallPresent,
                'total'   => $overallTotal,
            ],
            'coScholastic' => $coScholastic,
        ];
    }
}
