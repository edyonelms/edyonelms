<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card - {{ $student->full_name ?? 'Student' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #333; background: #f3f4f6; }
        .report-card { max-width: 800px; margin: 20px auto; background: #fff; border: 2px solid #4f46e5; border-radius: 8px; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 16px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; color: #4f46e5; text-transform: uppercase; letter-spacing: 0.05em; }
        .header .school-name { font-size: 24px; font-weight: bold; color: #1e1b4b; margin-bottom: 4px; }
        .header .tagline { font-size: 11px; color: #888; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #4f46e5; letter-spacing: 0.05em; margin-bottom: 8px; margin-top: 16px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 24px; margin-bottom: 16px; }
        .info-item { display: flex; gap: 8px; }
        .info-item .label { font-size: 11px; color: #888; min-width: 100px; }
        .info-item .value { font-size: 12px; font-weight: 600; color: #111; }
        .marks-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 16px; }
        .marks-table th { background: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; padding: 8px 10px; border: 1px solid #c7d2fe; text-align: center; }
        .marks-table th:first-child { text-align: left; }
        .marks-table td { padding: 7px 10px; border: 1px solid #e5e7eb; font-size: 12px; text-align: center; }
        .marks-table td:first-child { text-align: left; font-weight: 500; }
        .marks-table tr:nth-child(even) { background: #fafafa; }
        .marks-table .total-row { background: #eef2ff !important; font-weight: 700; }
        .grade-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .grade-a { background: #d1fae5; color: #065f46; }
        .grade-b { background: #dbeafe; color: #1e40af; }
        .grade-c { background: #fef3c7; color: #92400e; }
        .grade-d { background: #fecaca; color: #991b1b; }
        .footer { border-top: 1px dashed #ccc; padding-top: 16px; margin-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; font-size: 11px; color: #777; }
        .signature-block { text-align: center; }
        .signature-block .line { border-top: 1px solid #333; width: 150px; margin: 30px auto 4px; }
        .signature-block p { font-size: 11px; color: #555; }
        .print-btn { display: block; max-width: 200px; margin: 20px auto; padding: 10px 24px; background: #4f46e5; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; text-align: center; }
        .print-btn:hover { background: #4338ca; }
        @media print {
            body { background: #fff; margin: 0; }
            .report-card { border: 1px solid #999; max-width: 100%; margin: 0; box-shadow: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<button class="print-btn no-print" onclick="window.print()">Print Report Card</button>

<div class="report-card">
    <div class="header">
        <div class="school-name">{{ $organization->name ?? 'School Name' }}</div>
        <div class="tagline">{{ $organization->address ?? '' }}</div>
        <h1>Academic Report Card</h1>
        <div class="tagline">Academic Year: {{ $reportCard->academic_year ?? 'N/A' }}</div>
    </div>

    <div class="section-title">Student Information</div>
    <div class="info-grid">
        <div class="info-item">
            <span class="label">Student Name:</span>
            <span class="value">{{ $student->full_name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Admission No:</span>
            <span class="value">{{ $student->admission_no ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Class & Section:</span>
            <span class="value">{{ $student->standard->name ?? '' }} - {{ $student->section->name ?? '' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Roll No:</span>
            <span class="value">{{ $student->roll_no ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Father's Name:</span>
            <span class="value">{{ $student->father_name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Date of Birth:</span>
            <span class="value">{{ $student->dob ? $student->dob->format('d M Y') : 'N/A' }}</span>
        </div>
    </div>

    <div class="section-title">Examination Results</div>
    <table class="marks-table">
        <thead>
            <tr>
                <th>Subject</th>
                @foreach ($exams as $exam)
                    <th>{{ $exam->exam_name }}<br><small>({{ $exam->total_marks ?? 'N/A' }})</small></th>
                @endforeach
                <th>Total</th>
                <th>%</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $grandMax = 0;
            @endphp
            @foreach ($subjects as $subject)
                @php
                    $subjectTotal = 0;
                    $subjectMax = 0;
                @endphp
                <tr>
                    <td>{{ $subject->name }}</td>
                    @foreach ($exams as $exam)
                        @php
                            $copy = isset($examCopies[$exam->id])
                                ? $examCopies[$exam->id]->firstWhere('subject_id', $subject->id)
                                : null;
                            if ($copy) {
                                $subjectTotal += $copy->marks_obtained ?? 0;
                                $subjectMax += $copy->max_marks ?? 0;
                            }
                        @endphp
                        <td>
                            @if ($copy)
                                @if ($copy->is_absent)
                                    <span style="color:#dc2626;">AB</span>
                                @else
                                    {{ $copy->marks_obtained ?? '-' }} / {{ $copy->max_marks ?? '-' }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    @php
                        $pct = $subjectMax > 0 ? round(($subjectTotal / $subjectMax) * 100, 1) : 0;
                        $grade = $pct >= 90 ? 'A+' : ($pct >= 80 ? 'A' : ($pct >= 70 ? 'B+' : ($pct >= 60 ? 'B' : ($pct >= 50 ? 'C' : ($pct >= 40 ? 'D' : 'F')))));
                        $gradeClass = in_array($grade, ['A+', 'A']) ? 'grade-a' : (in_array($grade, ['B+', 'B']) ? 'grade-b' : ($grade === 'C' ? 'grade-c' : 'grade-d'));
                        $grandTotal += $subjectTotal;
                        $grandMax += $subjectMax;
                    @endphp
                    <td><strong>{{ $subjectTotal }} / {{ $subjectMax }}</strong></td>
                    <td>{{ $pct }}%</td>
                    <td><span class="grade-badge {{ $gradeClass }}">{{ $grade }}</span></td>
                </tr>
            @endforeach

            @php
                $overallPct = $grandMax > 0 ? round(($grandTotal / $grandMax) * 100, 1) : 0;
                $overallGrade = $overallPct >= 90 ? 'A+' : ($overallPct >= 80 ? 'A' : ($overallPct >= 70 ? 'B+' : ($overallPct >= 60 ? 'B' : ($overallPct >= 50 ? 'C' : ($overallPct >= 40 ? 'D' : 'F')))));
                $overallGradeClass = in_array($overallGrade, ['A+', 'A']) ? 'grade-a' : (in_array($overallGrade, ['B+', 'B']) ? 'grade-b' : ($overallGrade === 'C' ? 'grade-c' : 'grade-d'));
            @endphp
            <tr class="total-row">
                <td>Grand Total</td>
                @foreach ($exams as $exam)
                    <td>-</td>
                @endforeach
                <td><strong>{{ $grandTotal }} / {{ $grandMax }}</strong></td>
                <td><strong>{{ $overallPct }}%</strong></td>
                <td><span class="grade-badge {{ $overallGradeClass }}">{{ $overallGrade }}</span></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div>
            <p>Issued on: {{ $reportCard->issued_at ? $reportCard->issued_at->format('d M Y') : 'N/A' }}</p>
        </div>
        <div class="signature-block">
            <div class="line"></div>
            <p>Principal / Head of School</p>
        </div>
        <div class="signature-block">
            <div class="line"></div>
            <p>Class Teacher</p>
        </div>
    </div>
</div>

</body>
</html>
