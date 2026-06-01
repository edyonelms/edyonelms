<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Timetable — {{ $standard->name }} — {{ $section->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4 portrait; margin: 16mm 14mm 18mm 14mm; }
        body { font-family: "DejaVu Sans", sans-serif; color: #000; font-size: 10.5pt; line-height: 1.4; }

        .center { text-align: center; }

        /* Header */
        .logo {
            display: block; margin: 0 auto 4mm auto;
            max-height: 22mm; max-width: 38mm;
        }
        .org-name {
            font-size: 17pt; font-weight: 700; letter-spacing: 0.4px;
            text-transform: uppercase;
        }
        .org-contact {
            font-size: 9.5pt; color: #333; margin-top: 1.5mm;
        }
        .org-contact span + span { margin-left: 6mm; }

        .divider {
            border-top: 1px solid #000;
            margin: 5mm 0 4mm 0;
        }

        .doc-title {
            font-size: 13pt; font-weight: 700; letter-spacing: 0.3px;
            text-transform: uppercase; margin-bottom: 5mm;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1mm;
        }
        thead th {
            background: #f2f2f2;
            font-size: 9.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: left;
            padding: 5px 7px;
            border: 1px solid #000;
            color: #000;
        }
        tbody td {
            padding: 5px 7px;
            border: 1px solid #555;
            vertical-align: top;
            font-size: 10pt;
        }
        tbody tr td:first-child { text-align: center; color: #333; width: 8%; }
        td.subject { font-weight: 700; }
        td.time { white-space: nowrap; width: 18%; }
        td.days { width: 18%; }

        .teacher-block + .teacher-block {
            margin-top: 3px; padding-top: 3px; border-top: 1px dashed #aaa;
        }
        .teacher-name { font-weight: 700; }
        .teacher-days { color: #555; font-size: 9pt; }

        .empty { text-align: center; padding: 14mm 6mm; color: #555; font-style: italic; }

        .footer {
            position: fixed;
            bottom: 6mm; left: 14mm; right: 14mm;
            border-top: 1px solid #999;
            padding-top: 2mm;
            font-size: 8pt; color: #666;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ============ HEADER ============ --}}
    @php
        $logoSrc = null;
        if (!empty($organization?->logo)) {
            if (\Illuminate\Support\Str::startsWith($organization->logo, ['http://', 'https://'])) {
                $logoSrc = $organization->logo;
            } elseif (file_exists(public_path('storage/' . $organization->logo))) {
                $logoSrc = public_path('storage/' . $organization->logo);
            }
        }

        // Prefer SchoolInfo's email/mobile (set by superadmin during school create flow);
        // fall back to the Organization's email/mobile_number.
        $schoolEmail = $schoolInfo->school_email
            ?? $organization?->email
            ?? null;
        $schoolMobile = $schoolInfo->school_mobile
            ?? $organization?->mobile_number
            ?? null;
    @endphp

    <div class="center">
        @if ($logoSrc)
            <img class="logo" src="{{ $logoSrc }}" alt="Logo">
        @endif

        <div class="org-name">{{ $organization?->name ?? 'School' }}</div>

        @if ($schoolEmail || $schoolMobile)
            <div class="org-contact">
                @if ($schoolEmail)<span>Email: {{ $schoolEmail }}</span>@endif
                @if ($schoolMobile)<span>Phone: {{ $schoolMobile }}</span>@endif
            </div>
        @endif
    </div>

    <div class="divider"></div>

    <div class="center doc-title">
        Timetable — {{ $standard->name }} — {{ $section->name }}
    </div>

    {{-- ============ TABLE ============ --}}
    @if (empty($rows))
        <div class="empty">No timetable entries scheduled for this section.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:8%;">S. No.</th>
                    <th>Subject</th>
                    <th>Teacher &amp; Days</th>
                    <th class="time">Time</th>
                    <th class="days">Days</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="subject">{{ $r['subject'] }}</td>
                        <td>
                            @foreach ($r['teachers'] as $t)
                                <div class="teacher-block">
                                    <span class="teacher-name">{{ $t['name'] }}</span>
                                    <div class="teacher-days">{{ implode(', ', $t['days']) }}</div>
                                </div>
                            @endforeach
                        </td>
                        <td class="time">
                            {{ \Carbon\Carbon::parse($r['start_time'])->format('h:i A') }}
                            – {{ \Carbon\Carbon::parse($r['end_time'])->format('h:i A') }}
                        </td>
                        <td class="days">{{ implode(', ', $r['days']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        {{ $organization?->name ?? 'School' }} &nbsp;·&nbsp; Class Timetable &nbsp;·&nbsp; Generated {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}
    </div>

</body>
</html>
