{{-- Shared ID-card design (front + back). Expects $c = IdCardService::cardViewData(). --}}
<style>
    .idc-wrap {
        --idc-g1: #6d5bef; --idc-g2: #9446d6; --idc-g3: #b659d4;
        --idc-grad: linear-gradient(135deg, #6d5bef 0%, #8b46da 52%, #b659d4 100%);
        --idc-grad-soft: linear-gradient(135deg, #7c6bf2 0%, #a35adf 100%);
        --idc-purple: #7c3aed; --idc-ink: #2f2a44; --idc-muted: #8983a3; --idc-line: #ece8f6;
    }
    .idc-wrap * { box-sizing: border-box; margin: 0; padding: 0; }
    .idc-wrap { display: flex; flex-wrap: wrap; gap: 26px; justify-content: center; align-items: flex-start;
        font-family: 'Poppins','Segoe UI',Arial,sans-serif; padding: 4px; }
    .idc-card { width: 324px; height: 512px; background: #fff; border-radius: 22px; position: relative;
        overflow: hidden; box-shadow: 0 18px 44px rgba(86,46,140,.26), 0 2px 6px rgba(86,46,140,.12);
        -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .idc-card::after { content: ''; position: absolute; inset: 0; border-radius: 22px;
        border: 1px solid rgba(124,58,237,.10); pointer-events: none; }

    /* decorative bubbles */
    .idc-bubble { position: absolute; border-radius: 50%; z-index: 1; }
    .idc-bubble.p { background: rgba(190,89,212,.10); }
    .idc-bubble.w { background: rgba(255,255,255,.14); }

    /* ───────────── FRONT ───────────── */
    .idc-header { position: relative; height: 196px; background: var(--idc-grad); z-index: 2;
        border-radius: 0 0 46% 46% / 0 0 22% 22%; padding: 26px 22px 0; text-align: center; overflow: hidden; }
    .idc-header .idc-bubble.h1 { width: 130px; height: 130px; top: -34px; right: -26px; background: rgba(255,255,255,.12); }
    .idc-header .idc-bubble.h2 { width: 70px; height: 70px; top: 56px; left: -22px; background: rgba(255,255,255,.10); }
    .idc-logo-w { width: 54px; height: 54px; margin: 0 auto 8px; border-radius: 14px; background: #fff;
        display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 14px rgba(0,0,0,.20); position: relative; z-index: 3; }
    .idc-logo-w img { width: 100%; height: 100%; object-fit: contain; border-radius: 12px; padding: 5px; }
    .idc-logo-w .ph { font-size: 22px; font-weight: 800; color: var(--idc-purple); }
    .idc-school { color: #fff; font-size: 18px; font-weight: 700; letter-spacing: .4px; line-height: 1.15;
        text-shadow: 0 1px 4px rgba(0,0,0,.20); position: relative; z-index: 3; }
    .idc-school-sub { color: rgba(255,255,255,.9); font-size: 8.5px; margin-top: 5px; line-height: 1.4;
        letter-spacing: .3px; position: relative; z-index: 3; padding: 0 14px; }

    .idc-photo-ring { position: absolute; left: 50%; transform: translateX(-50%); bottom: -56px; z-index: 5;
        width: 122px; height: 122px; border-radius: 50%; background: var(--idc-grad);
        padding: 5px; box-shadow: 0 10px 22px rgba(86,46,140,.34); }
    .idc-photo { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; background: #ede9f7; border: 4px solid #fff; }
    .idc-photo-ph { width: 100%; height: 100%; border-radius: 50%; border: 4px solid #fff;
        background: linear-gradient(135deg,#efe9fb,#e2d6f6); color: var(--idc-purple); font-size: 42px; font-weight: 700;
        display: flex; align-items: center; justify-content: center; }

    .idc-body { padding: 72px 24px 0; position: relative; z-index: 2; }
    .idc-name { text-align: center; font-size: 17px; font-weight: 700; color: var(--idc-ink);
        text-transform: uppercase; letter-spacing: .5px; line-height: 1.1; }
    .idc-desig { text-align: center; font-size: 9.5px; color: #fff; font-weight: 600; margin: 7px auto 0;
        background: var(--idc-grad-soft); display: inline-block; padding: 3px 14px; border-radius: 999px;
        letter-spacing: .6px; text-transform: uppercase; box-shadow: 0 3px 8px rgba(124,58,237,.28); }
    .idc-desig-wrap { text-align: center; margin-bottom: 15px; }

    .idc-rows { padding: 0 2px; }
    .idc-row { display: flex; align-items: baseline; font-size: 10.5px; line-height: 1.5; padding: 5px 0;
        border-bottom: 1px solid var(--idc-line); color: var(--idc-ink); }
    .idc-row:last-child { border-bottom: 0; }
    .idc-row .k { width: 92px; flex: 0 0 92px; font-weight: 600; color: var(--idc-muted);
        text-transform: uppercase; font-size: 8.5px; letter-spacing: .4px; }
    .idc-row .v { flex: 1; font-weight: 600; word-break: break-word; padding-left: 6px;
        border-left: 2px solid rgba(124,58,237,.18); }

    .idc-front-foot { position: absolute; left: 0; right: 0; bottom: 0; background: var(--idc-grad);
        color: #fff; padding: 9px 22px; display: flex; justify-content: space-between; align-items: center; z-index: 4; }
    .idc-front-foot .lbl { font-size: 7px; text-transform: uppercase; letter-spacing: .8px; opacity: .85; }
    .idc-front-foot .val { font-size: 10px; font-weight: 700; font-family: 'Courier New',monospace; letter-spacing: .5px; }
    .idc-front-foot .col-r { text-align: right; }

    /* ───────────── BACK ───────────── */
    .idc-back { padding: 26px 22px 0; height: 100%; display: flex; flex-direction: column; position: relative; z-index: 2; }
    .idc-back .idc-bubble.b1 { width: 120px; height: 120px; bottom: 70px; right: -34px; background: rgba(190,89,212,.10); }
    .idc-back .idc-bubble.b2 { width: 64px; height: 64px; top: 90px; left: -26px; background: rgba(109,91,239,.08); }
    .idc-pill { background: var(--idc-grad); color: #fff; font-size: 12px; font-weight: 700; letter-spacing: 1.2px;
        text-transform: uppercase; text-align: center; padding: 10px; border-radius: 10px; margin-bottom: 18px;
        box-shadow: 0 6px 14px rgba(124,58,237,.28); position: relative; z-index: 3; }

    .idc-sec-title { font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
        color: var(--idc-purple); margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .idc-sec-title::after { content: ''; flex: 1; height: 1px; background: var(--idc-line); }

    .idc-panel { background: #faf8fe; border: 1px solid var(--idc-line); border-radius: 12px; padding: 12px 14px;
        position: relative; z-index: 3; }
    .idc-panel .idc-row { font-size: 10px; padding: 4px 0; }
    .idc-panel .idc-row .k { width: 64px; flex: 0 0 64px; }
    .idc-panel .idc-row .v { border-left: 0; padding-left: 6px; }

    .idc-terms { list-style: none; position: relative; z-index: 3; }
    .idc-terms li { font-size: 9.5px; color: var(--idc-muted); line-height: 1.5; margin-bottom: 8px;
        display: flex; gap: 8px; }
    .idc-terms li::before { content: ''; flex: 0 0 6px; width: 6px; height: 6px; border-radius: 50%;
        background: var(--idc-grad); margin-top: 5px; }

    .idc-empty { font-size: 10px; color: var(--idc-muted); text-align: center; padding: 14px 8px;
        background: #faf8fe; border: 1px dashed var(--idc-line); border-radius: 12px; position: relative; z-index: 3; }

    .idc-contact { margin-top: 16px; position: relative; z-index: 3; }
    .idc-contact .ci { display: flex; align-items: center; gap: 9px; font-size: 9.5px; color: var(--idc-ink);
        font-weight: 600; padding: 4px 0; }
    .idc-contact .ci svg { width: 14px; height: 14px; flex: 0 0 14px; color: var(--idc-purple); }
    .idc-contact .ci span { word-break: break-all; }

    .idc-meta { margin-top: auto; padding-top: 14px; display: flex; align-items: flex-end; justify-content: space-between;
        position: relative; z-index: 3; }
    .idc-qr { width: 66px; height: 66px; border-radius: 8px; background: #fff; padding: 3px;
        box-shadow: 0 3px 8px rgba(0,0,0,.14); border: 1px solid var(--idc-line); }
    .idc-qr img { width: 100%; height: 100%; object-fit: contain; }
    .idc-qr-lbl { font-size: 6.5px; color: var(--idc-muted); text-align: center; margin-top: 3px;
        text-transform: uppercase; letter-spacing: .5px; }
    .idc-sign { text-align: center; }
    .idc-sign .line { border-top: 1.5px solid var(--idc-ink); width: 118px; margin: 0 auto 5px; }
    .idc-sign .role { font-size: 12px; font-weight: 700; color: var(--idc-ink); }
    .idc-status { display: inline-block; font-size: 7.5px; font-weight: 700; text-transform: uppercase;
        padding: 2px 9px; border-radius: 999px; letter-spacing: .5px; margin-top: 5px; }
    .idc-status.active { background: #dcfce7; color: #15803d; }
    .idc-status.inactive { background: #f3f4f6; color: #6b7280; }

    /* barcode footer */
    .idc-barcode-wrap { margin: 14px -22px 0; padding: 9px 22px 12px; background: #faf8fe; border-top: 1px solid var(--idc-line);
        position: relative; z-index: 3; }
    .idc-barcode { height: 30px; width: 100%; border-radius: 3px;
        background-image: repeating-linear-gradient(90deg, #2f2a44 0, #2f2a44 2px, #fff 2px, #fff 4px,
            #2f2a44 4px, #2f2a44 5px, #fff 5px, #fff 8px, #2f2a44 8px, #2f2a44 11px, #fff 11px, #fff 13px); }
    .idc-cardno { font-family: 'Courier New',monospace; font-size: 9px; color: var(--idc-ink); letter-spacing: 1.5px;
        text-align: center; margin-top: 6px; font-weight: 700; }
</style>

<div class="idc-wrap">
    {{-- ============ FRONT ============ --}}
    <div class="idc-card">
        <div class="idc-header">
            <span class="idc-bubble h1"></span>
            <span class="idc-bubble h2"></span>
            <div class="idc-logo-w">
                @if (!empty($c['school']['logo']))
                    <img src="{{ $c['school']['logo'] }}" alt="logo">
                @else
                    <span class="ph">{{ strtoupper(substr($c['school']['name'], 0, 1)) }}</span>
                @endif
            </div>
            <div class="idc-school">{{ $c['school']['name'] }}</div>
            @if (!empty($c['school']['address']))
                <div class="idc-school-sub">{{ $c['school']['address'] }}</div>
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
            <div class="idc-desig-wrap"><span class="idc-desig">{{ $c['subtitle'] }}</span></div>
            <div class="idc-rows">
                @foreach ($c['front_rows'] as $k => $v)
                    <div class="idc-row"><span class="k">{{ $k }}</span><span class="v">{{ $v ?: '—' }}</span></div>
                @endforeach
            </div>
        </div>

        <div class="idc-front-foot">
            <div><div class="lbl">Card No.</div><div class="val">{{ $c['card_number'] }}</div></div>
            <div class="col-r"><div class="lbl">Valid Till</div><div class="val">{{ $c['expiry_date'] }}</div></div>
        </div>
    </div>

    {{-- ============ BACK ============ --}}
    <div class="idc-card">
        <span class="idc-bubble b1"></span>
        <span class="idc-bubble b2"></span>
        <div class="idc-back">
            @if ($c['back_mode'] === 'transport')
                <div class="idc-pill">Transport Details</div>
                <div class="idc-sec-title">Route Information</div>
                @if (!empty($c['transport']))
                    <div class="idc-panel">
                        @foreach ($c['transport'] as $k => $v)
                            <div class="idc-row"><span class="k">{{ $k }}</span><span class="v">{{ $v ?: '—' }}</span></div>
                        @endforeach
                    </div>
                @else
                    <div class="idc-empty">No transport route assigned to this student.</div>
                @endif
            @else
                <div class="idc-pill">Terms &amp; Conditions</div>
                <ul class="idc-terms">
                    <li>This card is the property of the institution and is strictly non-transferable.</li>
                    <li>If found, please return to the school at the address provided.</li>
                    <li>The card must be carried and produced on demand within the premises.</li>
                    <li>Report loss or damage to the administration immediately.</li>
                </ul>
            @endif

            <div class="idc-contact">
                <div class="idc-sec-title">Contact</div>
                @if (!empty($c['school']['phone']))
                    <div class="ci"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg><span>{{ $c['school']['phone'] }}</span></div>
                @endif
                @if (!empty($c['school']['email']))
                    <div class="ci"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg><span>{{ $c['school']['email'] }}</span></div>
                @endif
                @if (!empty($c['school']['website']))
                    <div class="ci"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0a8.949 8.949 0 004.951-1.488A3.987 3.987 0 0013 16h-2a3.987 3.987 0 00-3.951 3.512A8.949 8.949 0 0012 21zM3.6 9h16.8M3.6 15h16.8"/></svg><span>{{ $c['school']['website'] }}</span></div>
                @endif
            </div>

            <div class="idc-meta">
                <div>
                    @if (!empty($c['qr_code']))
                        <div class="idc-qr"><img src="data:image/png;base64,{{ $c['qr_code'] }}" alt="QR"></div>
                        <div class="idc-qr-lbl">Scan to verify</div>
                    @endif
                </div>
                <div class="idc-sign">
                    <div class="line"></div>
                    <div class="role">Principal</div>
                    <span class="idc-status {{ $c['status'] === 'active' ? 'active' : 'inactive' }}">{{ $c['status'] }}</span>
                </div>
            </div>

            <div class="idc-barcode-wrap">
                <div class="idc-barcode"></div>
                <div class="idc-cardno">{{ $c['card_number'] }}</div>
            </div>
        </div>
    </div>
</div>
