<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Timetable — {{ $standard->name }} · {{ $section->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4 landscape; margin: 12mm 10mm; }
        body { font-family: "DejaVu Sans", sans-serif; color: #1f2937; font-size: 10pt; }

        .header { border-bottom: 2px solid #1d4ed8; padding-bottom: 6mm; margin-bottom: 6mm; }
        .school { font-size: 14pt; font-weight: bold; color: #1f2937; }
        .title  { font-size: 18pt; font-weight: bold; color: #1d4ed8; margin-top: 1.5mm; }
        .meta   { font-size: 9pt; color: #6b7280; margin-top: 2mm; }
        .meta strong { color: #1f2937; font-weight: 600; }

        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; vertical-align: top; }
        thead th {
            background: #1d4ed8; color: #fff; font-size: 9pt; text-transform: uppercase;
            letter-spacing: 0.6px; font-weight: 700; text-align: left;
        }
        tbody tr:nth-child(even) td { background: #f9fafb; }

        .sno  { width: 6%;  text-align: center; color: #6b7280; }
        .csec { width: 14%; }
        .subj { width: 18%; font-weight: 600; }
        .tch  { width: 28%; }
        .time { width: 16%; white-space: nowrap; }
        .days { width: 18%; }

        .badge {
            display: inline-block; padding: 1.5px 6px; border-radius: 9px;
            font-size: 8pt; font-weight: 600; border: 1px solid;
            margin-right: 2px; margin-bottom: 2px;
        }
        .badge-blue   { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
        .badge-indigo { background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }
        .badge-purple { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }

        .teacher-line { font-size: 9pt; margin-bottom: 2px; }
        .teacher-line .name { font-weight: 600; color: #1f2937; }
        .teacher-line .days { color: #6b7280; font-size: 8pt; }
        .teacher-line + .teacher-line { padding-top: 3px; border-top: 1px dashed #e5e7eb; margin-top: 3px; }

        .empty { text-align: center; padding: 18mm 6mm; color: #9ca3af; }

        .footer {
            position: fixed; bottom: 4mm; left: 10mm; right: 10mm;
            text-align: right; font-size: 7.5pt; color: #9ca3af;
            border-top: 1px solid #e5e7eb; padding-top: 2mm;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="school">{{ $orgName }}</div>
        <div class="title">Class Timetable</div>
        <div class="meta">
            <strong>Class:</strong> {{ $standard->name }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Section:</strong> {{ $section->name }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Generated:</strong> {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}
        </div>
    </div>

    @if (empty($rows))
        <div class="empty">No timetable entries scheduled for this section.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th class="csec">Class · Section</th>
                    <th class="subj">Subject</th>
                    <th class="tch">Teacher(s) &amp; Day Coverage</th>
                    <th class="time">Time</th>
                    <th class="days">All Days</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $r)
                    <tr>
                        <td class="sno">{{ $i + 1 }}</td>
                        <td class="csec">
                            <span class="badge badge-blue">{{ $standard->name }} · {{ $section->name }}</span>
                        </td>
                        <td class="subj">{{ $r['subject'] }}</td>
                        <td class="tch">
                            @foreach ($r['teachers'] as $t)
                                <div class="teacher-line">
                                    <span class="name">{{ $t['name'] }}</span>
                                    <div class="days">{{ implode(', ', $t['days']) }}</div>
                                </div>
                            @endforeach
                        </td>
                        <td class="time">
                            {{ \Carbon\Carbon::parse($r['start_time'])->format('h:i A') }}
                            <br>
                            <span style="color:#6b7280;font-size:8pt;">to {{ \Carbon\Carbon::parse($r['end_time'])->format('h:i A') }}</span>
                        </td>
                        <td class="days">
                            @foreach ($r['days'] as $d)
                                <span class="badge badge-indigo">{{ $d }}</span>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        {{ $orgName }} — Class Timetable — Page 1
    </div>

</body>
</html>
