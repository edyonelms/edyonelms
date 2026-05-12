<?php

namespace App\Livewire\Admin;

use App\Models\Admin\Exam;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class AddExam extends Component
{
    use WireUiActions, WithPagination;

    // Form properties
    public $examName = '';
    public $academicYear = '';
    public $startDate = '';
    public $endDate = '';
    public $description = '';
    public $isPublished = false;
    public $examType = '';
    public $totalMarks = '';
    public $passingMarks = '';
    public $usesGradingSystem = false;
    public $editId = null;

    // Modal states
    public $open = false;
    public $showViewModal = false;
    public $viewModalTitle = '';
    public $viewData = [];

    // Filter properties
    public $search = '';
    public $perPage = 10;
    public $filterAcademicYear = '';
    public $filterExamType = '';
    public $filterStatus = '';

    // Data options
    public $academicYearOptions = [];
    public $examTypes = [
        'quarterly' => 'Quarterly',
        'half_yearly' => 'Half Yearly',
        'annual' => 'Annual',
        'unit_test' => 'Unit Test',
        'pre_board' => 'Pre Board'
    ];

    // Statistics
    public $totalExams = 0;
    public $publishedExams = 0;
    public $upcomingExams = 0;
    public $activeExams = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterAcademicYear' => ['except' => ''],
        'filterExamType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        $this->loadAcademicYearOptions();
        $this->loadStatistics();
    }

    public function loadAcademicYearOptions()
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;

        $this->academicYearOptions = [
            $currentYear . '-' . $nextYear,
            $nextYear . '-' . ($nextYear + 1)
        ];

        $this->academicYear = $currentYear . '-' . $nextYear;
    }

    public function loadStatistics()
    {
        $organizationId = Auth::user()->organization_id;

        // Total exams
        $this->totalExams = Exam::where('organization_id', $organizationId)->count();

        // Published exams
        $this->publishedExams = Exam::where('organization_id', $organizationId)
            ->where('is_published', true)
            ->count();

        // Upcoming exams (start date in future)
        $this->upcomingExams = Exam::where('organization_id', $organizationId)
            ->where('start_date', '>', now())
            ->count();

        // Active exams (current date between start and end date)
        $this->activeExams = Exam::where('organization_id', $organizationId)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
    }

    // Filter methods
    public function updatedSearch()
    {
        $this->resetPage();
        $this->loadStatistics();
    }

    public function updatedFilterAcademicYear()
    {
        $this->resetPage();
        $this->loadStatistics();
    }

    public function updatedFilterExamType()
    {
        $this->resetPage();
        $this->loadStatistics();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
        $this->loadStatistics();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    // Form methods
    public function onAddExam()
    {
        $this->resetForm();
        $this->open = true;
        $this->editId = null;
    }

    public function onSave()
    {
        $rules = [
            'examName' => 'required|string|max:255',
            'academicYear' => 'required|string|max:9',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'examType' => 'required|string',
            'usesGradingSystem' => 'boolean',
        ];

        if ($this->usesGradingSystem) {
            // Grading system validation rules
        } else {
            $rules['totalMarks'] = 'required|integer|min:1';
            $rules['passingMarks'] = 'required|integer|min:1|lt:totalMarks';
        }

        $this->validate($rules);

        try {
            $examData = [
                'organization_id' => Auth::user()->organization_id,
                'exam_name' => $this->examName,
                'academic_year' => $this->academicYear,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'description' => $this->description,
                'is_published' => $this->isPublished,
                'exam_type' => $this->examType,
                'uses_grading_system' => $this->usesGradingSystem,
                'total_marks' => $this->usesGradingSystem ? null : $this->totalMarks,
                'passing_marks' => $this->usesGradingSystem ? null : $this->passingMarks,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            if ($this->editId) {
                $exam = Exam::findOrFail($this->editId);
                $exam->update($examData);
                $this->notification()->success('Exam Updated Successfully!');
            } else {
                Exam::create($examData);
                $this->notification()->success('Exam Created Successfully!');
            }

            $this->resetForm();
            $this->loadStatistics();
            $this->dispatch('refreshExams');
        } catch (\Exception $e) {
            $this->notification()->error(
                'Error Saving Exam',
                $e->getMessage()
            );
        }
    }

    public function resetForm()
    {
        $this->reset([
            'examName',
            'academicYear',
            'startDate',
            'endDate',
            'description',
            'isPublished',
            'examType',
            'totalMarks',
            'passingMarks',
            'usesGradingSystem',
            'editId'
        ]);
        $this->open = false;
    }

    // View methods
    public function onViewExam($id)
    {
        $exam = Exam::with(['createdBy', 'updatedBy'])->findOrFail($id);

        $this->viewModalTitle = 'Exam Details - ' . $exam->exam_name;
        $this->viewData = [
            'exam' => $exam,
            'details' => [
                'Exam Name' => $exam->exam_name,
                'Academic Year' => $exam->academic_year,
                'Start Date' => $exam->start_date->format('d-m-Y'),
                'End Date' => $exam->end_date->format('d-m-Y'),
                'Exam Type' => $this->examTypes[$exam->exam_type] ?? $exam->exam_type,
                'Evaluation System' => $exam->uses_grading_system ? 'Grading System' : 'Marks System',
                'Total Marks' => $exam->uses_grading_system ? 'N/A' : $exam->total_marks,
                'Passing Marks' => $exam->uses_grading_system ? 'N/A' : $exam->passing_marks,
                'Status' => $exam->is_published ? 'Published' : 'Draft',
                'Created By' => $exam->createdBy->name ?? 'N/A',
                'Created At' => $exam->created_at->format('d-m-Y H:i'),
                'Last Updated' => $exam->updated_at->format('d-m-Y H:i'),
            ]
        ];

        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewData = [];
    }

    // Edit method
    public function onEditExam($id)
    {
        $exam = Exam::findOrFail($id);
        $this->editId = $exam->id;
        $this->examName = $exam->exam_name;
        $this->academicYear = $exam->academic_year;
        $this->startDate = $exam->start_date ? \Carbon\Carbon::parse($exam->start_date)->format('Y-m-d') : '';
        $this->endDate = $exam->end_date ? \Carbon\Carbon::parse($exam->end_date)->format('Y-m-d') : '';
        $this->description = $exam->description;
        $this->isPublished = $exam->is_published;
        $this->examType = $exam->exam_type;
        $this->totalMarks = $exam->total_marks;
        $this->passingMarks = $exam->passing_marks;
        $this->usesGradingSystem = $exam->uses_grading_system;
        $this->open = true;
    }

    // Delete methods
    public function onDeleteExam($id)
    {
        $this->dialog()->confirm([
            'title' => 'Are you Sure?',
            'description' => 'Are you sure you want to delete this exam? This action cannot be undone.',
            'icon' => 'error',
            'accept' => [
                'label' => 'Yes, delete it',
                'method' => 'doDeleteExam',
                'params' => $id,
            ],
            'reject' => [
                'label' => 'No, cancel',
            ],
        ]);
    }

    public function doDeleteExam($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $exam->delete();
            $this->notification()->success('Exam Deleted Successfully!');
            $this->loadStatistics();
        } catch (\Exception $e) {
            $this->notification()->error('Error Deleting Exam', $e->getMessage());
        }
    }

    // Toggle publish status
    public function onTogglePublish($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $exam->update([
                'is_published' => !$exam->is_published,
                'updated_by' => Auth::id()
            ]);

            $status = $exam->is_published ? 'published' : 'unpublished';
            $this->notification()->success("Exam {$status} successfully!");
            $this->loadStatistics();
        } catch (\Exception $e) {
            $this->notification()->error('Error updating exam status', $e->getMessage());
        }
    }

    public function render()
    {
        $exams = $this->getExams();
        return view('livewire.admin.add-exam', compact('exams'));
    }

    private function getExams()
    {
        $query = Exam::with(['createdBy', 'updatedBy'])
            ->where('organization_id', Auth::user()->organization_id);

        // Apply filters
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
            if ($this->filterStatus === 'published') {
                $query->where('is_published', true);
            } elseif ($this->filterStatus === 'draft') {
                $query->where('is_published', false);
            } elseif ($this->filterStatus === 'active') {
                $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            } elseif ($this->filterStatus === 'upcoming') {
                $query->where('start_date', '>', now());
            } elseif ($this->filterStatus === 'completed') {
                $query->where('end_date', '<', now());
            }
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }
}
