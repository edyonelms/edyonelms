<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student\AdmitCard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdmitCardController extends Controller
{
    public function view(Request $request, $organization, $id)
    {
        $admitCard = $this->getAdmitCard($id);
        return view('admin.admit-card-pdf', [
            'admitCard'    => $admitCard,
            'organization' => $admitCard->organization,
        ]);
    }

    public function download(Request $request, $organization, $id)
    {
        $admitCard = $this->getAdmitCard($id);

        $pdf = Pdf::loadView('admin.admit-card-pdf', [
            'admitCard'    => $admitCard,
            'organization' => $admitCard->organization,
        ])->setPaper('a4', 'portrait');

        $name = str_replace(' ', '_', $admitCard->student_name ?? 'admit_card');

        return $pdf->download("admit_card_{$name}.pdf");
    }

    public function printAll(Request $request, $organization)
    {
        $orgId = Auth::user()->organization_id;

        $admitCards = AdmitCard::with(['studentDetail.standard', 'studentDetail.section', 'organization'])
            ->where('organization_id', $orgId)
            ->when($request->exam_id, fn($q) => $q->where('exam_id', $request->exam_id))
            ->when($request->standard_id, fn($q) => $q->where('standard_id', $request->standard_id))
            ->when($request->section_id, fn($q) => $q->where('section_id', $request->section_id))
            ->get();

        $organization = Auth::user()->organization;

        return view('admin.admit-card-print-all', compact('admitCards', 'organization'));
    }

    private function getAdmitCard($id)
    {
        return AdmitCard::with([
            'studentDetail',
            'studentDetail.standard',
            'studentDetail.section',
            'organization',
        ])
            ->where('organization_id', Auth::user()->organization_id)
            ->findOrFail($id);
    }
}
