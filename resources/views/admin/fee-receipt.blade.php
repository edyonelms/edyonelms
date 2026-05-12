<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $payment->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; background: #fff; }
        .receipt { max-width: 600px; margin: 30px auto; border: 2px solid #6b21a8; border-radius: 8px; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #6b21a8; padding-bottom: 16px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #6b21a8; }
        .header p { font-size: 12px; color: #777; margin-top: 4px; }
        .receipt-no { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 12px; color: #555; }
        .receipt-no strong { color: #333; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #6b21a8; letter-spacing: 0.05em; margin-bottom: 8px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 24px; margin-bottom: 20px; }
        .info-item { display: flex; flex-direction: column; }
        .info-item .label { font-size: 11px; color: #888; }
        .info-item .value { font-size: 13px; font-weight: 600; color: #111; margin-top: 2px; }
        .amount-box { background: linear-gradient(135deg, #f5f3ff, #ede9fe); border: 1px solid #c4b5fd; border-radius: 8px; padding: 16px; text-align: center; margin: 20px 0; }
        .amount-box .label { font-size: 11px; color: #7c3aed; text-transform: uppercase; letter-spacing: 0.05em; }
        .amount-box .amount { font-size: 32px; font-weight: bold; color: #4c1d95; margin-top: 4px; }
        .footer { border-top: 1px dashed #ccc; padding-top: 14px; margin-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; font-size: 12px; color: #777; }
        .footer .signature { text-align: right; }
        .footer .signature p { border-top: 1px solid #333; padding-top: 4px; margin-top: 30px; font-size: 11px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-academic { background: #dbeafe; color: #1d4ed8; }
        .badge-transport { background: #d1fae5; color: #065f46; }
        @media print {
            body { margin: 0; }
            .receipt { border: 1px solid #999; max-width: 100%; margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="receipt">
    <div class="header">
        <h1>Fee Receipt</h1>
        <p>Official Payment Acknowledgement</p>
    </div>

    <div class="receipt-no">
        <span>Receipt No: <strong>{{ $payment->receipt_number }}</strong></span>
        <span>Date: <strong>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</strong></span>
    </div>

    <div class="section-title">Student Details</div>
    <div class="info-grid">
        <div class="info-item">
            <span class="label">Student Name</span>
            <span class="value">{{ $payment->studentDetail->user->name ?? '-' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Admission No.</span>
            <span class="value">{{ $payment->studentDetail->admission_no ?? '-' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Class</span>
            <span class="value">{{ $payment->standard->name ?? '-' }}</span>
        </div>
        <div class="info-item">
            <span class="label">Section</span>
            <span class="value">{{ $payment->section->name ?? '-' }}</span>
        </div>
        @if ($payment->studentDetail->father_name)
        <div class="info-item">
            <span class="label">Father's Name</span>
            <span class="value">{{ $payment->studentDetail->father_name }}</span>
        </div>
        @endif
    </div>

    <div class="section-title">Payment Details</div>
    <div class="info-grid">
        <div class="info-item">
            <span class="label">Fee Type</span>
            <span class="value">
                <span class="badge {{ $payment->fee_type === 'academic' ? 'badge-academic' : 'badge-transport' }}">
                    {{ ucfirst($payment->fee_type) }}
                </span>
            </span>
        </div>
        <div class="info-item">
            <span class="label">Payment Mode</span>
            <span class="value">{{ ucfirst(str_replace('_', ' ', $payment->payment_mode)) }}</span>
        </div>
        <div class="info-item">
            <span class="label">Payment Date</span>
            <span class="value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</span>
        </div>
        <div class="info-item">
            <span class="label">Collected By</span>
            <span class="value">{{ $payment->submitted_by }}</span>
        </div>
        @if ($payment->remark)
        <div class="info-item" style="grid-column: span 2;">
            <span class="label">Remark</span>
            <span class="value">{{ $payment->remark }}</span>
        </div>
        @endif
    </div>

    <div class="amount-box">
        <div class="label">Amount Paid</div>
        <div class="amount">₹{{ number_format($payment->amount, 2) }}</div>
    </div>

    <div class="footer">
        <div>
            <p>This is a computer generated receipt.</p>
            <p>No signature required.</p>
        </div>
        <div class="signature">
            <p>Authorised Signatory</p>
        </div>
    </div>
</div>

<div class="no-print" style="text-align:center; margin: 20px;">
    <button onclick="window.print()" style="padding: 10px 24px; background: #7c3aed; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
        🖨️ Print Receipt
    </button>
</div>

<script>
    // Auto-print on load (optional - uncomment if desired)
    // window.onload = function() { window.print(); }
</script>
</body>
</html>
