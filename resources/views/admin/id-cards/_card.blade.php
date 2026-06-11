{{-- Shared ID-card design (front + back). Expects $c = IdCardService::cardViewData(). --}}
<style>
    .idc-wrap { --idc-grad: linear-gradient(140deg, #6f5bff 0%, #9b4dde 55%, #b85fd6 100%);
        --idc-purple: #7c3aed; --idc-ink: #3b3550; --idc-muted: #6b6480; }
    .idc-wrap * { box-sizing: border-box; margin: 0; padding: 0; }
    .idc-wrap { display: flex; flex-wrap: wrap; gap: 22px; justify-content: center;
        font-family: 'Poppins','Segoe UI',Arial,sans-serif; }
    .idc-card { width: 300px; height: 480px; background: #fff; border-radius: 18px;
        position: relative; overflow: hidden; box-shadow: 0 12px 30px rgba(80,40,130,.22);
        -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* ---------- FRONT ---------- */
    .idc-header { position: relative; height: 188px; background: var(--idc-grad);
        border-radius: 0 0 50% 50% / 0 0 26% 26%; padding: 22px 20px 0; text-align: center; }
    .idc-blob { position: absolute; border-radius: 50%; background: rgba(255,255,255,.13); }
    .idc-blob.b1 { width: 120px; height: 120px; top: -30px; right: -20px; }
    .idc-blob.b2 { width: 80px; height: 80px; top: 60px; left: -25px; }
    .idc-logo { width: 46px; height: 46px; border-radius: 10px; background: #fff; object-fit: contain;
        padding: 4px; margin: 0 auto 6px; display: block; box-shadow: 0 3px 8px rgba(0,0,0,.18); }
    .idc-school { color: #fff; font-size: 17px; font-weight: 700; letter-spacing: .5px;
        line-height: 1.15; text-shadow: 0 1px 3px rgba(0,0,0,.18); }
    .idc-sub { color: rgba(255,255,255,.92); font-size: 9px; margin-top: 4px; line-height: 1.35; }
    .idc-photo-ring { position: absolute; left: 50%; transform: translateX(-50%); bottom: -54px;
        width: 116px; height: 116px; border-radius: 50%; background: var(--idc-grad);
        padding: 5px; box-shadow: 0 6px 14px rgba(80,40,130,.3); }
    .idc-photo { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; background: #ede9f7;
        border: 3px solid #fff; }
    .idc-photo-ph { width: 100%; height: 100%; border-radius: 50%; border: 3px solid #fff;
        background: #ece8f7; color: var(--idc-purple); font-size: 38px; font-weight: 700;
        display: flex; align-items: center; justify-content: center; }
    .idc-body { padding: 70px 26px 16px; }
    .idc-name { text-align: center; font-size: 16px; font-weight: 700; color: var(--idc-ink);
        text-transform: uppercase; letter-spacing: .4px; }
    .idc-desig { text-align: center; font-size: 10px; color: var(--idc-purple); font-weight: 600;
        margin-top: 2px; margin-bottom: 12px; }
    .idc-row { display: flex; font-size: 10.5px; line-height: 1.5; margin-bottom: 5px; color: var(--idc-ink); }
    .idc-row .k { width: 96px; font-weight: 600; color: var(--idc-muted); flex: 0 0 96px; }
    .idc-row .sep { width: 8px; flex: 0 0 8px; color: var(--idc-purple); }
    .idc-row .v { flex: 1; font-weight: 600; word-break: break-word; }

    /* ---------- BACK ---------- */
    .idc-back { padding: 26px 22px 18px; height: 100%; display: flex; flex-direction: column; }
    .idc-pill { background: var(--idc-grad); color: #fff; font-size: 12px; font-weight: 700;
        letter-spacing: 1px; text-transform: uppercase; text-align: center; padding: 9px 10px;
        border-radius: 8px; margin-bottom: 16px; box-shadow: 0 4px 10px rgba(124,58,237,.25); }
    .idc-back .idc-row { font-size: 10px; }
    .idc-back .idc-row .k { width: 70px; flex: 0 0 70px; }
    .idc-terms { font-size: 9.5px; color: var(--idc-muted); line-height: 1.5; margin-bottom: 6px;
        display: flex; gap: 6px; }
    .idc-terms::before { content: '•'; color: var(--idc-purple); font-weight: 700; }
    .idc-contact { margin-top: auto; }
    .idc-contact .idc-row { font-size: 10px; }
    .idc-sign { text-align: center; margin-top: 16px; }
    .idc-sign .line { border-top: 1.5px solid var(--idc-ink); width: 130px; margin: 0 auto 4px; }
    .idc-sign .role { font-size: 12px; font-weight: 700; color: var(--idc-ink); }
    .idc-qr { width: 64px; height: 64px; }
    .idc-cardno { font-family: 'Courier New', monospace; font-size: 9px; color: var(--idc-muted);
        letter-spacing: .5px; text-align: center; margin-top: 12px; }
    .idc-status { display: inline-block; font-size: 8px; font-weight: 700; text-transform: uppercase;
        padding: 2px 9px; border-radius: 999px; }
    .idc-status.active { background: #dcfce7; color: #15803d; }
    .idc-status.inactive { background: #f3f4f6; color: #6b7280; }
</style>

<div class="idc-wrap">
    {{-- ============ FRONT ============ --}}
    <div class="idc-card">
        <div class="idc-header">
            <span class="idc-blob b1"></span>
            <span class="idc-blob b2"></span>
            @if (!empty($c['school']['logo']))
                <img src="{{ $c['school']['logo'] }}" class="idc-logo" alt="logo">
            @endif
            <div class="idc-school">{{ $c['school']['name'] }}</div>
            @if (!empty($c['school']['address']))
                <div class="idc-sub">{{ $c['school']['address'] }}</div>
            @endif
            <div class="idc-photo-ring">
                @if (!empty($c['photo']))
                    <img src="{{ $c['photo'] }}" class="idc-photo" alt="photo">
                @else
                    <div class="idc-photo-ph">{{ strtoupper(substr($c['name'], 0, 1)) }}</div>
                @endif
            </div>
        </div>
        <div class="idc-body">
            <div class="idc-name">{{ $c['name'] }}</div>
            <div class="idc-desig">{{ $c['subtitle'] }}</div>
            @foreach ($c['front_rows'] as $k => $v)
                <div class="idc-row"><span class="k">{{ $k }}</span><span class="sep">:</span><span class="v">{{ $v ?: '—' }}</span></div>
            @endforeach
        </div>
    </div>

    {{-- ============ BACK ============ --}}
    <div class="idc-card">
        <div class="idc-back">
            @if ($c['back_mode'] === 'transport')
                <div class="idc-pill">Transport Details</div>
                @if (!empty($c['transport']))
                    @foreach ($c['transport'] as $k => $v)
                        <div class="idc-row"><span class="k">{{ $k }}</span><span class="sep">:</span><span class="v">{{ $v ?: '—' }}</span></div>
                    @endforeach
                @else
                    <div class="idc-terms">No transport assigned to this student.</div>
                @endif
            @else
                <div class="idc-pill">Terms &amp; Conditions</div>
                <div class="idc-terms">This card is the property of the institution and is non-transferable.</div>
                <div class="idc-terms">If found, please return to the school address mentioned.</div>
                <div class="idc-terms">Must be produced on demand within the premises.</div>
            @endif

            <div class="idc-contact">
                @if (!empty($c['school']['phone']))
                    <div class="idc-row"><span class="k">Phone</span><span class="sep">:</span><span class="v">{{ $c['school']['phone'] }}</span></div>
                @endif
                @if (!empty($c['school']['email']))
                    <div class="idc-row"><span class="k">Mail</span><span class="sep">:</span><span class="v">{{ $c['school']['email'] }}</span></div>
                @endif
                @if (!empty($c['school']['website']))
                    <div class="idc-row"><span class="k">Website</span><span class="sep">:</span><span class="v">{{ $c['school']['website'] }}</span></div>
                @endif
                <div class="idc-row"><span class="k">Valid Till</span><span class="sep">:</span><span class="v">{{ $c['expiry_date'] }}</span></div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;">
                    @if (!empty($c['qr_code']))
                        <img src="data:image/png;base64,{{ $c['qr_code'] }}" class="idc-qr" alt="QR">
                    @else
                        <span></span>
                    @endif
                    <span class="idc-status {{ $c['status'] === 'active' ? 'active' : 'inactive' }}">{{ $c['status'] }}</span>
                </div>

                <div class="idc-sign">
                    <div class="line"></div>
                    <div class="role">Principal</div>
                </div>
                <div class="idc-cardno">Card No: {{ $c['card_number'] }}</div>
            </div>
        </div>
    </div>
</div>
