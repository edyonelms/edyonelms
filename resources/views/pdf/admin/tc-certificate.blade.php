<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Transfer Certificate - {{ $tc->student->full_name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4 portrait;
            margin: 12mm 14mm;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            border-bottom: 2px double #000;
            padding-bottom: 4mm;
            margin-bottom: 3mm;
        }

        .school-name {
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-family: "DejaVu Serif", Georgia, serif;
            color: #111;
        }

        .affiliation {
            font-size: 9pt;
            color: #333;
            margin-top: 1mm;
            letter-spacing: 0.5px;
        }

        .address {
            font-size: 8pt;
            color: #555;
            margin-top: 1mm;
        }

        .codes {
            font-size: 8pt;
            color: #555;
            margin-top: 1mm;
        }

        /* ── TC Title ── */
        .tc-title-row {
            width: 100%;
            margin-bottom: 3mm;
        }
        .tc-title-row td {
            vertical-align: middle;
        }

        .tc-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-align: center;
            text-decoration: underline;
            color: #111;
        }

        .book-no {
            font-size: 8.5pt;
            white-space: nowrap;
        }

        /* ── Admission no row ── */
        .adm-row {
            width: 100%;
            margin-bottom: 2mm;
        }

        /* ── Main table ── */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
        }

        .main-table tr td {
            border: 0.5px solid #555;
            padding: 2.5mm 3mm;
            vertical-align: top;
            line-height: 1.4;
        }

        .main-table tr td:first-child {
            width: 62%;
            font-size: 8.5pt;
            color: #222;
        }

        .main-table tr td:last-child {
            width: 38%;
            font-size: 8.5pt;
            font-weight: bold;
            color: #000;
        }

        .main-table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        /* ── Signature row ── */
        .sig-table {
            width: 100%;
            margin-top: 10mm;
        }
        .sig-table td {
            text-align: center;
            width: 33.33%;
            font-size: 8pt;
            color: #333;
        }
        .sig-line {
            display: block;
            width: 36mm;
            border-top: 1px solid #000;
            margin: 0 auto 1mm;
            padding-top: 2mm;
        }
    </style>
</head>
<body>

    {{-- ── SCHOOL HEADER ── --}}
    <div class="header">
        @if (($tc->organization->logo ?? false))
            <div style="margin-bottom:2mm;">
                <img src="{{ $tc->organization->logo }}" height="50">
            </div>
        @endif

        <p class="school-name">{{ strtoupper($tc->organization->name ?? 'School Name') }}</p>

        @if ($tc->organization->affiliation_board ?? false)
            <p class="affiliation">Affiliated to {{ $tc->organization->affiliation_board }}</p>
        @else
            <p class="affiliation">Affiliated to CBSE, New Delhi</p>
        @endif

        @if ($tc->organization->address ?? false)
            <p class="address">{{ $tc->organization->address }}</p>
        @endif

        <p class="codes">
            @if ($tc->organization->school_code ?? false)
                School Code: {{ $tc->organization->school_code }}
            @endif
            @if (($tc->organization->school_code ?? false) && ($tc->organization->affiliation_no ?? false))
                &nbsp;&nbsp;|&nbsp;&nbsp;
            @endif
            @if ($tc->organization->affiliation_no ?? false)
                Affiliation No: {{ $tc->organization->affiliation_no }}
            @endif
        </p>
    </div>

    {{-- ── TC TITLE + BOOK NO ── --}}
    <table class="tc-title-row">
        <tr>
            <td style="width:20%; font-size:8.5pt;">
                @if ($tc->book_no)
                    <strong>Book No:</strong> {{ $tc->book_no }}
                @endif
            </td>
            <td style="width:60%;">
                <p class="tc-title">Transfer Certificate</p>
            </td>
            <td style="width:20%; text-align:right; font-size:8.5pt;">
                @if ($tc->tc_no)
                    <strong>TC No:</strong> {{ $tc->tc_no }}
                @endif
            </td>
        </tr>
    </table>

    {{-- ── ADMISSION NO ── --}}
    <table class="adm-row">
        <tr>
            <td style="width:50%; font-size:8.5pt; padding-bottom:2mm;">
                <strong>Admission No:</strong> {{ $tc->student->admission_no ?? '—' }}
            </td>
            <td style="width:50%; text-align:right; font-size:8.5pt; padding-bottom:2mm;">
            </td>
        </tr>
    </table>

    {{-- ── MAIN DATA TABLE ── --}}
    <table class="main-table">
        <tr>
            <td>Name of pupil</td>
            <td>:&nbsp; {{ $tc->student->full_name ?? '—' }}</td>
        </tr>
        <tr>
            <td>Mother's Name</td>
            <td>:&nbsp; {{ $tc->student->mother_name ?? '—' }}</td>
        </tr>
        <tr>
            <td>Father's / Guardian Name</td>
            <td>:&nbsp; {{ $tc->student->father_name ?? '—' }}</td>
        </tr>
        <tr>
            <td>Nationality</td>
            <td>:&nbsp; {{ $tc->nationality }}</td>
        </tr>
        <tr>
            <td>Whether the Candidate belongs to Schedule Caste or Schedule Tribe</td>
            <td>:&nbsp; {{ $tc->is_sc_st ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
            <td>
                Date of first admission in the school with Class
            </td>
            <td>
                :&nbsp; {{ $tc->student->date_of_admission?->format('d/m/Y') ?? '—' }}
                @if ($tc->student->standard?->name ?? false)
                    &nbsp;&nbsp; <strong>Class:</strong> {{ $tc->student->standard->name }}
                @endif
            </td>
        </tr>
        <tr>
            <td>Date of birth according to Admission register (in figures)</td>
            <td>:&nbsp; {{ $tc->student->dob?->format('d/m/Y') ?? '—' }}</td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;(in words)</td>
            <td>:&nbsp;
                @if ($tc->student->dob)
                    @php
                        $months = ['','JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE',
                                   'JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'];
                        $d = $tc->student->dob;
                        $ones = ['','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE',
                                 'TEN','ELEVEN','TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN',
                                 'SEVENTEEN','EIGHTEEN','NINETEEN','TWENTY','TWENTY ONE','TWENTY TWO',
                                 'TWENTY THREE','TWENTY FOUR','TWENTY FIVE','TWENTY SIX','TWENTY SEVEN',
                                 'TWENTY EIGHT','TWENTY NINE','THIRTY','THIRTY ONE'];
                        $year = $d->year;
                        $thousands = intdiv($year, 1000);
                        $hundreds  = intdiv($year % 1000, 100);
                        $tens      = $year % 100;
                        $yr = ($thousands > 0 ? $ones[$thousands].' THOUSAND ' : '')
                            . ($hundreds > 0 ? $ones[$hundreds].' HUNDRED ' : '')
                            . ($tens > 0 ? $ones[$tens] : '');
                    @endphp
                    {{ $ones[(int)$d->format('j')] }} {{ $months[(int)$d->format('n')] }} {{ trim($yr) }}
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <td>Class in which the pupil last studied (in figures)</td>
            <td>:&nbsp; {{ $tc->last_class_studied ?? '—' }}</td>
        </tr>
        <tr>
            <td>School/Board Annual examination last taken with results</td>
            <td>:&nbsp; {{ $tc->exam_last_taken ?? '—' }}</td>
        </tr>
        <tr>
            <td>Whether failed, if so once/twice in the same class</td>
            <td>:&nbsp; {{ $tc->whether_failed }}</td>
        </tr>
        <tr>
            <td>Subjects studied</td>
            <td>:&nbsp; {{ $tc->subjects_studied ?? '—' }}</td>
        </tr>
        <tr>
            <td>Whether qualified for promotion to the higher class</td>
            <td>:&nbsp; {{ $tc->qualified_for_promotion }}</td>
        </tr>
        <tr>
            <td>Month upto which the pupil has paid school dues paid</td>
            <td>:&nbsp; {{ $tc->fees_paid_upto ?? '—' }}</td>
        </tr>
        <tr>
            <td>Any fee concession availed of; if so, the nature of such concession</td>
            <td>:&nbsp; {{ $tc->fee_concession ?? 'None' }}</td>
        </tr>
        <tr>
            <td>Total No. of working days</td>
            <td>:&nbsp; {{ $tc->total_working_days }}</td>
        </tr>
        <tr>
            <td>Total No. of working days present</td>
            <td>:&nbsp; {{ $tc->days_present }}</td>
        </tr>
        <tr>
            <td>Whether NCC Cadet/Boy Scout/Girl Guide</td>
            <td>:&nbsp; {{ $tc->is_ncc_scout }}</td>
        </tr>
        <tr>
            <td>Games played or extra curricular activities in which pupil usually took part (mention)</td>
            <td>:&nbsp; {{ $tc->extra_activities ?? 'None' }}</td>
        </tr>
        <tr>
            <td>General conduct</td>
            <td>:&nbsp; {{ $tc->general_conduct }}</td>
        </tr>
        <tr>
            <td>Date of application for certificate</td>
            <td>:&nbsp; {{ $tc->application_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Date of issue of certificate</td>
            <td>:&nbsp; {{ $tc->issue_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Reason for leaving the school</td>
            <td>:&nbsp; {{ $tc->reason_for_leaving ?? '—' }}</td>
        </tr>
        <tr>
            <td>Any other Remark</td>
            <td>:&nbsp; {{ $tc->remarks ?? 'No' }}</td>
        </tr>
    </table>

    {{-- ── SIGNATURES ── --}}
    <table class="sig-table">
        <tr>
            <td>
                <span class="sig-line"></span>
                Class Teacher
            </td>
            <td>
                <span class="sig-line"></span>
                Issuer
            </td>
            <td>
                <span class="sig-line"></span>
                Principal
            </td>
        </tr>
    </table>

</body>
</html>