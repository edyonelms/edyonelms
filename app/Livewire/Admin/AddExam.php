<?php

namespace App\Livewire\Admin;

use App\Models\Admin\Exam;
use App\Models\Admin\ExamSyllabusChapter;
use App\Models\Student\Chapter;
use App\Models\Student\Standard;
use App\Models\Student\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class AddExam extends Component
{
    use WireUiActions, WithPagination;

    // ─── Tabs ────────────────────────────────────────────────────────────────
    public string $activeTab = 'exams'; // 'exams' | 'syllabus'

    // ─── Exam form ──────────────────────────────────────────────────────────
    public $examName       = '';
    public $academicYear   = '';
    public $startDate      = '';
    public $endDate        = '';
    public $description    = '';
    public $isPublished    = false;
    public $examType       = '';
    public $totalMarks     = '';
    public $passingMarks   = '';
    public $usesGradingSystem = false;
    public $editId         = null;

    // ─── Modal states ───────────────────────────────────────────────────────
    public $open               = false;
    public $showViewModal      = false;
    public $viewModalTitle     = '';
    public $viewData           = [];

    // Custom delete overlay (replaces broken WireUI dialog)
    public bool $showDeleteConfirm = false;
    public $deleteTargetId         = null;
    public string $deleteTargetType = 'exam'; // 'exam' | 'syllabus'

    // ─── Exam filters ───────────────────────────────────────────────────────
    public $search             = '';
    public $perPage            = 10;
    public $filterAcademicYear = '';
    public $filterExamType     = '';
    public $filterStatus       = '';

    // ─── Syllabus filters (Class → Subject → Exam) ──────────────────────────
    public $syllabusFilterStandard = '';
    public $syllabusFilterSubject  = '';
    public $syllabusFilterExam     = '';

    // ─── Syllabus modal ─────────────────────────────────────────────────────
    public bool $openSyllabusModal = false;
    public $sylModalExamId         = '';
    public $sylModalStandardId     = '';
    public $sylModalSubjectId      = '';
    public array $sylModalChapterIds  = []; // selected chapter ids
    public array $sylModalSubjects = [];    // subjects for selected class
    public array $sylModalChapters = [];    // chapters for selected class+subject

    // ─── Data options ───────────────────────────────────────────────────────
    public $academicYearOptions = [];
    public $examTypes = [
        'quarterly'  => 'Quarterly',
        'half_yearly' => 'Half Yearly',
        'annual'     => 'Annual',
        'unit_test'  => 'Unit Test',
        'pre_board'  => 'Pre Board',
    ];

    public $allStandards = [];
    public $allSubjects  = [];
    public $allExams     = [];

    // ─── Statistics ─────────────────────────────────────────────────────────
    public $totalExams       = 0;
    public $publishedExams   = 0;
    public $upcomingExams    = 0;
    public $activeExams      = 0;
    public $totalSyllabusRows = 0;

    protected $queryString = [
        'activeTab'          => ['except' => 'exams'],
        'search'             => ['except' => ''],
        'filterAcademicYear' => ['except' => ''],
        'filterExamType'     => ['except' => ''],
        'filterStatus'       => ['except' => ''],
        'perPage'            => ['except' => 10],
    ];

    public function mount(): void
    {
        $this->loadAcademicYearOptions();
        $this->loadLookups();
        $this->loadStatistics();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // ─── Lookups ────────────────────────────────────────────────────────────

    public function loadAcademicYearOptions(): void
    {
        $currentYear = date('Y');
        $nextYear    = $currentYear + 1;

        $this->academicYearOptions = [
            $currentYear . '-' . $nextYear,
            $nextYear . '-' . ($nextYear + 1),
        ];
        $this->academicYear = $currentYear . '-' . $nextYear;
    }

    public function loadLookups(): void
    {
        $orgId = Auth::user()->organization_id;

        $this->allStandards = Standard::where('organization_id', $orgId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get(['id', 'name', 'code'])
            ->toArray();

        $this->allSubjects = Subject::where('organization_id', $orgId)
            ->where('is_active', true)
            ->get(['id', 'name'])
            ->toArray();

        $this->allExams = Exam::where('organization_id', $orgId)
            ->orderBy('start_date', 'desc')
            ->get(['id', 'exam_name', 'academic_year'])
            ->toArray();
    }

    public function loadStatistics(): void
    {
        $orgId = Auth::user()->organization_id;

        $this->totalExams     = Exam::where('organization_id', $orgId)->count();
        $this->publishedExams = Exam::where('organization_id', $orgId)->where('is_published', true)->count();
        $this->upcomingExams  = Exam::where('organization_id', $orgId)->where('start_date', '>', now())->count();
        $this->activeExams    = Exam::where('organization_id', $orgId)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        $this->totalSyllabusRows = ExamSyllabusChapter::where('organization_id', $orgId)->count();
    }

    // ─── Exam filter watchers ───────────────────────────────────────────────

    public function updatedSearch(): void             { $this->resetPage(); $this->loadStatistics(); }
    public function updatedFilterAcademicYear(): void { $this->resetPage(); $this->loadStatistics(); }
    public function updatedFilterExamType(): void     { $this->resetPage(); $this->loadStatistics(); }
    public function updatedFilterStatus(): void       { $this->resetPage(); $this->loadStatistics(); }
    public function updatedPerPage(): void            { $this->resetPage(); }

    public function clearExamFilters(): void
    {
        $this->reset(['search', 'filterAcademicYear', 'filterExamType', 'filterStatus']);
        $this->resetPage();
    }

    // ─── Syllabus filter cascading ──────────────────────────────────────────

    public function updatedSyllabusFilterStandard($value): void
    {
        $this->syllabusFilterSubject = '';
        $this->syllabusFilterExam    = '';
    }

    public function updatedSyllabusFilterSubject($value): void
    {
        $this->syllabusFilterExam = '';
    }

    public function clearSyllabusFilters(): void
    {
        $this->reset(['syllabusFilterStandard', 'syllabusFilterSubject', 'syllabusFilterExam']);
    }

    // ─── Exam: Add / Edit ───────────────────────────────────────────────────

    public function onAddExam(): void
    {
        $this->resetExamForm();
        $this->open   = true;
        $this->editId = null;
    }

    public function onSave(): void
    {
        $rules = [
            'examName'      => 'required|string|max:255',
            'academicYear'  => 'required|string|max:9',
            'startDate'     => 'required|date',
            'endDate'       => 'required|date|after_or_equal:startDate',
            'examType'      => 'required|string',
            'usesGradingSystem' => 'boolean',
        ];

        if (!$this->usesGradingSystem) {
            $rules['totalMarks']   = 'required|integer|min:1';
            $rules['passingMarks'] = 'required|integer|min:1|lt:totalMarks';
        }

        $this->validate($rules);

        try {
            $examData = [
                'organization_id'      => Auth::user()->organization_id,
                'exam_name'            => $this->examName,
                'academic_year'        => $this->academicYear,
                'start_date'           => $this->startDate,
                'end_date'             => $this->endDate,
                'description'          => $this->description,
                'is_published'         => $this->isPublished,
                'exam_type'            => $this->examType,
                'total_marks'          => $this->usesGradingSystem ? null : $this->totalMarks,
                'passing_marks'        => $this->usesGradingSystem ? null : $this->passingMarks,
                'created_by'           => Auth::id(),
                'updated_by'           => Auth::id(),
            ];

            if (DB::getSchemaBuilder()->hasColumn('exams', 'uses_grading_system')) {
                $examData['uses_grading_system'] = $this->usesGradingSystem;
            }

            if ($this->editId) {
                Exam::findOrFail($this->editId)->update($examData);
                $this->notification()->success('Exam updated successfully!');
            } else {
                Exam::create($examData);
                $this->notification()->success('Exam created successfully!');
            }

            $this->resetExamForm();
            $this->loadLookups();
            $this->loadStatistics();
        } catch (\Exception $e) {
            $this->notification()->error('Error saving exam', $e->getMessage());
        }
    }

    public function onEditExam($id): void
    {
        $exam = Exam::findOrFail($id);

        $this->editId          = $exam->id;
        $this->examName        = $exam->exam_name;
        $this->academicYear    = $exam->academic_year;
        $this->startDate       = $exam->start_date ? \Carbon\Carbon::parse($exam->start_date)->format('Y-m-d') : '';
        $this->endDate         = $exam->end_date ? \Carbon\Carbon::parse($exam->end_date)->format('Y-m-d') : '';
        $this->description     = $exam->description;
        $this->isPublished     = (bool) $exam->is_published;
        $this->examType        = $exam->exam_type;
        $this->totalMarks      = $exam->total_marks;
        $this->passingMarks    = $exam->passing_marks;
        $this->usesGradingSystem = (bool) ($exam->uses_grading_system ?? false);
        $this->open            = true;
    }

    public function resetExamForm(): void
    {
        $this->reset([
            'examName', 'academicYear', 'startDate', 'endDate', 'description',
            'isPublished', 'examType', 'totalMarks', 'passingMarks',
            'usesGradingSystem', 'editId',
        ]);
        $this->loadAcademicYearOptions();
        $this->open = false;
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->resetExamForm();
    }

    public function onViewExam($id): void
    {
        $exam = Exam::with(['createdBy', 'updatedBy'])->findOrFail($id);

        $this->viewModalTitle = 'Exam Details — ' . $exam->exam_name;
        $this->viewData = [
            'exam'    => $exam,
            'details' => [
                'Exam Name'     => $exam->exam_name,
                'Academic Year' => $exam->academic_year,
                'Start Date'    => $exam->start_date->format('d M Y'),
                'End Date'      => $exam->end_date->format('d M Y'),
                'Exam Type'     => $this->examTypes[$exam->exam_type] ?? $exam->exam_type,
                'Total Marks'   => ($exam->uses_grading_system ?? false) ? 'N/A (Grading)' : $exam->total_marks,
                'Passing Marks' => ($exam->uses_grading_system ?? false) ? 'N/A (Grading)' : $exam->passing_marks,
                'Status'        => $exam->is_published ? 'Published' : 'Draft',
                'Created By'    => $exam->createdBy->name ?? 'N/A',
                'Created'       => $exam->created_at->format('d M Y, g:i A'),
                'Last Updated'  => $exam->updated_at->format('d M Y, g:i A'),
            ],
        ];
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewData      = [];
    }

    public function onTogglePublish($id): void
    {
        try {
            $exam = Exam::findOrFail($id);
            $exam->update([
                'is_published' => !$exam->is_published,
                'updated_by'   => Auth::id(),
            ]);
            $this->notification()->success(
                'Exam ' . ($exam->is_published ? 'published' : 'unpublished') . ' successfully!'
            );
            $this->loadStatistics();
        } catch (\Exception $e) {
            $this->notification()->error('Error updating exam status', $e->getMessage());
        }
    }

    // ─── Delete (custom overlay) ────────────────────────────────────────────

    public function onDeleteExam($id): void
    {
        $this->deleteTargetId   = $id;
        $this->deleteTargetType = 'exam';
        $this->showDeleteConfirm = true;
    }

    public function onDeleteSyllabusGroup($examId, $standardId, $subjectId): void
    {
        $this->deleteTargetId    = ['exam_id' => $examId, 'standard_id' => $standardId, 'subject_id' => $subjectId];
        $this->deleteTargetType  = 'syllabus';
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteTargetId    = null;
    }

    public function confirmDelete(): void
    {
        try {
            if ($this->deleteTargetType === 'exam') {
                $exam = Exam::find($this->deleteTargetId);
                if ($exam) {
                    // Cascade delete syllabus rows for this exam
                    ExamSyllabusChapter::where('exam_id', $exam->id)->delete();
                    $exam->delete();
                    $this->notification()->success('Exam deleted successfully!');
                }
            } elseif ($this->deleteTargetType === 'syllabus' && is_array($this->deleteTargetId)) {
                $t = $this->deleteTargetId;
                ExamSyllabusChapter::where('exam_id', $t['exam_id'])
                    ->where('standard_id', $t['standard_id'])
                    ->where('subject_id', $t['subject_id'])
                    ->where('organization_id', Auth::user()->organization_id)
                    ->delete();
                $this->notification()->success('Syllabus removed successfully!');
            }
            $this->loadStatistics();
        } catch (\Exception $e) {
            $this->notification()->error('Error deleting', $e->getMessage());
        }

        $this->showDeleteConfirm = false;
        $this->deleteTargetId    = null;
    }

    // ─── Syllabus modal ─────────────────────────────────────────────────────

    public function onAddSyllabus(): void
    {
        $this->reset(['sylModalExamId', 'sylModalStandardId', 'sylModalSubjectId', 'sylModalChapterIds', 'sylModalSubjects', 'sylModalChapters']);
        $this->openSyllabusModal = true;
    }

    public function closeSyllabusModal(): void
    {
        $this->openSyllabusModal = false;
        $this->reset(['sylModalExamId', 'sylModalStandardId', 'sylModalSubjectId', 'sylModalChapterIds', 'sylModalSubjects', 'sylModalChapters']);
    }

    public function updatedSylModalStandardId($value): void
    {
        $this->sylModalSubjectId   = '';
        $this->sylModalChapterIds  = [];
        $this->sylModalChapters    = [];

        if (!$value) {
            $this->sylModalSubjects = [];
            return;
        }

        // Subjects linked to this standard
        $orgId      = Auth::user()->organization_id;
        $subjectIds = DB::table('standard_subjects')
            ->where('standard_id', $value)
            ->pluck('subject_id')
            ->toArray();

        $this->sylModalSubjects = Subject::where('organization_id', $orgId)
            ->whereIn('id', $subjectIds)
            ->where('is_active', true)
            ->get(['id', 'name'])
            ->toArray();
    }

    public function updatedSylModalSubjectId($value): void
    {
        $this->sylModalChapterIds = [];

        if (!$value || !$this->sylModalStandardId) {
            $this->sylModalChapters = [];
            return;
        }

        $orgId = Auth::user()->organization_id;

        $this->sylModalChapters = Chapter::with('topics:id,chapter_id,topic_name')
            ->where('organization_id', $orgId)
            ->where('standard_id', $this->sylModalStandardId)
            ->where('subject_id', $value)
            ->orderBy('order')
            ->get(['id', 'name', 'description', 'order'])
            ->toArray();

        // If exam already chosen + we have existing syllabus, pre-tick those chapters
        if ($this->sylModalExamId) {
            $this->sylModalChapterIds = ExamSyllabusChapter::where('organization_id', $orgId)
                ->where('exam_id', $this->sylModalExamId)
                ->where('standard_id', $this->sylModalStandardId)
                ->where('subject_id', $value)
                ->pluck('chapter_id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        }
    }

    public function updatedSylModalExamId($value): void
    {
        // Refresh existing selections if subject already chosen
        if ($value && $this->sylModalSubjectId && $this->sylModalStandardId) {
            $this->updatedSylModalSubjectId($this->sylModalSubjectId);
        }
    }

    public function toggleAllChapters($selectAll): void
    {
        $this->sylModalChapterIds = $selectAll
            ? collect($this->sylModalChapters)->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function saveSyllabus(): void
    {
        $this->validate([
            'sylModalExamId'     => 'required|integer|exists:exams,id',
            'sylModalStandardId' => 'required|integer',
            'sylModalSubjectId'  => 'required|integer',
            'sylModalChapterIds' => 'required|array|min:1',
        ], [
            'sylModalExamId.required'     => 'Please select an exam.',
            'sylModalStandardId.required' => 'Please select a class.',
            'sylModalSubjectId.required'  => 'Please select a subject.',
            'sylModalChapterIds.required' => 'Please select at least one chapter.',
            'sylModalChapterIds.min'      => 'Please select at least one chapter.',
        ]);

        $orgId = Auth::user()->organization_id;

        try {
            DB::transaction(function () use ($orgId) {
                // Replace strategy: delete existing rows for this exam+class+subject, insert fresh selection
                ExamSyllabusChapter::where('organization_id', $orgId)
                    ->where('exam_id', $this->sylModalExamId)
                    ->where('standard_id', $this->sylModalStandardId)
                    ->where('subject_id', $this->sylModalSubjectId)
                    ->delete();

                foreach ($this->sylModalChapterIds as $chapterId) {
                    ExamSyllabusChapter::create([
                        'organization_id' => $orgId,
                        'exam_id'         => $this->sylModalExamId,
                        'standard_id'     => $this->sylModalStandardId,
                        'subject_id'      => $this->sylModalSubjectId,
                        'chapter_id'      => (int) $chapterId,
                    ]);
                }
            });

            $this->notification()->success('Syllabus saved successfully!');
            $this->loadStatistics();
            $this->closeSyllabusModal();
        } catch (\Exception $e) {
            $this->notification()->error('Error saving syllabus', $e->getMessage());
        }
    }

    // ─── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        $exams    = $this->getExams();
        $syllabus = $this->getSyllabusView();

        // Cascading dropdown options for syllabus filter
        $filterSubjects = [];
        $filterExams    = [];

        if ($this->syllabusFilterStandard) {
            $subjectIds = DB::table('standard_subjects')
                ->where('standard_id', $this->syllabusFilterStandard)
                ->pluck('subject_id')->toArray();

            $filterSubjects = Subject::where('organization_id', Auth::user()->organization_id)
                ->whereIn('id', $subjectIds)
                ->get(['id', 'name'])
                ->toArray();
        }

        if ($this->syllabusFilterSubject) {
            // All exams that have syllabus rows for this standard+subject
            $examIds = ExamSyllabusChapter::where('organization_id', Auth::user()->organization_id)
                ->where('standard_id', $this->syllabusFilterStandard)
                ->where('subject_id', $this->syllabusFilterSubject)
                ->distinct()->pluck('exam_id')->toArray();

            $filterExams = Exam::whereIn('id', $examIds)
                ->orderBy('start_date', 'desc')
                ->get(['id', 'exam_name', 'academic_year'])
                ->toArray();
        }

        return view('livewire.admin.add-exam', compact('exams', 'syllabus', 'filterSubjects', 'filterExams'));
    }

    private function getExams()
    {
        $query = Exam::with(['createdBy', 'updatedBy'])
            ->where('organization_id', Auth::user()->organization_id);

        if ($this->search) {
            $query->where('exam_name', 'like', '%' . $this->search . '%');
        }
        if ($this->filterAcademicYear) {
            $query->where('academic_year', $this->filterAcademicYear);
        }
        if ($this->filterExamType) {
            $query->where('exam_type', $this->filterExamType);
        }
        if ($this->filterStatus) {
            match ($this->filterStatus) {
                'published' => $query->where('is_published', true),
                'draft'     => $query->where('is_published', false),
                'active'    => $query->where('start_date', '<=', now())->where('end_date', '>=', now()),
                'upcoming'  => $query->where('start_date', '>', now()),
                'completed' => $query->where('end_date', '<', now()),
                default     => null,
            };
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    /**
     * When all three filters set, return the chapters (with topics) selected
     * as syllabus for that exam+class+subject.
     * Otherwise return a grouped list of (exam, class, subject) syllabus groups.
     */
    private function getSyllabusView(): array
    {
        $orgId = Auth::user()->organization_id;

        // All three selected → full chapter+topic detail
        if ($this->syllabusFilterStandard && $this->syllabusFilterSubject && $this->syllabusFilterExam) {
            $chapterIds = ExamSyllabusChapter::where('organization_id', $orgId)
                ->where('exam_id', $this->syllabusFilterExam)
                ->where('standard_id', $this->syllabusFilterStandard)
                ->where('subject_id', $this->syllabusFilterSubject)
                ->pluck('chapter_id')->toArray();

            $chapters = Chapter::with('topics:id,chapter_id,topic_name')
                ->whereIn('id', $chapterIds)
                ->orderBy('order')
                ->get(['id', 'name', 'description', 'order'])
                ->toArray();

            return [
                'mode'     => 'detail',
                'chapters' => $chapters,
            ];
        }

        // Otherwise grouped overview
        $rows = ExamSyllabusChapter::with(['exam:id,exam_name,academic_year', 'standard:id,name', 'subject:id,name'])
            ->where('organization_id', $orgId)
            ->when($this->syllabusFilterStandard, fn($q) => $q->where('standard_id', $this->syllabusFilterStandard))
            ->when($this->syllabusFilterSubject, fn($q) => $q->where('subject_id', $this->syllabusFilterSubject))
            ->get()
            ->groupBy(fn($r) => $r->exam_id . '-' . $r->standard_id . '-' . $r->subject_id)
            ->map(fn($group) => [
                'exam_id'      => $group->first()->exam_id,
                'exam_name'    => $group->first()->exam->exam_name ?? 'N/A',
                'standard_id'  => $group->first()->standard_id,
                'standard_name' => $group->first()->standard->name ?? 'N/A',
                'subject_id'   => $group->first()->subject_id,
                'subject_name' => $group->first()->subject->name ?? 'N/A',
                'chapter_count' => $group->count(),
            ])
            ->values()
            ->toArray();

        return [
            'mode'   => 'list',
            'groups' => $rows,
        ];
    }
}
