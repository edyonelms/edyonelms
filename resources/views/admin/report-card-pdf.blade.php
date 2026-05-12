<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Card - {{ $student->full_name ?? 'Student' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .report-card { padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 12px; margin-bottom: 16px; }
        .header .school-name { font-size: 22px; font-weight: bold; color: #1e1b4b; }
        .header .address { font-size: 10px; color: #888; margin-top: 2px; }
        .header h1 { font-size: 16px; color: #4f46e5; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 6px; }
        .header .academic-year { font-size: 10px; color: #666; margin-top: 2px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #4f46e5; letter-spacing: 0.04em; margin-bottom: 6px; margin-top: 12px; }
        .info-table { width: 100%; margin-bottom: 12px; }
        .info-table td { padding: 3px 8px; font-size: 11px; }
        .info-table .label { color: #888; width: 100px; }
        .info-table .value { font-weight: 600; color: #111; }
        .marks-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
        .marks-table th { background: #eef2ff; color: #4338ca; font-size: 9px; font-weight: 600; text-transform: uppercase; padding: 6px 6px; border: 1px solid #c7d2fe; text-align: center; }
        .marks-table th:first-child { text-align: left; }
        .marks-table td { padding: 5px 6px; border: 1px solid #e5e7eb; font-size: 10px; text-align: center; }
        .marks-table td:first-child { text-align: left; font-weight: 500; }
        .marks-table tr:nth-child(even) { background: #fafafa; }
        .marks-table .total-row { background: #eef2ff; font-weight: 700; }
        .grade-badge { padding: 1px 6px; border-radius: 8px; font-size: 9px; font-weight: 600; }
        .grade-a { background: #d1fae5; color: #065f46; }
        .grade-b { background: #dbeafe; color: #1e40af; }
        .grade-c { background: #fef3c7; color: #92400e; }
        .grade-d { background: #fecaca; color: #991b1b; }
        .footer { border-top: 1px dashed #ccc; padding-top: 12px; margin-top: 16px; }
        .footer-table { width: 100%; }
        .footer-table td { font-size: 10px; color: #777; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #333; width: 120px; margin-top: 40px; padding-top: 3px; text-align: center; font-size: 9px; color: #555; }
    </style>
</head>
<body>

<div class="report-card">
    <div class="header">
        <div class="school-name">{{ $organization->name ?? 'School Name' }}</div>
        <div class="address">{{ $organization->address ?? '' }}</div>
        <h1>Academic Report Card</h1>
        <div class="academic-year">Academic Year: {{ $reportCard->academic_year ?? 'N/A' }}</div>
    </div>

    <div class="section-title">Student Information</div>
    <table class="info-table">
        <tr>
            <td class="label">Student Name:</td>
            <td class="value">{{ $student->full_name ?? 'N/A' }}</td>
            <td class="label">Admission No:</td>
            <td class="value">{{ $student->admission_no ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Class & Section:</td>
            <td class="value">{{ $student->standard->name ?? '' }} - {{ $student->section->name ?? '' }}</td>
            <td class="label">Roll No:</td>
            <td class="value">{{ $student->roll_no ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Father's Name:</td>
            <td class="value">{{ $student->father_name ?? 'N/A' }}</td>
            <td class="label">Date of Birth:</td>
            <td class="value">{{ $student->dob ? $student->dob->format('d M Y') : 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">Examination Results</div>
    <table class="marks-table">
        <thead>
            <tr>
                <th>Subject</th>
                @foreach ($exams as $exam)
                    <th>{{ $exam->exam_name }}<br>({{ $exam->total_marks ?? 'N/A' }})</th>
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
                                    {{ $copy->marks_obtained ?? '-' }}/{{ $copy->max_marks ?? '-' }}
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
                    <td><strong>{{ $subjectTotal }}/{{ $subjectMax }}</strong></td>
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
                <td><strong>{{ $grandTotal }}/{{ $grandMax }}</strong></td>
                <td><strong>{{ $overallPct }}%</strong></td>
                <td><span class="grade-badge {{ $overallGradeClass }}">{{ $overallGrade }}</span></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>Issued on: {{ $reportCard->issued_at ? $reportCard->issued_at->format('d M Y') : 'N/A' }}</td>
                <td style="text-align:center;">
                    <div class="signature-line">Class Teacher</div>
                </td>
                <td style="text-align:right;">
                    <div class="signature-line">Principal</div>
                </td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
