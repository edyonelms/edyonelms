<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ID Card — {{ $data['name'] }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; padding: 24px; color: #1f2937; }
        .toolbar { text-align: center; margin-bottom: 16px; }
        .toolbar button { background: #111827; color: #fff; border: 0; padding: 8px 18px; border-radius: 6px; font-size: 13px; cursor: pointer; font-weight: 600; }
        .card {
            width: 340px; margin: 0 auto; background: #fff; border-radius: 14px; overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,.12); border: 1px solid #e5e7eb;
        }
        .card-head { background: linear-gradient(135deg,#4f46e5,#7c3aed); color: #fff; padding: 16px; text-align: center; }
        .card-head h1 { font-size: 16px; font-weight: 700; }
        .card-head p { font-size: 11px; opacity: .9; margin-top: 2px; }
        .card-body { padding: 18px; text-align: center; }
        .photo { width: 92px; height: 92px; border-radius: 50%; object-fit: cover; border: 3px solid #eef2ff; margin: 0 auto 10px; display: block; }
        .photo-ph { width: 92px; height: 92px; border-radius: 50%; background: #eef2ff; color: #6366f1; font-size: 34px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
        .name { font-size: 18px; font-weight: 700; }
        .ident { font-size: 12px; color: #6b7280; margin-top: 2px; }
        .rows { margin-top: 14px; text-align: left; }
        .rows .row { display: flex; justify-content: space-between; font-size: 12px; padding: 5px 0; border-bottom: 1px dashed #eee; }
        .rows .row span:first-child { color: #9ca3af; }
        .rows .row span:last-child { font-weight: 600; color: #374151; }
        .meta { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; }
        .meta .qr img { width: 70px; height: 70px; }
        .meta .cardno { font-size: 11px; text-align: right; }
        .meta .cardno strong { display: block; color: #111827; }
        .status { display: inline-block; margin-top: 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 10px; border-radius: 999px; }
        .status.active { background: #dcfce7; color: #15803d; }
        .status.inactive { background: #f3f4f6; color: #6b7280; }
        .card-foot { background: #f9fafb; padding: 8px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #eee; }
        @media print { body { background: #fff; padding: 0; } .toolbar { display: none; } .card { box-shadow: none; } }
    </style>
</head>
<body>
    <div class="toolbar"><button onclick="window.print()">🖨 Print / Save as PDF</button></div>

    <div class="card">
        <div class="card-head">
            <h1>{{ $org->name ?? 'School' }}</h1>
            <p>{{ ucfirst($type) }} Identity Card</p>
        </div>
        <div class="card-body">
            @if ($data['photo'])
                <img src="{{ $data['photo'] }}" class="photo" alt="photo">
            @else
                <div class="photo-ph">{{ strtoupper(substr($data['name'], 0, 1)) }}</div>
            @endif
            <div class="name">{{ $data['name'] }}</div>
            <div class="ident">{{ $data['identifier'] }}</div>
            <span class="status {{ $card->status === 'active' ? 'active' : 'inactive' }}">{{ $card->status }}</span>

            <div class="rows">
                @foreach ($data['rows'] as $label => $value)
                    <div class="row"><span>{{ $label }}</span><span>{{ $value }}</span></div>
                @endforeach
                <div class="row"><span>Issued</span><span>{{ $card->issue_date?->format('d M Y') ?? '—' }}</span></div>
                <div class="row"><span>Valid Till</span><span>{{ $card->expiry_date?->format('d M Y') ?? '—' }}</span></div>
            </div>

            <div class="meta">
                <div class="qr">
                    @if ($card->qr_code)
                        <img src="data:image/png;base64,{{ $card->qr_code }}" alt="QR">
                    @endif
                </div>
                <div class="cardno">
                    Card No.
                    <strong>{{ $card->card_number }}</strong>
                </div>
            </div>
        </div>
        <div class="card-foot">If found, please return to {{ $org->name ?? 'the school' }}.</div>
    </div>
</body>
</html>
