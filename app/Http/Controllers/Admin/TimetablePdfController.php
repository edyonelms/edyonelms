<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\SchoolInfo;
use App\Models\Admin\TeacherTimeTable;
use App\Models\Organization;
use App\Models\Student\Section;
use App\Models\Student\Standard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TimetablePdfController extends Controller
{
    /**
     * Download a class-section timetable as PDF.
     * Route: GET /admin/{organization}/timetable/{standard}/{section}/pdf
     */
    public function download(int $organization, int $standard, int $section): Response
    {
        $orgId = Auth::user()?->organization_id;
        abort_if(!$orgId || $orgId !== $organization, 403);

        $org           = Organization::find($orgId);
        $schoolInfo    = SchoolInfo::where('organization_id', $orgId)->first();
        $standardModel = Standard::where('organization_id', $orgId)->findOrFail($standard);
        $sectionModel  = Section::where('organization_id', $orgId)->findOrFail($section);

        $entries = TeacherTimeTable::with(['teacher.user:id,name', 'subject:id,name,code'])
            ->where('organization_id', $orgId)
            ->where('standard_id', $standard)
            ->where('section_id',  $section)
            ->orderBy('start_time')
            ->orderBy('day_of_week')
            ->get();

        $daysFull  = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
        $daysShort = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];

        $rows = $entries
            ->groupBy(fn($e) => $e->subject_id . '|' . $e->start_time . '|' . $e->end_time)
            ->map(function ($items) use ($daysShort) {
                $first    = $items->first();
                $teachers = $items->groupBy('teacher_detail_id')->map(function ($g) use ($daysShort) {
                    $first = $g->first();
                    return [
                        'name' => $first->teacher?->user?->name ?? '—',
                        'days' => $g->pluck('day_of_week')
                            ->map(fn($d) => (int) $d)
                            ->sort()
                            ->map(fn($d) => $daysShort[$d] ?? $d)
                            ->values()
                            ->all(),
                    ];
                })->sortByDesc(fn($t) => count($t['days']))->values()->all();

                return [
                    'subject'    => $first->subject?->name ?? '—',
                    'start_time' => $first->start_time,
                    'end_time'   => $first->end_time,
                    'teachers'   => $teachers,
                    'days'       => $items->pluck('day_of_week')
                        ->map(fn($d) => (int) $d)
                        ->unique()
                        ->sort()
                        ->map(fn($d) => $daysShort[$d] ?? $d)
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy('start_time')
            ->values()
            ->all();

        $pdf = Pdf::loadView('pdf.admin.timetable', [
            'organization' => $org,
            'schoolInfo'   => $schoolInfo,
            'standard'     => $standardModel,
            'section'      => $sectionModel,
            'rows'         => $rows,
            'daysShort'    => $daysShort,
            'daysFull'     => $daysFull,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('dpi', 150)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'timetable_' . str_replace(' ', '_', $standardModel->name) . '_' . str_replace(' ', '_', $sectionModel->name) . '.pdf';
        return $pdf->download($filename);
    }
}
