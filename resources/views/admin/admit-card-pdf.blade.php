<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admit Card – {{ $admitCard->student_name ?? 'Student' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; background: #fff; }
        .page { padding: 24px 28px; max-width: 780px; margin: 0 auto; }

        /* Header */
        .header { text-align: center; margin-bottom: 14px; }
        .header img.logo { height: 90px; width: 90px; object-fit: contain; margin-bottom: 6px; }
        .header .school-name { font-size: 22px; font-weight: 900; color: #111; text-transform: uppercase; letter-spacing: 0.02em; }
        .header .address { font-size: 10px; color: #444; margin-top: 3px; line-height: 1.5; }

        /* Admit Card title */
        .ac-title { text-align: center; margin: 14px 0 10px; }
        .ac-title p:first-child { font-size: 13px; font-weight: 700; }
        .ac-title p:last-child  { font-size: 13px; font-weight: 700; }

        /* Outer border box */
        .card-box { border: 1.5px solid #333; }

        /* Student info table */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { border: 1px solid #555; padding: 5px 8px; font-size: 11px; vertical-align: middle; }
        .info-table .label { font-weight: 700; background: #f5f5f5; white-space: nowrap; width: 130px; }
        .info-table .value { color: #111; }

        /* Spacer row */
        .spacer-row td { height: 10px; border-left: 1px solid #555; border-right: 1px solid #555; border-top: none; border-bottom: none; }

        /* Subject table */
        .subject-table { width: 100%; border-collapse: collapse; margin-top: 2px; }
        .subject-table th { border: 1px solid #555; padding: 5px 6px; font-size: 11px; font-weight: 700; background: #f0f0f0; text-align: center; }
        .subject-table td { border: 1px solid #555; padding: 5px 6px; font-size: 11px; text-align: center; }
        .subject-table td:nth-child(2) { text-align: left; }

        /* Status badges */
        .eligible     { color: #166534; font-weight: 600; }
        .not-eligible { color: #991b1b; font-weight: 600; }

        /* Issue date */
        .issue-date { padding: 6px 8px; font-size: 11px; border-top: 1px solid #555; }

        /* Instructions */
        .instructions-section { margin-top: 18px; }
        .instructions-section h4 { font-size: 12px; font-weight: 700; text-align: center; margin-bottom: 8px; text-decoration: underline; }
        .instructions-section ol { padding-left: 18px; }
        .instructions-section ol li { font-size: 11px; margin-bottom: 4px; line-height: 1.5; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page { padding: 12px 16px; }
        }
    </style>
</head>
<body>

{{-- Print / Close controls --}}
<div class="no-print" style="position:fixed;top:0;left:0;right:0;z-index:100;background:#1e293b;padding:8px 16px;display:flex;align-items:center;gap:10px;">
    <button onclick="window.print()" style="background:#4f46e5;color:#fff;border:none;padding:6px 18px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">Print</button>
    <button onclick="window.close()" style="background:#64748b;color:#fff;border:none;padding:6px 18px;border-radius:6px;cursor:pointer;font-size:13px;">Close</button>
    <span style="color:#94a3b8;font-size:12px;margin-left:8px;">Admit Card – {{ $admitCard->student_name }}</span>
</div>

<div style="height:44px;" class="no-print"></div>

<div class="page">

    {{-- ── HEADER ── --}}
    <div class="header">
        @if($organization->logo)
            <img class="logo" src="{{ public_path('storage/' . $organization->logo) }}" alt="Logo">
        @endif
        <div class="school-name">{{ $organization->name }}</div>
        <div class="address">
            {{ $organization->address }}
            @if($organization->mobile_number)
                <br>{{ $organization->mobile_number }}
                @if($organization->email) / {{ $organization->email }} @endif
            @endif
        </div>
    </div>

    {{-- ── ADMIT CARD TITLE ── --}}
    <div class="ac-title">
        <p>Admit Card</p>
        <p>{{ $admitCard->academic_year }}/ Exam: {{ $admitCard->exam_name }}</p>
    </div>

    {{-- ── STUDENT INFO + SUBJECTS BOX ── --}}
    <div class="card-box">

        {{-- Student Info --}}
        <table class="info-table">
            <tr>
                <td class="label">Student Name:</td>
                <td class="value" colspan="3">{{ $admitCard->student_name }}</td>
            </tr>
            <tr>
                <td class="label">Mother's Name:</td>
                <td class="value">{{ $admitCard->mother_name ?: '—' }}</td>
                <td class="label">Father's Name:</td>
                <td class="value">{{ $admitCard->father_name ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Admission No:</td>
                <td class="value">{{ $admitCard->studentDetail?->admission_no ?? '—' }}</td>
                <td class="label">Roll No:</td>
                <td class="value">{{ $admitCard->roll_number }}</td>
            </tr>
            <tr>
                <td class="label">School Name:</td>
                <td class="value" colspan="3">{{ $organization->name }}</td>
            </tr>
            <tr>
                <td class="label">Program Name:</td>
                <td class="value">{{ $admitCard->exam_name }}</td>
                <td class="label">Class/Section</td>
                <td class="value">
                    {{ $admitCard->studentDetail?->standard?->name ?? '—' }}
                    @if($admitCard->studentDetail?->section?->name)
                        / {{ $admitCard->studentDetail->section->name }}
                    @endif
                </td>
            </tr>
        </table>

        {{-- Spacer --}}
        <table style="width:100%;border-collapse:collapse;">
            <tr><td style="height:8px;border-left:1px solid #555;border-right:1px solid #555;"></td></tr>
        </table>

        {{-- Subject Schedule --}}
        @if(!empty($admitCard->subjects))
        <table class="subject-table">
            <thead>
                <tr>
                    <th style="width:62px;">Subject Code</th>
                    <th>Course Name</th>
                    <th style="width:52px;">Total<br>Marks</th>
                    <th style="width:52px;">Pass<br>Marks</th>
                    <th style="width:82px;">Date</th>
                    <th style="width:70px;">Day</th>
                    <th style="width:92px;">Seating Plan</th>
                    <th style="width:74px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admitCard->subjects as $i => $subject)
                @php
                    $subjectModel = \App\Models\Student\Subject::find($subject['subject_id'] ?? null);
                    $code = $subjectModel?->code ?? str_pad($i + 1, 3, '0', STR_PAD_LEFT);
                    $examDate = isset($subject['exam_date']) ? \Carbon\Carbon::parse($subject['exam_date']) : null;
                    $seatingPlan = $admitCard->seating_label ?? '';
                    if (!$seatingPlan) {
                        if ($admitCard->room_number && $admitCard->seat_number) {
                            $seatingPlan = 'R(' . $admitCard->room_number . ')/ S(' . $admitCard->seat_number . ')';
                        } elseif ($admitCard->seat_number) {
                            $seatingPlan = 'S(' . $admitCard->seat_number . ')';
                        } elseif ($admitCard->room_number) {
                            $seatingPlan = 'R(' . $admitCard->room_number . ')';
                        }
                    }
                    $subjectStatus = $subject['status'] ?? 'eligible';
                    $totalMarks = $subject['total_marks'] ?? $subject['max_marks'] ?? $admitCard->exam?->total_marks;
                    $passMarks  = $subject['passing_marks'] ?? $subject['pass_marks'] ?? $admitCard->exam?->passing_marks;
                @endphp
                <tr>
                    <td>{{ $code }}</td>
                    <td>{{ $subject['subject_name'] ?? '—' }}</td>
                    <td>{{ $totalMarks !== null ? rtrim(rtrim(number_format($totalMarks, 2), '0'), '.') : '—' }}</td>
                    <td>{{ $passMarks !== null ? rtrim(rtrim(number_format($passMarks, 2), '0'), '.') : '—' }}</td>
                    <td>{{ $examDate ? $examDate->format('d/m/Y') : '—' }}</td>
                    <td>{{ $examDate ? $examDate->format('l') : '—' }}</td>
                    <td>{{ $seatingPlan ?: '—' }}</td>
                    <td>
                        @if($subjectStatus === 'not_eligible')
                            <span class="not-eligible">Not Eligible</span>
                        @else
                            <span class="eligible">Eligible</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Issue Date --}}
        <div class="issue-date">
            Issue Date: {{ $admitCard->issue_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}
        </div>

    </div>{{-- end card-box --}}

    {{-- ── INSTRUCTIONS ── --}}
    <div class="instructions-section">
        <h4>Instructions to Candidates</h4>
        @if($admitCard->instructions)
            <ol>
                @foreach(array_filter(preg_split('/\r?\n|(?<=\.)(?=\s*\d+\.)/', $admitCard->instructions)) as $line)
                    @if(trim($line))
                        <li>{{ preg_replace('/^\d+\.\s*/', '', trim($line)) }}</li>
                    @endif
                @endforeach
            </ol>
        @else
            <ol>
                <li>Enter the examination hall 15 minutes before the scheduled time. Students coming 15 minutes after the commencement of the examination will not be permitted to enter the examination hall or to write the exam.</li>
                <li>Without identity card and Hall Ticket, no student will be permitted to enter the Exam Hall.</li>
                <li>Read all instructions printed on the answer book and follow them strictly.</li>
                <li>Candidates should handover the answer script to the invigilator before leaving the exam Hall and shall not be permitted to re-enter.</li>
            </ol>
        @endif
    </div>

</div>

<script>
    // Auto-trigger print if opened with ?print=1
    if (new URLSearchParams(window.location.search).get('print') === '1') {
        window.addEventListener('load', function() { window.print(); });
    }
</script>
</body>
</html>
