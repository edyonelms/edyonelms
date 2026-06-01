{{--
    Shared report-card body. Included by:
      - resources/views/admin/report-card-pdf.blade.php   (DomPDF, A4 portrait)
      - resources/views/admin/report-card-print.blade.php (browser print)

    Identical layout in both; the wrapping <style> only differs to suit each
    medium (DomPDF doesn't understand all browser CSS, browsers want a screen
    fallback). The markup below is the canonical design.

    Variables in scope:
      $organization, $student, $reportCard, $exams, $subjects, $examCopies,
      $attendance['term1'|'term2'|'overall' => ['present', 'total']],
      $coScholastic['term1'|'term2' => [['subject', 'grade'], ...]]
--}}

@php
    $grandObtained = 0;
    $grandMax = 0;
    $passed = true;
    $examTotals = [];
    foreach ($exams as $exam) { $examTotals[$exam->id] = ['obt' => 0, 'max' => 0]; }
@endphp

<div class="sheet">

    {{-- ─── Top bar ─────────────────────────────────────────── --}}
    <table class="topbar">
        <tr>
            <td>Affiliation No: {{ $organization->affiliation_no ?? '—' }}</td>
            <td class="right">{{ $organization->email ?? '' }}</td>
        </tr>
    </table>

    {{-- ─── Brand / school header ──────────────────────────── --}}
    @php
        $logoSrc = null;
        if (!empty($organization?->logo)) {
            if (\Illuminate\Support\Str::startsWith($organization->logo, ['http://', 'https://'])) {
                $logoSrc = $organization->logo;
            } elseif (file_exists(public_path('storage/' . $organization->logo))) {
                $logoSrc = public_path('storage/' . $organization->logo);
            }
        }
    @endphp
    <div class="brand">
        @if ($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logo">
        @endif
        <div class="school-name">{{ $organization->name ?? 'School Name' }}</div>
        <div class="address">{{ $organization->address ?? '' }}</div>
        <div class="contact">{{ $organization->mobile_number ?? '' }}</div>
        <div class="doc-title">Record of Academic Performance</div>
        <div class="session">Session: {{ $reportCard->academic_year ?? 'N/A' }}</div>
    </div>

    {{-- ─── Student info ───────────────────────────────────── --}}
    <table class="info">
        <tr>
            <td class="label">Student Name:</td>
            <td class="value" colspan="3">{{ $student->full_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Mother's Name:</td>
            <td class="value">{{ $student->mother_name ?? 'N/A' }}</td>
            <td class="label">Father's Name:</td>
            <td class="value">{{ $student->father_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Admission No:</td>
            <td class="value">{{ $student->admission_no ?? 'N/A' }}</td>
            <td class="label">Class/Section:</td>
            <td class="value">{{ $student->standard->name ?? '' }} / {{ $student->section->name ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Date of Birth:</td>
            <td class="value">{{ $student->dob ? $student->dob->format('d/m/Y') : 'N/A' }}</td>
            <td class="label">Regd. No:</td>
            <td class="value">{{ $student->registration_number ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- ─── Scholastic marks ───────────────────────────────── --}}
    <table class="marks">
        <thead>
            <tr>
                <th class="subj grp">Scholastic Area</th>
                @foreach ($exams as $exam)
                    <th class="grp">{{ $exam->exam_name }}</th>
                @endforeach
                <th class="grp">Total</th>
                <th class="grp">%</th>
                <th class="grp">Grade</th>
            </tr>
            <tr>
                <th class="subj">Subject Name</th>
                @foreach ($exams as $exam)
                    <th>{{ $exam->total_marks ?? '—' }}</th>
                @endforeach
                <th colspan="3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subjects as $subject)
                @php $rowObt = 0; $rowMax = 0; @endphp
                <tr>
                    <td class="subj">{{ $subject->name }}</td>
                    @foreach ($exams as $exam)
                        @php
                            $copy = isset($examCopies[$exam->id])
                                ? $examCopies[$exam->id]->firstWhere('subject_id', $subject->id)
                                : null;
                            $cell = '-';
                            if ($copy) {
                                if (!empty($copy->is_absent)) {
                                    $cell = 'AB';
                                } else {
                                    $cell = $copy->marks_obtained ?? '-';
                                    $rowObt += (float) ($copy->marks_obtained ?? 0);
                                    $rowMax += (float) ($copy->max_marks ?? 0);
                                    $examTotals[$exam->id]['obt'] += (float) ($copy->marks_obtained ?? 0);
                                    $examTotals[$exam->id]['max'] += (float) ($copy->max_marks ?? 0);
                                }
                            }
                        @endphp
                        <td>{{ $cell }}</td>
                    @endforeach
                    @php
                        $rowPct = $rowMax > 0 ? round(($rowObt / $rowMax) * 100, 2) : 0;
                        $rowGrade = $rowPct >= 90 ? 'A+' : ($rowPct >= 80 ? 'A' : ($rowPct >= 70 ? 'B+' : ($rowPct >= 60 ? 'B' : ($rowPct >= 50 ? 'C' : ($rowPct >= 33 ? 'D' : 'F')))));
                        if ($rowMax > 0 && $rowPct < 33) { $passed = false; }
                        $grandObtained += $rowObt;
                        $grandMax += $rowMax;
                    @endphp
                    <td>{{ $rowObt }}/{{ $rowMax }}</td>
                    <td>{{ $rowPct }}%</td>
                    <td>{{ $rowGrade }}</td>
                </tr>
            @empty
                <tr><td class="subj" colspan="{{ count($exams) + 4 }}">No subjects found for this section.</td></tr>
            @endforelse

            {{-- Total row --}}
            <tr class="totrow">
                <td class="subj">Total</td>
                @foreach ($exams as $exam)
                    <td>{{ $examTotals[$exam->id]['obt'] }}</td>
                @endforeach
                <td>{{ $grandObtained }}/{{ $grandMax }}</td>
                <td colspan="2"></td>
            </tr>
            {{-- Percentage row --}}
            <tr class="pctrow">
                <td class="subj">Percentage</td>
                @foreach ($exams as $exam)
                    @php
                        $eMax = $examTotals[$exam->id]['max'];
                        $ePct = $eMax > 0 ? round(($examTotals[$exam->id]['obt'] / $eMax) * 100, 2) : 0;
                    @endphp
                    <td>{{ $ePct }}%</td>
                @endforeach
                @php $overallPct = $grandMax > 0 ? round(($grandObtained / $grandMax) * 100, 2) : 0; @endphp
                <td>{{ $overallPct }}%</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    {{-- ─── Co-Scholastic Areas (Term 1 + Term 2 side by side) ─── --}}
    <table class="cosch">
        <tr>
            <td class="cell-left">
                <table class="cosch-inner">
                    <thead>
                        <tr>
                            <th>Co-Scholastic Areas: Term 1 (A-E)</th>
                            <th class="gd">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coScholastic['term1'] ?? [] as $row)
                            <tr>
                                <td>{{ $row['subject'] }}</td>
                                <td class="gd">{{ $row['grade'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td class="cell-right">
                <table class="cosch-inner">
                    <thead>
                        <tr>
                            <th>Co-Scholastic Areas: Term 2 (A-E)</th>
                            <th class="gd">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coScholastic['term2'] ?? [] as $row)
                            <tr>
                                <td>{{ $row['subject'] }}</td>
                                <td class="gd">{{ $row['grade'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- ─── Attendance + Remark ─────────────────────────────── --}}
    @php
        $a1 = $attendance['term1']   ?? ['present' => 0, 'total' => 0];
        $a2 = $attendance['term2']   ?? ['present' => 0, 'total' => 0];
        $ao = $attendance['overall'] ?? ['present' => 0, 'total' => 0];
    @endphp
    <table class="foot">
        <tr>
            <td class="label">Attendance</td>
            <td>Term 1: {{ $a1['present'] }}/{{ $a1['total'] }}</td>
            <td>Term 2: {{ $a2['present'] }}/{{ $a2['total'] }}</td>
            <td>Overall Attendance: {{ $ao['present'] }}/{{ $ao['total'] }}</td>
        </tr>
        <tr>
            <td class="label">Remark</td>
            @php $op = $overallPct ?? 0; @endphp
            <td colspan="3">
                {{ $op >= 75 ? 'Excellent performance' : ($op >= 50 ? 'Good, keep improving' : ($op >= 33 ? 'Need improvement' : 'Requires serious attention')) }}
            </td>
        </tr>
    </table>

    {{-- ─── Result row ──────────────────────────────────────── --}}
    <table class="result">
        <tr>
            <td>Issue Date: {{ $reportCard->issued_at ? $reportCard->issued_at->format('d/m/Y') : now()->format('d/m/Y') }}</td>
            <td class="r">RESULT: <strong>{{ $passed && $grandMax > 0 ? 'PASSED' : ($grandMax > 0 ? 'FAILED' : 'N/A') }}</strong></td>
        </tr>
    </table>

    {{-- ─── Signatures ──────────────────────────────────────── --}}
    <table class="sign">
        <tr>
            <td>Class Teacher</td>
            <td class="r">Principal</td>
        </tr>
    </table>

</div>
