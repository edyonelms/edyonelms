<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\StudentIdCard;
use App\Models\Admin\TeacherIdCard;
use Illuminate\Support\Facades\Auth;

class IdCardPrintController extends Controller
{
    /**
     * Printable single ID card (browser print / save as PDF).
     * GET /{organization}/id-card/{type}/{id}/print
     */
    public function print($organization, $type, $id)
    {
        $orgId = Auth::user()->organization_id;

        if ($type === 'student') {
            $card = StudentIdCard::with(['studentDetail.user', 'studentDetail.standard', 'studentDetail.section', 'organization'])
                ->where('organization_id', $orgId)->findOrFail($id);
            $person = $card->studentDetail;
            $data = [
                'name'        => $person->full_name ?? ($person->user->name ?? '—'),
                'identifier'  => 'Admission: ' . ($person->admission_no ?? '—'),
                'photo'       => $person->user->image ?? null,
                'rows'        => [
                    'Class'   => ($person->standard->name ?? '—') . ($person->section ? ' - ' . $person->section->name : ''),
                    'Roll No' => $person->roll_no ?? '—',
                    'Father'  => $person->father_name ?? '—',
                    'Phone'   => $person->phone ?? '—',
                ],
            ];
        } else {
            $card = TeacherIdCard::with(['teacherDetail.user', 'organization'])
                ->where('organization_id', $orgId)->findOrFail($id);
            $person = $card->teacherDetail;
            $data = [
                'name'        => $person->user->name ?? '—',
                'identifier'  => 'Employee: ' . ($person->employee_id ?? '—'),
                'photo'       => $person->user->image ?? null,
                'rows'        => [
                    'Designation'  => 'Teacher',
                    'Qualification' => $person->qualification ?? '—',
                    'Phone'        => $person->phone ?? '—',
                    'Joined'       => $person->date_of_joining ? \Carbon\Carbon::parse($person->date_of_joining)->format('d M Y') : '—',
                ],
            ];
        }

        return view('admin.id-cards.print', [
            'card'   => $card,
            'org'    => $card->organization,
            'data'   => $data,
            'type'   => $type,
        ]);
    }
}
