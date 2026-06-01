<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Card - {{ $student->full_name ?? 'Student' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4 portrait; margin: 14px; }
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

        /* Co-Scholastic side-by-side wrapper */
        table.cosch { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-top: 12px; }
        table.cosch > tbody > tr > td { padding: 0; vertical-align: top; width: 50%; }
        table.cosch > tbody > tr > td.cell-left  { padding-right: 4px; }
        table.cosch > tbody > tr > td.cell-right { padding-left: 4px; }

        table.cosch-inner { width: 100%; border-collapse: collapse; }
        table.cosch-inner th, table.cosch-inner td { border: 1px solid #9ca3af; padding: 5px 8px; font-size: 9px; }
        table.cosch-inner th { background: #f3f4f6; font-weight: bold; text-align: left; }
        table.cosch-inner .gd { text-align: center; width: 60px; font-weight: bold; }

        table.foot { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.foot td { border: 1px solid #9ca3af; padding: 5px 8px; font-size: 9px; }
        table.foot .label { font-weight: bold; width: 14%; background: #f9fafb; }

        .result { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .result td { font-size: 10px; padding: 4px 0; }
        .result .r { text-align: right; }

        .sign { margin-top: 34px; width: 100%; }
        .sign td { font-size: 10px; font-weight: bold; }
        .sign .r { text-align: right; }
    </style>
</head>
<body>
    @include('admin._report-card-body')
</body>
</html>
