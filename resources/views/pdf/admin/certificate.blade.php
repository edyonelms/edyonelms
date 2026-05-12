<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Certificate - {{ $cert->student->full_name ?? '' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            width: 297mm;
            height: 210mm;
            background: #fff;
            overflow: hidden;
        }

        .page {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: {{ $cert->type === 'achievement' ? '#fffbeb' : '#eff6ff' }};
            display: block;
        }

        /* ── Outer border frame ── */
        .outer-border {
            position: absolute;
            top: 6mm;
            left: 6mm;
            right: 6mm;
            bottom: 6mm;
            border: 3px solid {{ $cert->type === 'achievement' ? '#fbbf24' : '#93c5fd' }};
        }

        .inner-border {
            position: absolute;
            top: 3mm;
            left: 3mm;
            right: 3mm;
            bottom: 3mm;
            border: 1.5px solid {{ $cert->type === 'achievement' ? '#fde68a' : '#bfdbfe' }};
        }

        /* ── Corner ornaments ── */
        .corner {
            position: absolute;
            width: 20mm;
            height: 20mm;
            opacity: 0.15;
        }

        .corner-tl {
            top: 0;
            left: 0;
            border-top: 6px solid {{ $cert->type === 'achievement' ? '#d97706' : '#2563eb' }};
            border-left: 6px solid {{ $cert->type === 'achievement' ? '#d97706' : '#2563eb' }};
        }

        .corner-tr {
            top: 0;
            right: 0;
            border-top: 6px solid {{ $cert->type === 'achievement' ? '#d97706' : '#2563eb' }};
            border-right: 6px solid {{ $cert->type === 'achievement' ? '#d97706' : '#2563eb' }};
        }

        .corner-bl {
            bottom: 0;
            left: 0;
            border-bottom: 6px solid {{ $cert->type === 'achievement' ? '#d97706' : '#2563eb' }};
            border-left: 6px solid {{ $cert->type === 'achievement' ? '#d97706' : '#2563eb' }};
        }

        .corner-br {
            bottom: 0;
            right: 0;
            border-bottom: 6px solid {{ $cert->type === 'achievement' ? '#d97706' : '#2563eb' }};
            border-right: 6px solid {{ $cert->type === 'achievement' ? '#d97706' : '#2563eb' }};
        }

        /* ── Content ── */
        .content {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            text-align: center;
            padding: 14mm 20mm;
        }

        .org-name {
            font-size: 9pt;
            color: #9ca3af;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 3mm;
        }

        .cert-title {
            font-size: 28pt;
            font-weight: bold;
            color: {{ $cert->type === 'achievement' ? '#d97706' : '#1d4ed8' }};
            letter-spacing: 3px;
            text-transform: uppercase;
            font-family: "DejaVu Serif", Georgia, serif;
            margin-bottom: 4mm;
        }

        .divider {
            width: 70%;
            margin: 3mm auto;
            border-top: 1.5px solid {{ $cert->type === 'achievement' ? '#fbbf24' : '#93c5fd' }};
            position: relative;
        }

        .divider-star {
            position: absolute;
            top: -5px;
            left: 50%;
            margin-left: -6px;
            font-size: 10pt;
            color: {{ $cert->type === 'achievement' ? '#d97706' : '#2563eb' }};
            background: {{ $cert->type === 'achievement' ? '#fffbeb' : '#eff6ff' }};
            padding: 0 2mm;
            line-height: 1;
        }

        .presented-to {
            font-size: 9pt;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 2mm;
        }

        .student-name {
            font-size: 26pt;
            font-weight: bold;
            color: #1f2937;
            font-style: italic;
            font-family: "DejaVu Serif", Georgia, serif;
            margin-bottom: 1mm;
        }

        .admission-no {
            font-size: 8pt;
            color: #9ca3af;
            margin-bottom: 4mm;
        }

        .for-label {
            font-size: 9pt;
            color: #6b7280;
            margin-bottom: 1mm;
        }

        .event-name {
            font-size: 16pt;
            font-weight: bold;
            color: {{ $cert->type === 'achievement' ? '#d97706' : '#1d4ed8' }};
            margin-bottom: 2mm;
        }

        .description {
            font-size: 8.5pt;
            color: #6b7280;
            font-style: italic;
            max-width: 160mm;
            margin: 0 auto 4mm;
            line-height: 1.5;
        }

        /* ── Footer row ── */
        .footer-table {
            width: 100%;
            margin-top: 6mm;
        }

        .footer-table td {
            vertical-align: bottom;
            padding: 0 4mm;
        }

        .footer-left {
            text-align: left;
        }

        .footer-center {
            text-align: center;
        }

        .footer-right {
            text-align: right;
        }

        .cert-no-label {
            font-size: 7pt;
            color: #9ca3af;
            display: block;
        }

        .cert-no-value {
            font-size: 9pt;
            font-weight: bold;
            color: #374151;
        }

        .sig-line {
            width: 40mm;
            border-top: 1.5px solid #9ca3af;
            padding-top: 2mm;
            display: inline-block;
        }

        .sig-name {
            font-size: 10pt;
            font-weight: bold;
            color: #111827;
        }

        .sig-title {
            font-size: 7pt;
            color: #6b7280;
        }

        .date-label {
            font-size: 7pt;
            color: #9ca3af;
            display: block;
        }

        .date-value {
            font-size: 9pt;
            font-weight: bold;
            color: #374151;
        }
    </style>
</head>

<body>
    <div class="page">

        <div class="outer-border">
            <div class="inner-border"></div>
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>
        </div>

        <div class="content">

            @if ($cert->organization->logo ?? false)
                <img src="{{ $cert->organization->logo }}" height="40" style="margin-bottom:3mm;">
            @endif

            <p class="org-name">{{ $cert->organization->name ?? 'School Name' }}</p>

            <p class="cert-title">Certificate of {{ ucfirst($cert->type) }}</p>

            <div class="divider"><span class="divider-star">&#10022;</span></div>

            <p class="presented-to">This certificate is proudly presented to</p>

            <p class="student-name">{{ $cert->student->full_name ?? 'Student Name' }}</p>

            @if ($cert->student?->admission_no)
                <p class="admission-no">Admission No: {{ $cert->student->admission_no }}</p>
            @endif

            <p class="for-label">
                {{ $cert->type === 'achievement' ? 'For outstanding achievement in' : 'For actively participating in' }}
            </p>

            <p class="event-name">{{ $cert->event_name }}</p>

            @if ($cert->description)
                <p class="description">{{ $cert->description }}</p>
            @endif

            <div class="divider"><span class="divider-star">&#10022;</span></div>

            {{-- Footer --}}
            <table class="footer-table">
                <tr>
                    <td class="footer-left" style="width:30%;">
                        <span class="cert-no-label">Certificate No.</span>
                        <span class="cert-no-value">{{ $cert->certificate_no }}</span>
                    </td>
                    <td class="footer-center" style="width:40%;">
                        <div class="sig-line">
                            <p class="sig-name">{{ $cert->issued_by }}</p>
                            @if ($cert->issued_by_designation)
                                <p class="sig-title">{{ $cert->issued_by_designation }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="footer-right" style="width:30%;">
                        <span class="date-label">Date</span>
                        <span class="date-value">{{ $cert->issued_date->format('d M Y') }}</span>
                    </td>
                </tr>
            </table>

        </div>{{-- /content --}}
    </div>{{-- /page --}}
</body>

</html>
