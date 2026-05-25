<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Card - {{ $student->full_name ?? 'Student' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { margin: 14px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #222; }
        .sheet { border: 3px solid #2563eb; border-radius: 6px; padding: 14px 16px; }
        .topbar { width: 100%; font-size: 9px; color: #333; margin-bottom: 6px; }
        .topbar td { vertical-align: top; }
        .topbar .right { text-align: right; }
        .brand { text-align: center; margin-bottom: 8px; }
        .brand img { height: 70px; width: 70px; object-fit: contain; }
        .brand .school-name { font-size: 22px; font-weight: bold; color: #111; letter-spacing: 0.5px; margin-top: 2px; }
        .brand .address { font-size: 9px; color: #444; margin-top: 3px; }
        .brand .contact { font-size: 9px; color: #444; }
        .brand .doc-title { font-size: 12px; font-weight: bold; color: #111; margin-top: 8px; }
        .brand .session { font-size: 11px; font-weight: bold; color: #111; }

        table.info { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.info td { border: 1px solid #9ca3af; padding: 5px 8px; font-size: 10px; }
        table.info .label { font-weight: bold; width: 16%; background: #fff; }
        table.info .value { width: 34%; }

        table.marks { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.marks th, table.marks td { border: 1px solid #9ca3af; text-align: center; padding: 4px 3px; font-size: 9px; }
        table.marks th { font-weight: bold; }
        table.marks td.subj, table.marks th.subj { text-align: left; padding-left: 8px; }
        table.marks .grp { background: #f3f4f6; }
        table.marks .totrow td { font-weight: bold; background: #f9fafb; }
        table.marks .pctrow td { font-weight: bold; }

        table.co { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.co td { border: 1px solid #9ca3af; padding: 5px 8px; font-size: 9px; }
        table.co .hd { font-weight: bold; background: #f3f4f6; }
        table.co .gd { text-align: center; width: 60px; font-weight: bold; }

        table.foot { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.foot td { border: 1px solid #9ca3af; padding: 5px 8px; font-size: 9px; }
        table.foot .label { font-weight: bold; width: 14%; }

        .result { margin-top: 10px; }
        .result td { font-size: 10px; }
        .sign { margin-top: 34px; width: 100%; }
        .sign td { font-size: 10px; font-weight: bold; }
        .sign .r { text-align: right; }
    </style>
</head>
<body>
@php
    $passed = true;
    $grandObtained = 0;
    $grandMax = 0;
    // Pre-compute per-exam column totals
    $examTotals = [];
    foreach ($exams as $exam) { $examTotals[$exam->id] = ['obt' => 0, 'max' => 0]; }
@endphp
<div class="sheet">

    {{-- Top bar --}}
    <table class="topbar">
        <tr>
            <td>Affiliation No: {{ $organization->affiliation_no ?? '—' }}</td>
            <td class="right">{{ $organization->email ?? '' }}</td>
        </tr>
    </table>

    {{-- Brand --}}
    <div class="brand">
        @if(!empty($organization->logo) && file_exists(public_path('storage/' . $organization->logo)))
            <img src="{{ public_path('storage/' . $organization->logo) }}" alt="Logo">
        @endif
        <div class="school-name">{{ $organization->name ?? 'School Name' }}</div>
        <div class="address">{{ $organization->address ?? '' }}</div>
        <div class="contact">{{ $organization->mobile_number ?? '' }}</div>
        <div class="doc-title">Record of Academic Performance</div>
        <div class="session">Session: {{ $reportCard->academic_year ?? 'N/A' }}</div>
    </div>

    {{-- Student info --}}
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

    {{-- Scholastic marks --}}
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
                @php
                    $rowObt = 0; $rowMax = 0;
                @endphp
                <tr>
                    <td class="subj">{{ $subject->name }}</td>
                    @foreach ($exams as $exam)
                        @php
                            $copy = isset($examCopies[$exam->id])
                                ? $examCopies[$exam->id]->firstWhere('subject_id', $subject->id)
                                : null;
                            $cell = '-';
                            if ($copy) {
                                if ($copy->is_absent) {
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

    {{-- Attendance & Remark --}}
    <table class="foot">
        <tr>
            <td class="label">Attendance</td>
            <td>Present: {{ $attendance['present'] ?? 0 }} / {{ $attendance['total'] ?? 0 }} days</td>
            <td class="label">Result</td>
            <td><strong>{{ $passed && $grandMax > 0 ? 'PASSED' : ($grandMax > 0 ? 'FAILED' : 'N/A') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Remark</td>
            <td colspan="3">
                @php $op = $overallPct ?? 0; @endphp
                {{ $op >= 75 ? 'Excellent performance' : ($op >= 50 ? 'Good, keep improving' : ($op >= 33 ? 'Needs improvement' : 'Requires serious attention')) }}
            </td>
        </tr>
    </table>

    {{-- Result + signatures --}}
    <table class="result">
        <tr>
            <td>Issue Date: {{ $reportCard->issued_at ? $reportCard->issued_at->format('d/m/Y') : now()->format('d/m/Y') }}</td>
            <td style="text-align:right;">Overall: {{ $overallPct ?? 0 }}%</td>
        </tr>
    </table>

    <table class="sign">
        <tr>
            <td>Class Teacher</td>
            <td class="r">Principal</td>
        </tr>
    </table>

</div>
</body>
</html>
