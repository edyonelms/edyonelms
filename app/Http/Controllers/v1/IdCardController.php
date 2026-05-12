<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Student\{AdmitCard, StudentDetail};
use App\Models\Teacher\TeacherDetail;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IdCardController extends Controller
{
    protected $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function getStudentIdCard()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            if ($user->role !== 'user') {
                return $this->responseService->errorResponse(
                    'Only students can access ID cards',
                    403
                );
            }

            // Get student details
            $studentDetail = StudentDetail::where('user_id', $user->id)
                ->where('organization_id', $user->organization_id)
                ->first();

            if (!$studentDetail) {
                return $this->responseService->errorResponse(
                    'Student details not found',
                    404
                );
            }

            // Get active ID card
            $idCard = $studentDetail->idCards()
                ->where('status', 'active')
                ->where('expiry_date', '>=', now())
                ->latest()
                ->first();

            if (!$idCard) {
                return $this->responseService->errorResponse(
                    'No active ID card found',
                    404
                );
            }

            // Prepare response data
            $idCardData = [
                'id' => $idCard->id,
                'card_number' => $idCard->card_number,
                'issue_date' => $idCard->issue_date->format('Y-m-d'),
                'expiry_date' => $idCard->expiry_date->format('Y-m-d'),
                'status' => $idCard->status,
                'qr_code_url' => $idCard->qr_code ? null : null,
                'days_remaining' => now()->diffInDays($idCard->expiry_date, false),
                'is_expired' => $idCard->expiry_date->isPast(),
            ];

            // Add student details
            $idCardData['student'] = [
                'full_name' => $studentDetail->full_name,
                'admission_no' => $studentDetail->admission_no,
                'roll_no' => $studentDetail->roll_no,
                'standard' => $studentDetail->standard->name ?? null,
                'section' => $studentDetail->section->name ?? null,
                'image_url' => $studentDetail->image ? Storage::url($studentDetail->image) : null,
                'dob' => $studentDetail->dob ? $studentDetail->dob->format('Y-m-d') : null,
                'gender' => $studentDetail->gender,
                'contact_number' => $studentDetail->phone,
                'email' => $studentDetail->email,
            ];

            // Add organization details
            if ($studentDetail->organization) {
                $idCardData['organization'] = [
                    'name' => $studentDetail->organization->name,
                    'logo_url' => $studentDetail->organization->logo ?? null,
                    'address' => $studentDetail->organization->address,
                ];
            }

            return $this->responseService->success(
                $idCardData,
                'ID card retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getTeacherIdCard()
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'teacher') {
                return $this->responseService->errorResponse(
                    'Only teachers can access ID cards',
                    403
                );
            }

            $teacherDetail = TeacherDetail::with([
                'user',
                'organization',
                'assignedClasses.standard',
                'assignedClasses.section'
            ])
                ->where('user_id', $user->id)
                ->where('organization_id', $user->organization_id)
                ->first();

            if (!$teacherDetail) {
                return $this->responseService->errorResponse(
                    'Teacher details not found',
                    404
                );
            }

            $idCard = $teacherDetail->idCards()
                ->where('status', 'active')
                ->where('expiry_date', '>=', now())
                ->latest()
                ->first();

            if (!$idCard) {
                return $this->responseService->errorResponse(
                    'No active ID card found',
                    404
                );
            }

            // Get assigned classes
            $assignedClasses = $teacherDetail->assignedClasses->map(function ($class) {
                return [
                    'standard' => $class->standard->name ?? null,
                    'section' => $class->section->name ?? null,
                    'display' => ($class->standard->name ?? '') . ' ' . ($class->section->name ?? '')
                ];
            })->values();

            $idCardData = [
                'id' => $idCard->id,
                'card_number' => $idCard->card_number,
                'issue_date' => $idCard->issue_date->format('Y-m-d'),
                'expiry_date' => $idCard->expiry_date->format('Y-m-d'),
                'status' => $idCard->status,
                'qr_code' => $idCard->qr_code ? 'data:image/png;base64,' . $idCard->qr_code : null,
                'days_remaining' => now()->diffInDays($idCard->expiry_date, false),
                'is_expired' => $idCard->expiry_date->isPast(),
            ];

            $idCardData['teacher'] = [
                'id' => $teacherDetail->id,
                'full_name' => $teacherDetail->user->name ?? 'N/A',
                'employee_id' => $teacherDetail->employee_id,
                'phone' => $teacherDetail->phone,
                'email' => $teacherDetail->user->email ?? null,
                'qualification' => $teacherDetail->qualification ?? null,
                'date_of_joining' => $teacherDetail->date_of_joining ? \Carbon\Carbon::parse($teacherDetail->date_of_joining)->format('Y-m-d') : null,
                'address' => $teacherDetail->address,
                'city' => $teacherDetail->city,
                'state' => $teacherDetail->state,
                'pincode' => $teacherDetail->pincode,
                'image_url' => $teacherDetail->user->image ?? null,
                'assigned_classes' => $assignedClasses,
            ];

            $idCardData['organization'] = [
                'id' => $teacherDetail->organization->id,
                'name' => $teacherDetail->organization->name,
                'logo_url' => $teacherDetail->organization->logo ?? null,
                'address' => $teacherDetail->organization->address,
                'phone' => $teacherDetail->organization->phone ?? null,
            ];

            return $this->responseService->success(
                $idCardData,
                'ID card retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get active admit card for authenticated student
     */
    public function getStudentAdmitCard()
    {
        try {
            $user = Auth::user();

            // Check if user is a student
            if ($user->role !== 'user') {
                return $this->responseService->errorResponse(
                    'Only students can access admit cards',
                    403
                );
            }

            // Get student details
            $studentDetail = StudentDetail::with([
                'user',
                'organization',
                'standard',
                'section',
            ])
                ->where('user_id', $user->id)
                ->where('organization_id', $user->organization_id)
                ->first();

            if (!$studentDetail) {
                return $this->responseService->errorResponse(
                    'Student details not found',
                    404
                );
            }

            // Get active admit card
            $admitCard = AdmitCard::with(['exam'])
                ->where('student_detail_id', $studentDetail->id)
                ->where('status', 'active')
                ->whereHas('exam', function ($query) {
                    $query->where('is_published', true)
                        ->where('end_date', '>=', now()->format('Y-m-d'));
                })
                ->latest()
                ->first();

            if (!$admitCard) {
                return $this->responseService->errorResponse(
                    'No active admit card found',
                    404
                );
            }

            // Get current date and time for checking upcoming exams
            $now = Carbon::now();
            $today = $now->format('Y-m-d');
            $currentTime = $now->format('H:i:s');

            // Process subjects with status
            $subjects = collect($admitCard->subjects ?? [])->map(function ($subject) use ($today, $now, $admitCard) {
                $subjectDate = $subject['exam_date'] ?? null;
                $subjectTime = $subject['exam_time'] ?? null;

                $status = 'upcoming';
                $is_today = false;
                $is_completed = false;
                $is_ongoing = false;

                if ($subjectDate) {
                    $examDateTime = Carbon::createFromFormat('Y-m-d H:i', $subjectDate . ' ' . $subjectTime);
                    $duration = $this->parseDuration($subject['exam_duration'] ?? '3 Hours');
                    $endDateTime = $examDateTime->copy()->addHours($duration);

                    $is_today = $subjectDate === $today;
                    $is_completed = $endDateTime->isPast();
                    $is_ongoing = $now->between($examDateTime, $endDateTime);

                    if ($is_completed) {
                        $status = 'completed';
                    } elseif ($is_ongoing) {
                        $status = 'ongoing';
                    } elseif ($is_today) {
                        $status = 'today';
                    }
                }

                return [
                    'subject_id' => $subject['subject_id'] ?? null,
                    'subject_name' => $subject['subject_name'] ?? 'General',
                    'subject_code' => $this->getSubjectCode($subject['subject_id'] ?? null),
                    'exam_date' => $subjectDate,
                    'exam_date_formatted' => $subjectDate ? Carbon::parse($subjectDate)->format('d M, Y') : null,
                    'exam_time' => $subjectTime,
                    'exam_time_formatted' => $subjectTime ? Carbon::parse($subjectTime)->format('h:i A') : null,
                    'exam_duration' => $subject['exam_duration'] ?? '3 Hours',
                    'exam_duration_minutes' => $duration * 60,
                    'status' => $status,
                    'is_today' => $is_today,
                    'is_completed' => $is_completed,
                    'is_ongoing' => $is_ongoing,
                    'reporting_time' => $admitCard->reporting_time ? Carbon::parse($admitCard->reporting_time)->format('h:i A') : null,
                ];
            })->values();

            // Check if any exam is ongoing or upcoming today
            $has_ongoing_exam = $subjects->contains('is_ongoing', true);
            $has_today_exam = $subjects->contains('is_today', true);
            $has_upcoming_exam = $subjects->contains('status', 'upcoming');
            $all_exams_completed = $subjects->every('is_completed', true);

            // Calculate overall exam status
            $overall_status = 'upcoming';
            if ($has_ongoing_exam) {
                $overall_status = 'ongoing';
            } elseif ($has_today_exam) {
                $overall_status = 'today';
            } elseif ($all_exams_completed) {
                $overall_status = 'completed';
            }

            // Prepare admit card data
            $admitCardData = [
                'id' => $admitCard->id,
                'admit_card_number' => $admitCard->admit_card_number,
                'issue_date' => $admitCard->issue_date->format('Y-m-d'),
                'issue_date_formatted' => $admitCard->issue_date->format('d M, Y'),
                'status' => $admitCard->status,
                'qr_code' => $admitCard->qr_code ?? null,
                'overall_exam_status' => $overall_status,
                'has_ongoing_exam' => $has_ongoing_exam,
                'has_today_exam' => $has_today_exam,
                'has_upcoming_exam' => $has_upcoming_exam,
                'all_exams_completed' => $all_exams_completed,
            ];

            // Exam details
            $admitCardData['exam'] = [
                'id' => $admitCard->exam->id,
                'name' => $admitCard->exam->exam_name,
                'academic_year' => $admitCard->exam->academic_year,
                'description' => $admitCard->exam->description,
                'start_date' => $admitCard->exam->start_date ? $admitCard->exam->start_date->format('Y-m-d') : null,
                'end_date' => $admitCard->exam->end_date ? $admitCard->exam->end_date->format('Y-m-d') : null,
                'total_subjects' => $subjects->count(),
                'subjects' => $subjects,
            ];

            // Exam center details
            $admitCardData['exam_center'] = [
                'name' => $admitCard->exam_center,
                'address' => $admitCard->exam_center_address,
                'reporting_time' => $admitCard->reporting_time ? Carbon::parse($admitCard->reporting_time)->format('h:i A') : null,
                'seat_number' => $admitCard->seat_number,
                'room_number' => $admitCard->room_number,
                'instructions' => $admitCard->instructions,
            ];

            // Student details
            $admitCardData['student'] = [
                'id' => $studentDetail->id,
                'full_name' => $studentDetail->full_name,
                'admission_no' => $studentDetail->admission_no,
                'roll_no' => $studentDetail->roll_no,
                'roll_number' => $admitCard->roll_number,
                'exam_roll_number' => $admitCard->exam_roll_number,
                'phone' => $studentDetail->phone,
                'email' => $studentDetail->user->email ?? null,
                'date_of_birth' => $studentDetail->date_of_birth ? Carbon::parse($studentDetail->date_of_birth)->format('Y-m-d') : null,
                'gender' => $studentDetail->gender,
                'blood_group' => $studentDetail->blood_group,
                'address' => $studentDetail->address,
                'city' => $studentDetail->city,
                'state' => $studentDetail->state,
                'pincode' => $studentDetail->pincode,
                'image_url' => $studentDetail->user->image ?? null,
            ];

            // Academic details
            $admitCardData['academic'] = [
                'class' => [
                    'id' => $studentDetail->standard_id,
                    'name' => $studentDetail->standard->name ?? null,
                    'section' => $studentDetail->section->name ?? null,
                    'display' => ($studentDetail->standard->name ?? '') . ' - ' . ($studentDetail->section->name ?? ''),
                ],
                'father_name' => $studentDetail->father_name,
                'mother_name' => $studentDetail->mother_name,
                'guardian_name' => $studentDetail->guardian?->name ?? null,
                'guardian_phone' => $studentDetail->guardian?->phone ?? null,
            ];

            // Organization details
            $admitCardData['organization'] = [
                'id' => $studentDetail->organization->id,
                'name' => $studentDetail->organization->name,
                'logo_url' => $studentDetail->organization->logo ?? null,
                'address' => $studentDetail->organization->address,
                'phone' => $studentDetail->organization->phone ?? null,
                'email' => $studentDetail->organization->email ?? null,
            ];

            // Allowed and prohibited items
            $admitCardData['exam_rules'] = [
                'allowed_items' => $admitCard->allowed_items ?? [],
                'prohibited_items' => $admitCard->prohibited_items ?? [],
                'general_instructions' => $admitCard->instructions,
            ];

            // Verification info
            $admitCardData['verification'] = [
                'verification_url' => route('admit-card.verify', $admitCard->admit_card_number),
                'last_updated' => $admitCard->updated_at->format('Y-m-d H:i:s'),
                'valid_until' => $admitCard->exam->end_date ? $admitCard->exam->end_date->format('Y-m-d') : null,
            ];

            return $this->responseService->success(
                $admitCardData,
                'Admit card retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get subject code from subject_id
     */
    private function getSubjectCode($subjectId)
    {
        if (!$subjectId) {
            return null;
        }

        $subject = \App\Models\Student\Subject::find($subjectId);
        return $subject->code ?? null;
    }

    /**
     * Parse duration string to hours
     */
    private function parseDuration($duration)
    {
        if (preg_match('/(\d+)\s*hour/i', $duration, $matches)) {
            return (int) $matches[1];
        } elseif (preg_match('/(\d+)\s*hr/i', $duration, $matches)) {
            return (int) $matches[1];
        } elseif (preg_match('/(\d+)/', $duration, $matches)) {
            return (int) $matches[1];
        }

        return 3;
    }

    /**
     * Verify admit card by number (public API)
     */
    public function verifyAdmitCard($admitCardNumber)
    {
        try {
            $admitCard = AdmitCard::with([
                'studentDetail.user',
                'studentDetail.standard',
                'studentDetail.section',
                'exam',
                'organization'
            ])
                ->where('admit_card_number', $admitCardNumber)
                ->where('status', 'active')
                ->first();

            if (!$admitCard) {
                return $this->responseService->errorResponse(
                    'Admit card not found or invalid',
                    404
                );
            }

            $verificationData = [
                'is_valid' => true,
                'verification_date' => now()->format('Y-m-d H:i:s'),
                'admit_card' => [
                    'number' => $admitCard->admit_card_number,
                    'issue_date' => $admitCard->issue_date->format('Y-m-d'),
                    'status' => $admitCard->status,
                ],
                'student' => [
                    'full_name' => $admitCard->studentDetail->full_name,
                    'admission_no' => $admitCard->studentDetail->admission_no,
                    'roll_number' => $admitCard->roll_number,
                    'exam_roll_number' => $admitCard->exam_roll_number,
                    'class' => $admitCard->studentDetail->standard->name ?? null,
                    'section' => $admitCard->studentDetail->section->name ?? null,
                ],
                'exam' => [
                    'name' => $admitCard->exam->exam_name,
                    'academic_year' => $admitCard->exam->academic_year,
                    'center' => $admitCard->exam_center,
                ],
                'organization' => [
                    'name' => $admitCard->organization->name,
                ]
            ];

            return $this->responseService->success(
                '☑️',
                'Admit card verified successfully'
            );
        } catch (\Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }
}
