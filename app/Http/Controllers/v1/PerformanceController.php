<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Admin\Exam;
use App\Models\Admin\ExamCopy;
use App\Models\Admin\ExamSubjectMark;
use App\Models\Student\Standard;
use App\Models\Student\Section;
use App\Models\Student\StudentDetail;
use App\Models\Student\Subject;
use App\Services\ResponseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PerformanceController extends Controller
{
    protected ResponseService $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    /**
     * Get all exam copies with filters
     */
    public function getAllExamCopies(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $query = ExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'studentDetails.user'
            ])
                ->where('organization_id', $organizationId);

            // Apply filters
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('studentDetails.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    })
                        ->orWhereHas('exam', function ($examQuery) use ($search) {
                            $examQuery->where('exam_name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('subject', function ($subjectQuery) use ($search) {
                            $subjectQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            }

            if ($request->has('exam_id') && $request->exam_id) {
                $query->where('exam_id', $request->exam_id);
            }

            if ($request->has('standard_id') && $request->standard_id) {
                $query->where('standard_id', $request->standard_id);
            }

            if ($request->has('section_id') && $request->section_id) {
                $query->where('section_id', $request->section_id);
            }

            if ($request->has('subject_id') && $request->subject_id) {
                $query->where('subject_id', $request->subject_id);
            }

            $perPage = $request->per_page ?? 10;
            $examCopies = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return $this->responseService->success(
                $examCopies,
                'Exam copies retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get single exam copy details
     */
    public function getExamCopy($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $examCopy = ExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'studentDetails.user',
                'examSubjectMarks.subject'
            ])
                ->where('organization_id', $organizationId)
                ->find($id);

            if (!$examCopy) {
                return $this->responseService->errorResponse(
                    'Exam copy not found',
                    404
                );
            }

            return $this->responseService->success(
                $examCopy,
                'Exam copy retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get student performance by filters
     */
    public function getStudentPerformanceByTeacher(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'student_detail_id' => 'required|exists:student_details,id',
            ]);

            $studentPerformance = ExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'examSubjectMarks.subject'
            ])
                ->where('organization_id', $organizationId)
                ->where('exam_id', $request->exam_id)
                ->where('student_detail_id', $request->student_detail_id)
                ->get();

            return $this->responseService->success(
                $studentPerformance,
                'Student performance retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get filters data for performance
     */
    public function getPerformanceFilters()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $exams = Exam::where('organization_id', $organizationId)
                ->where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'exam_name']);

            $standards = Standard::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->orderBy('order')
                ->get(['id', 'name']);

            $subjects = Subject::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->get(['id', 'name']);

            $filters = [
                'exams' => $exams,
                'standards' => $standards,
                'subjects' => $subjects
            ];

            return $this->responseService->success(
                $filters,
                'Performance filters retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get sections by standard
     */
    public function getSectionsByStandard($standardId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $sections = Section::where('standard_id', $standardId)
                ->where('is_active', true)
                ->get(['id', 'name']);

            return $this->responseService->success(
                $sections,
                'Sections retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get students by standard and section
     */
    public function getStudentsByClass(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $request->validate([
                'standard_id' => 'required|exists:standards,id',
                'section_id' => 'required|exists:sections,id',
            ]);

            $students = StudentDetail::with('user:id,name')
                ->where('standard_id', $request->standard_id)
                ->where('section_id', $request->section_id)
                ->get(['id', 'user_id']);

            return $this->responseService->success(
                $students,
                'Students retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Delete exam copy
     */
    public function deleteExamCopy($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $examCopy = ExamCopy::where('organization_id', $organizationId)
                ->find($id);

            if (!$examCopy) {
                return $this->responseService->errorResponse(
                    'Exam copy not found',
                    404
                );
            }

            // Delete associated subject marks first
            ExamSubjectMark::where('exam_copy_id', $id)->delete();

            $examCopy->delete();

            return $this->responseService->success(
                null,
                'Exam copy deleted successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Download exam copy PDF
     */
    public function downloadExamCopyPdf($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $examCopy = ExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'studentDetails.user',
                'examSubjectMarks.subject'
            ])
                ->where('organization_id', $organizationId)
                ->find($id);

            if (!$examCopy) {
                return $this->responseService->errorResponse(
                    'Exam copy not found',
                    404
                );
            }

            // PDF generation logic here
            // $pdf = Pdf::loadView('pdf.exam-copy', compact('examCopy'));
            // return $pdf->download('exam-copy-' . $id . '.pdf');

            // For now, return success message
            return $this->responseService->success(
                ['exam_copy' => $examCopy],
                'PDF download initiated successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Download multiple exam copies PDF
     */
    public function downloadMultipleExamCopiesPdf(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'standard_id' => 'nullable|exists:standards,id',
                'section_id' => 'nullable|exists:sections,id',
            ]);

            $examCopies = ExamCopy::with([
                'exam',
                'standard',
                'section',
                'studentDetails.user',
                'examSubjectMarks.subject'
            ])
                ->where('organization_id', $organizationId)
                ->where('exam_id', $request->exam_id)
                ->when($request->standard_id, function ($query) use ($request) {
                    $query->where('standard_id', $request->standard_id);
                })
                ->when($request->section_id, function ($query) use ($request) {
                    $query->where('section_id', $request->section_id);
                })
                ->get();

            if ($examCopies->isEmpty()) {
                return $this->responseService->errorResponse(
                    'No exam copies found for the selected criteria',
                    404
                );
            }

            // PDF generation logic for multiple records
            // $pdf = PDF::loadView('pdf.multiple-exam-copies', compact('examCopies'));
            // return $pdf->download('exam-results-' . $request->exam_id . '.pdf');

            // For now, return success message
            return $this->responseService->success(
                ['exam_copies' => $examCopies],
                'Multiple PDF download initiated successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Upload/Create multiple exam copies for multiple subjects
     */
    public function uploadExamCopy(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'student_detail_id' => 'required|exists:student_details,id',
                'standard_id' => 'required|exists:standards,id',
                'section_id' => 'required|exists:sections,id',
                'subject_marks' => 'required|array|min:1', // Contains all subjects with marks
                'subject_marks.*.subject_id' => 'required|exists:subjects,id',
                'subject_marks.*.marks_obtained' => 'required|numeric|min:0',
                'subject_marks.*.max_marks' => 'required|numeric|min:1',
                'subject_marks.*.grade' => 'required|string|max:10',
                'subject_marks.*.is_absent' => 'boolean',
                'subject_marks.*.is_recheck' => 'boolean',
                'subject_marks.*.remarks' => 'nullable|string',
                'subject_marks.*.breakup' => 'nullable|array',
            ]);

            DB::beginTransaction();

            $createdExamCopies = [];
            $errors = [];

            foreach ($request->subject_marks as $index => $subjectMark) {
                try {
                    // Check if exam copy already exists for this student, exam, and subject
                    $existingExamCopy = ExamCopy::where('organization_id', $organizationId)
                        ->where('exam_id', $request->exam_id)
                        ->where('student_detail_id', $request->student_detail_id)
                        ->where('subject_id', $subjectMark['subject_id'])
                        ->first();

                    if ($existingExamCopy) {
                        $errors[] = "Subject {$subjectMark['subject_id']}: Exam copy already exists for this student, exam, and subject";
                        continue;
                    }

                    // Calculate percentage
                    $percentage = ($subjectMark['marks_obtained'] / $subjectMark['max_marks']) * 100;

                    // Convert breakup array to JSON
                    $breakupJson = null;
                    if (isset($subjectMark['breakup']) && is_array($subjectMark['breakup'])) {
                        $breakupJson = json_encode($subjectMark['breakup']);
                    }

                    // Create exam copy for each subject
                    $examCopy = ExamCopy::create([
                        'organization_id' => $organizationId,
                        'user_id' => $user->id,
                        'student_detail_id' => $request->student_detail_id,
                        'standard_id' => $request->standard_id,
                        'section_id' => $request->section_id,
                        'subject_id' => $subjectMark['subject_id'], // Different for each copy
                        'exam_id' => $request->exam_id,
                        'marks_obtained' => $subjectMark['marks_obtained'], // Different for each copy
                        'max_marks' => $subjectMark['max_marks'], // Different for each copy
                        'percentage' => round($percentage, 2),
                        'grade' => $subjectMark['grade'], // Different for each copy
                        'remarks' => $subjectMark['remarks'] ?? null,
                        'is_absent' => $subjectMark['is_absent'] ?? false,
                        'is_recheck' => $subjectMark['is_recheck'] ?? false,
                        'breakup' => $breakupJson,
                    ]);

                    // Create subject marks if provided in the subject mark data
                    if (isset($subjectMark['subject_marks']) && is_array($subjectMark['subject_marks'])) {
                        foreach ($subjectMark['subject_marks'] as $nestedSubjectMark) {
                            $nestedPercentage = ($nestedSubjectMark['marks_obtained'] / $nestedSubjectMark['max_marks']) * 100;

                            ExamSubjectMark::create([
                                'organization_id' => $organizationId,
                                'exam_copy_id' => $examCopy->id,
                                'subject_id' => $nestedSubjectMark['subject_id'],
                                'marks_obtained' => $nestedSubjectMark['marks_obtained'],
                                'max_marks' => $nestedSubjectMark['max_marks'],
                                'percentage' => round($nestedPercentage, 2),
                                'grade' => $nestedSubjectMark['grade'],
                                'evaluation_type' => $nestedSubjectMark['evaluation_type'] ?? 'theory',
                                'academic_year' => $nestedSubjectMark['academic_year'] ?? date('Y'),
                                'counts_towards_yearly' => $nestedSubjectMark['counts_towards_yearly'] ?? true,
                                'weightage' => $nestedSubjectMark['weightage'] ?? 100,
                            ]);
                        }
                    }

                    $createdExamCopies[] = $examCopy;
                } catch (Exception $e) {
                    $errors[] = "Subject {$subjectMark['subject_id']}: " . $e->getMessage();
                }
            }

            DB::commit();

            // Load relationships for all created exam copies
            foreach ($createdExamCopies as $examCopy) {
                $examCopy->load([
                    'exam',
                    'standard',
                    'section',
                    'subject',
                    'studentDetails.user',
                    'examSubjectMarks.subject'
                ]);

                // Convert breakup back to array for response
                if ($examCopy->breakup) {
                    $examCopy->breakup = json_decode($examCopy->breakup, true);
                }
            }

            $response = [
                'created_count' => count($createdExamCopies),
                'error_count' => count($errors),
                'exam_copies' => $createdExamCopies,
                'errors' => $errors,
            ];

            return $this->responseService->success(
                $response,
                count($createdExamCopies) . ' exam copies created successfully'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Update exam copy with multiple subjects
     */
    public function updateExamCopy(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $examCopy = ExamCopy::where('organization_id', $organizationId)
                ->find($id);

            if (!$examCopy) {
                return $this->responseService->errorResponse(
                    'Exam copy not found',
                    404
                );
            }

            $request->validate([
                'marks_obtained' => 'required|numeric|min:0',
                'max_marks' => 'required|numeric|min:1',
                'grade' => 'required|string|max:10',
                'is_absent' => 'boolean',
                'is_recheck' => 'boolean',
                'remarks' => 'nullable|string',
                'breakup' => 'nullable|array',
                'subject_marks' => 'nullable|array',
                'subject_marks.*.id' => 'nullable|exists:exam_subject_marks,id',
                'subject_marks.*.subject_id' => 'required_with:subject_marks|exists:subjects,id',
                'subject_marks.*.marks_obtained' => 'required_with:subject_marks|numeric|min:0',
                'subject_marks.*.max_marks' => 'required_with:subject_marks|numeric|min:1',
                'subject_marks.*.grade' => 'required_with:subject_marks|string|max:10',
            ]);

            DB::beginTransaction();

            // Calculate percentage
            $percentage = ($request->marks_obtained / $request->max_marks) * 100;

            // Convert breakup array to JSON
            $breakupJson = $examCopy->breakup;
            if ($request->has('breakup')) {
                $breakupJson = is_array($request->breakup) ? json_encode($request->breakup) : $request->breakup;
            }

            // Update exam copy
            $examCopy->update([
                'marks_obtained' => $request->marks_obtained,
                'max_marks' => $request->max_marks,
                'percentage' => round($percentage, 2),
                'grade' => $request->grade,
                'remarks' => $request->remarks,
                'is_absent' => $request->is_absent ?? $examCopy->is_absent,
                'is_recheck' => $request->is_recheck ?? $examCopy->is_recheck,
                'breakup' => $breakupJson,
            ]);

            // Update or create subject marks
            if ($request->has('subject_marks') && is_array($request->subject_marks)) {
                $processedSubjects = [];

                foreach ($request->subject_marks as $subjectMarkData) {
                    // Check for duplicate subject marks within the same request
                    if (in_array($subjectMarkData['subject_id'], $processedSubjects)) {
                        continue;
                    }

                    $subjectPercentage = ($subjectMarkData['marks_obtained'] / $subjectMarkData['max_marks']) * 100;

                    if (isset($subjectMarkData['id'])) {
                        // Update existing subject mark
                        $subjectMark = ExamSubjectMark::where('exam_copy_id', $examCopy->id)
                            ->where('id', $subjectMarkData['id'])
                            ->first();

                        if ($subjectMark) {
                            $subjectMark->update([
                                'marks_obtained' => $subjectMarkData['marks_obtained'],
                                'max_marks' => $subjectMarkData['max_marks'],
                                'percentage' => round($subjectPercentage, 2),
                                'grade' => $subjectMarkData['grade'],
                                'evaluation_type' => $subjectMarkData['evaluation_type'] ?? $subjectMark->evaluation_type,
                                'weightage' => $subjectMarkData['weightage'] ?? $subjectMark->weightage,
                            ]);

                            $processedSubjects[] = $subjectMarkData['subject_id'];
                        }
                    } else {
                        // Check if subject mark already exists for this exam copy and subject
                        $existingSubjectMark = ExamSubjectMark::where('exam_copy_id', $examCopy->id)
                            ->where('subject_id', $subjectMarkData['subject_id'])
                            ->first();

                        if ($existingSubjectMark) {
                            // Update existing instead of creating new
                            $existingSubjectMark->update([
                                'marks_obtained' => $subjectMarkData['marks_obtained'],
                                'max_marks' => $subjectMarkData['max_marks'],
                                'percentage' => round($subjectPercentage, 2),
                                'grade' => $subjectMarkData['grade'],
                                'evaluation_type' => $subjectMarkData['evaluation_type'] ?? $existingSubjectMark->evaluation_type,
                                'weightage' => $subjectMarkData['weightage'] ?? $existingSubjectMark->weightage,
                            ]);
                        } else {
                            // Create new subject mark
                            ExamSubjectMark::create([
                                'organization_id' => $organizationId,
                                'exam_copy_id' => $examCopy->id,
                                'subject_id' => $subjectMarkData['subject_id'],
                                'marks_obtained' => $subjectMarkData['marks_obtained'],
                                'max_marks' => $subjectMarkData['max_marks'],
                                'percentage' => round($subjectPercentage, 2),
                                'grade' => $subjectMarkData['grade'],
                                'evaluation_type' => $subjectMarkData['evaluation_type'] ?? 'theory',
                                'academic_year' => $subjectMarkData['academic_year'] ?? date('Y'),
                                'counts_towards_yearly' => $subjectMarkData['counts_towards_yearly'] ?? true,
                                'weightage' => $subjectMarkData['weightage'] ?? 100,
                            ]);
                        }

                        $processedSubjects[] = $subjectMarkData['subject_id'];
                    }
                }
            }

            DB::commit();

            // Load relationships for response
            $examCopy->load([
                'exam',
                'standard',
                'section',
                'subject',
                'studentDetails.user',
                'examSubjectMarks.subject'
            ]);

            // Convert breakup back to array for response
            if ($examCopy->breakup) {
                $examCopy->breakup = json_decode($examCopy->breakup, true);
            }

            return $this->responseService->success(
                $examCopy,
                'Exam copy updated successfully'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Bulk upload exam copies
     */
    public function bulkUploadExamCopies(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            if (!$user) {
                DB::rollBack();
                return $this->responseService->errorResponse(
                    'Authentication required. Please log in.',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                DB::rollBack();
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }


            // Validate request data
            $validator = Validator::make($request->all(), [
                'exam_id' => 'required|integer|exists:exams,id',
                'standard_id' => 'required|integer|exists:standards,id',
                'section_id' => 'required|integer|exists:sections,id',
                'exam_copies' => 'required|array|min:1',
                'exam_copies.*.student_detail_id' => 'required|integer|exists:student_details,id',
                'exam_copies.*.subject_marks' => 'required|array|min:1',
                'exam_copies.*.subject_marks.*.subject_id' => 'required|integer|exists:subjects,id',
                'exam_copies.*.subject_marks.*.marks_obtained' => 'required|numeric|min:0',
                'exam_copies.*.subject_marks.*.max_marks' => 'required|numeric|min:1',
                'exam_copies.*.subject_marks.*.grade' => 'required|string|max:10',
                'exam_copies.*.subject_marks.*.is_absent' => 'sometimes|boolean',
                'exam_copies.*.subject_marks.*.remarks' => 'nullable|string|max:500',
            ], [
                'exam_id.required' => 'The exam ID field is required.',
                'standard_id.required' => 'The standard ID field is required.',
                'section_id.required' => 'The section ID field is required.',
                'exam_copies.required' => 'At least one exam copy is required.',
                'exam_copies.*.student_detail_id.required' => 'Student detail ID is required for each exam copy.',
                'exam_copies.*.subject_marks.required' => 'Subject marks are required for each student.',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return $this->responseService->errorResponse(
                    'Validation failed: ' . $validator->errors()->first(),
                    422
                );
            }

            $validatedData = $validator->validated();
            $uploadedCopies = [];
            $errors = [];
            $successCount = 0;

            foreach ($validatedData['exam_copies'] as $studentIndex => $studentData) {
                $studentDetailId = $studentData['student_detail_id'];

                foreach ($studentData['subject_marks'] as $subjectIndex => $subjectMark) {
                    try {
                        // Check if exam copy already exists
                        $existingCopy = ExamCopy::where('organization_id', $organizationId)
                            ->where('exam_id', $validatedData['exam_id'])
                            ->where('student_detail_id', $studentDetailId)
                            ->where('subject_id', $subjectMark['subject_id'])
                            ->first();

                        if ($existingCopy) {
                            $errors[] = [
                                'student_detail_id' => $studentDetailId,
                                'subject_id' => $subjectMark['subject_id'],
                                'message' => 'Exam copy already exists for this student and subject'
                            ];
                            continue;
                        }

                        // Calculate percentage
                        $percentage = 0;
                        if ($subjectMark['max_marks'] > 0) {
                            $percentage = ($subjectMark['marks_obtained'] / $subjectMark['max_marks']) * 100;
                        }

                        // Create exam copy
                        $examCopy = ExamCopy::create([
                            'organization_id' => $organizationId,
                            'user_id' => $user->id,
                            'student_detail_id' => $studentDetailId,
                            'standard_id' => $validatedData['standard_id'],
                            'section_id' => $validatedData['section_id'],
                            'subject_id' => $subjectMark['subject_id'],
                            'exam_id' => $validatedData['exam_id'],
                            'marks_obtained' => $subjectMark['marks_obtained'],
                            'max_marks' => $subjectMark['max_marks'],
                            'percentage' => round($percentage, 2),
                            'grade' => $subjectMark['grade'],
                            'remarks' => $subjectMark['remarks'] ?? null,
                            'is_absent' => $subjectMark['is_absent'] ?? false,
                            'is_recheck' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $uploadedCopies[] = $examCopy;
                        $successCount++;
                    } catch (Exception $e) {
                        $errors[] = [
                            'student_detail_id' => $studentDetailId,
                            'subject_id' => $subjectMark['subject_id'],
                            'message' => $e->getMessage()
                        ];
                    }
                }
            }

            DB::commit();

            $response = [
                'uploaded_count' => $successCount,
                'error_count' => count($errors),
                'total_processed' => $successCount + count($errors),
                'uploaded_copies' => $uploadedCopies,
                'errors' => $errors,
            ];

            return $this->responseService->success(
                $response,
                'Bulk upload completed successfully. ' . $successCount . ' exam copies uploaded, ' . count($errors) . ' errors.'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseService->errorResponse(
                'An error occurred during bulk upload: ' . $e->getMessage(),
                500
            );
        }
    }


    /**
     * Get subjects for teacher
     */
    public function getTeacherSubjects()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            // Get subjects assigned to teacher
            $subjects = Subject::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->get(['id', 'name', 'code']);

            return $this->responseService->success(
                $subjects,
                'Teacher subjects retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get teacher's assigned classes and sections
     */
    public function getTeacherClasses()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            // Get standards and sections assigned to teacher
            $standards = Standard::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->with(['sections' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->get(['id', 'name', 'code']);

            return $this->responseService->success(
                $standards,
                'Teacher classes retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Check if exam copy already exists
     */
    public function checkExamCopyExists(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'student_detail_id' => 'required|exists:student_details,id',
                'subject_id' => 'required|exists:subjects,id',
            ]);

            $existingCopy = ExamCopy::where('organization_id', $organizationId)
                ->where('exam_id', $request->exam_id)
                ->where('student_detail_id', $request->student_detail_id)
                ->where('subject_id', $request->subject_id)
                ->first();

            return $this->responseService->success(
                [
                    'exists' => !is_null($existingCopy),
                    'exam_copy' => $existingCopy
                ],
                'Exam copy check completed'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get exam copies by student
     */
    public function getExamCopiesByStudent($studentId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $examCopies = ExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'examSubjectMarks.subject'
            ])
                ->where('organization_id', $organizationId)
                ->where('student_detail_id', $studentId)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->responseService->success(
                $examCopies,
                'Student exam copies retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get exam copies by exam and class
     */
    public function getExamCopiesByExamAndClass(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $organizationId = $user->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'User does not belong to any organization',
                    403
                );
            }

            $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'standard_id' => 'required|exists:standards,id',
                'section_id' => 'nullable|exists:sections,id',
            ]);

            $query = ExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'studentDetails.user',
                'examSubjectMarks.subject'
            ])
                ->where('organization_id', $organizationId)
                ->where('exam_id', $request->exam_id)
                ->where('standard_id', $request->standard_id);

            if ($request->has('section_id') && $request->section_id) {
                $query->where('section_id', $request->section_id);
            }

            $examCopies = $query->orderBy('created_at', 'desc')->get();

            return $this->responseService->success(
                $examCopies,
                'Exam copies retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }


    /**
     * Student ke liye performance aur analytics data get kare
     */
    public function getStudentPerformance(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            // StudentDetail table se user_id ke through data fetch karna
            $studentDetail = StudentDetail::where('user_id', $user->id)->first();

            if (!$studentDetail) {
                return $this->responseService->errorResponse(
                    'Student profile not found',
                    404
                );
            }

            $organizationId = $studentDetail->organization_id;

            if (!$organizationId) {
                return $this->responseService->errorResponse(
                    'Student does not belong to any organization',
                    403
                );
            }

            // Optional filters
            $examId = $request->input('exam_id');
            $standardId = $request->input('standard_id');
            $subjectId = $request->input('subject_id');
            $limit = $request->input('limit', 10);

            $query = ExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'examSubjectMarks.subject'
            ])
                ->where('organization_id', $organizationId)
                ->where('student_detail_id', $studentDetail->id);

            // Apply filters agar available ho
            if ($examId) {
                $query->where('exam_id', $examId);
            }

            if ($standardId) {
                $query->where('standard_id', $standardId);
            }

            if ($subjectId) {
                $query->where('subject_id', $subjectId);
            }

            // Latest exams first
            $studentPerformances = $query->latest()
                ->limit($limit)
                ->get();

            // Additional analytics data
            $analyticsData = $this->getStudentAnalytics($studentDetail->id, $organizationId);

            $responseData = [
                'student_info' => [
                    'student_id' => $studentDetail->id,
                    'full_name' => $studentDetail->full_name,
                    'standard' => $studentDetail->standard?->name,
                    'section' => $studentDetail->section?->name,
                    'roll_no' => $studentDetail->roll_no,
                    'admission_no' => $studentDetail->admission_no
                ],
                'performances' => $studentPerformances,
                'analytics' => $analyticsData
            ];

            return $this->responseService->success(
                $responseData,
                'Student performance and analytics retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Student ke liye analytics data calculate kare
     */
    private function getStudentAnalytics($studentDetailId, $organizationId)
    {
        try {
            // Overall performance stats
            $totalExams = ExamCopy::where('student_detail_id', $studentDetailId)
                ->where('organization_id', $organizationId)
                ->count();

            // Latest 10 exams se data
            $recentExams = ExamCopy::with(['exam', 'subject'])
                ->where('student_detail_id', $studentDetailId)
                ->where('organization_id', $organizationId)
                ->latest()
                ->limit(10)
                ->get();

            // Average marks calculation - CORRECTED: marks_obtained use karna
            $totalMarks = 0;
            $totalMaxMarks = 0;
            $subjectWisePerformance = [];

            foreach ($recentExams as $examCopy) {
                if ($examCopy->marks_obtained !== null && $examCopy->max_marks !== null) {
                    $totalMarks += $examCopy->marks_obtained;
                    $totalMaxMarks += $examCopy->max_marks;

                    // Subject-wise data collect karna
                    $subjectName = $examCopy->subject?->name ?? 'Unknown';
                    if (!isset($subjectWisePerformance[$subjectName])) {
                        $subjectWisePerformance[$subjectName] = [
                            'total_marks' => 0,
                            'max_marks' => 0,
                            'count' => 0
                        ];
                    }
                    $subjectWisePerformance[$subjectName]['total_marks'] += $examCopy->marks_obtained;
                    $subjectWisePerformance[$subjectName]['max_marks'] += $examCopy->max_marks;
                    $subjectWisePerformance[$subjectName]['count']++;
                }
            }

            // Calculate percentages
            $overallPercentage = $totalMaxMarks > 0 ? round(($totalMarks / $totalMaxMarks) * 100, 2) : 0;

            // Subject-wise percentages calculate karna
            $subjectWisePercentages = [];
            foreach ($subjectWisePerformance as $subject => $data) {
                if ($data['max_marks'] > 0) {
                    $percentage = round(($data['total_marks'] / $data['max_marks']) * 100, 2);
                    $subjectWisePercentages[$subject] = [
                        'percentage' => $percentage,
                        'exam_count' => $data['count'],
                        'average_marks' => round($data['total_marks'] / $data['count'], 2)
                    ];
                }
            }

            // Performance trend (last 5 exams)
            $last5Exams = ExamCopy::where('student_detail_id', $studentDetailId)
                ->where('organization_id', $organizationId)
                ->whereNotNull('marks_obtained')
                ->whereNotNull('max_marks')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($exam) {
                    return [
                        'exam_name' => $exam->exam?->name,
                        'percentage' => $exam->max_marks > 0 ? round(($exam->marks_obtained / $exam->max_marks) * 100, 2) : 0,
                        'date' => $exam->created_at->format('Y-m-d'),
                        'marks_obtained' => $exam->marks_obtained,
                        'max_marks' => $exam->max_marks
                    ];
                });

            // Additional stats: Calculate grade distribution
            $gradeDistribution = ExamCopy::where('student_detail_id', $studentDetailId)
                ->where('organization_id', $organizationId)
                ->whereNotNull('grade')
                ->selectRaw('grade, COUNT(*) as count')
                ->groupBy('grade')
                ->get()
                ->pluck('count', 'grade')
                ->toArray();

            return [
                'total_exams_given' => $totalExams,
                'overall_percentage' => $overallPercentage,
                'subject_wise_performance' => $subjectWisePercentages,
                'performance_trend' => $last5Exams,
                'grade_distribution' => $gradeDistribution,
                'recent_exams_count' => $recentExams->count(),
                'stats_summary' => [
                    'total_marks_obtained' => $totalMarks,
                    'total_max_marks' => $totalMaxMarks,
                    'average_percentage' => $overallPercentage
                ],
                'stats_calculated_on' => now()->format('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            // Log the error for debugging
            return [
                'total_exams_given' => 0,
                'overall_percentage' => 0,
                'subject_wise_performance' => [],
                'performance_trend' => [],
                'grade_distribution' => [],
                'recent_exams_count' => 0,
                'error' => 'Analytics calculation failed: ' . $e->getMessage()
            ];
        }
    }
}
