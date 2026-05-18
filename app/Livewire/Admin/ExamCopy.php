<?php

namespace App\Livewire\Admin;

use App\Models\Admin\Exam;
use App\Models\Admin\ExamCopy as ModelsExamCopy;
use App\Models\Student\Standard;
use App\Models\Student\Section;
use App\Models\Student\StudentDetail;
use App\Models\Student\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Url;
use WireUi\Traits\WireUiActions;

class ExamCopy extends Component
{
    use WireUiActions, WithPagination, WithFileUploads;

    public $activeTab = 'list';
    public $showSlider = false;
    public $sliderTitle = '';
    public $sliderData = [];

    // Statistics
    public $totalExamCopies = 0;
    public $totalStudents = 0;
    public $uploadedCopies = 0;
    public $pendingUploads = 0;

    // Filters for list tab
    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 10;

    #[Url]
    public $filterExam = '';

    #[Url]
    public $filterStandard = '';

    #[Url]
    public $filterSection = '';

    #[Url]
    public $filterSubject = '';

    // For view tab filters
    public $selectedExam = '';
    public $selectedStandard = '';
    public $selectedSection = '';
    public $selectedStudent = '';
    public $studentPerformance = [];

    // For upload PDF tab
    public $uploadExam = '';
    public $uploadStandard = '';
    public $uploadSection = '';
    public $uploadSubject = '';
    public $studentPdfs = [];
    public $uploadedFiles = [];

    // Data for dropdowns
    public $exams;
    public $standards = [];
    public $sections = [];
    public $students;
    public $subjects;

    public function mount()
    {
        $this->loadFilters();
        $this->loadInitialData();
        $this->loadStatistics();
    }

    public function loadFilters()
    {
        $organizationId = Auth::user()->organization_id;

        $this->exams = Exam::where('organization_id', $organizationId)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->standards = Standard::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $this->subjects = Subject::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadInitialData()
    {
        if ($this->filterStandard) {
            $this->sections = Section::where('standard_id', $this->filterStandard)
                ->where('is_active', true)
                ->get();

            $this->loadSubjectsForStandard($this->filterStandard, $this->filterSection);
        }
    }

    public function loadStatistics()
    {
        $organizationId = Auth::user()->organization_id;

        $query = ModelsExamCopy::where('organization_id', $organizationId);

        if ($this->filterExam) {
            $query->where('exam_id', $this->filterExam);
        }
        if ($this->filterStandard) {
            $query->where('standard_id', $this->filterStandard);
        }
        if ($this->filterSection) {
            $query->where('section_id', $this->filterSection);
        }
        if ($this->filterSubject) {
            $query->where('subject_id', $this->filterSubject);
        }

        $this->totalExamCopies = (clone $query)->count();
        $this->totalStudents = (clone $query)
            ->whereNotNull('student_detail_id')
            ->distinct('student_detail_id')
            ->count('student_detail_id');
        $this->uploadedCopies = (clone $query)->whereNotNull('pdf_path')->count();
        $this->pendingUploads = (clone $query)->whereNull('pdf_path')->count();
    }

    public function updated($property, $value)
    {
        if (in_array($property, ['search', 'filterExam', 'filterStandard', 'filterSection', 'filterSubject'])) {
            $this->resetPage();
        }

        if ($property === 'filterStandard' && $value) {
            $this->sections = Section::where('standard_id', $value)->where('is_active', true)->get();
            $this->filterSection = '';
            $this->loadSubjectsForStandard($value);
            $this->filterSubject = '';
        } elseif ($property === 'filterStandard' && !$value) {
            $this->sections = [];
            $this->filterSection = '';
            $this->loadFilters();
            $this->filterSubject = '';
        }

        if ($property === 'filterSection' && $value && $this->filterStandard) {
            $this->loadSubjectsForStandard($this->filterStandard, $value);
            $this->filterSubject = '';
        } elseif ($property === 'filterSection' && !$value && $this->filterStandard) {
            $this->loadSubjectsForStandard($this->filterStandard);
            $this->filterSubject = '';
        }

        $this->loadStatistics();
    }

    public function updatedSelectedStandard($value)
    {
        $this->selectedSection = '';
        $this->selectedStudent = '';
        $this->students = [];
        $this->studentPerformance = [];

        if ($value) {
            $this->sections = Section::where('standard_id', $value)->where('is_active', true)->get();
            $this->loadSubjectsForStandard($value);
        } else {
            $this->sections = [];
            $organizationId = Auth::user()->organization_id;
            $this->subjects = Subject::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }
    }

    public function updatedSelectedSection($value)
    {
        $this->selectedStudent = '';
        $this->studentPerformance = [];

        if ($value && $this->selectedStandard) {
            $this->loadStudents($this->selectedStandard, $value);
            $this->loadSubjectsForStandard($this->selectedStandard, $value);
        } else {
            $this->students = [];
            if ($this->selectedStandard) {
                $this->loadSubjectsForStandard($this->selectedStandard);
            }
        }
    }

    public function updatedSelectedStudent($value)
    {
        $this->studentPerformance = [];
        if ($value && $this->selectedExam && $this->selectedStandard && $this->selectedSection) {
            $this->searchPerformance();
        }
    }

    public function updatedUploadStandard($value)
    {
        $this->uploadSection = '';
        $this->uploadSubject = '';

        if ($value) {
            $this->sections = Section::where('standard_id', $value)->where('is_active', true)->get();
            $this->loadSubjectsForStandard($value);
        } else {
            $this->sections = [];
            $organizationId = Auth::user()->organization_id;
            $this->subjects = Subject::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }
        $this->resetStudentPdfs();
    }

    public function updatedUploadSection($value)
    {
        if ($value && $this->uploadStandard) {
            $this->loadStudents($this->uploadStandard, $value);
            $this->loadSubjectsForStandard($this->uploadStandard, $value);
        } else {
            $this->students = [];
            if ($this->uploadStandard) {
                $this->loadSubjectsForStandard($this->uploadStandard);
            }
        }
        $this->resetStudentPdfs();
    }

    public function updatedUploadSubject()
    {
        $this->loadStudentPdfs();
    }

    public function updatedUploadExam()
    {
        $this->loadStudentPdfs();
    }

    public function showTab($tab)
    {
        $this->activeTab = $tab;

        if ($tab === 'view') {
            $this->reset(['selectedExam', 'selectedStandard', 'selectedSection', 'selectedStudent', 'studentPerformance']);
            $this->sections = [];
            $this->students = [];
            $this->loadFilters();
        } elseif ($tab === 'upload') {
            $this->reset(['uploadExam', 'uploadStandard', 'uploadSection', 'uploadSubject', 'studentPdfs', 'uploadedFiles']);
            $this->sections = [];
            $this->students = [];
            $this->loadFilters();
        } else {
            $this->resetPage();
            $this->loadFilters();
            $this->loadStatistics();
        }
    }

    private function loadStudents($standardId, $sectionId)
    {
        $this->students = StudentDetail::where('standard_id', $standardId)
            ->where('section_id', $sectionId)
            ->with('user')
            ->orderBy('roll_no')
            ->get();
    }

    private function loadSubjectsForStandard($standardId, $sectionId = null)
    {
        $organizationId = Auth::user()->organization_id;

        if ($sectionId) {
            $this->subjects = Subject::join('section_subjects', 'subjects.id', '=', 'section_subjects.subject_id')
                ->where('section_subjects.section_id', $sectionId)
                ->where('section_subjects.standard_id', $standardId)
                ->where('subjects.organization_id', $organizationId)
                ->where('subjects.is_active', true)
                ->select('subjects.*')
                ->distinct()
                ->orderBy('subjects.name')
                ->get();
        } else {
            $this->subjects = Subject::join('standard_subjects', 'subjects.id', '=', 'standard_subjects.subject_id')
                ->where('standard_subjects.standard_id', $standardId)
                ->where('subjects.organization_id', $organizationId)
                ->where('subjects.is_active', true)
                ->select('subjects.*')
                ->distinct()
                ->orderBy('subjects.name')
                ->get();
        }

        if ($this->subjects->isEmpty()) {
            $this->subjects = collect();
        }
    }

    private function loadStudentPdfs()
    {
        $this->studentPdfs = [];
        $this->uploadedFiles = [];

        if (!$this->uploadExam || !$this->uploadStandard || !$this->uploadSection || !$this->uploadSubject) {
            return;
        }

        foreach ($this->students as $student) {
            $existingCopy = ModelsExamCopy::where('exam_id', $this->uploadExam)
                ->where('standard_id', $this->uploadStandard)
                ->where('section_id', $this->uploadSection)
                ->where('subject_id', $this->uploadSubject)
                ->where('student_detail_id', $student->id)
                ->first();

            $this->studentPdfs[$student->id] = [
                'student_id' => $student->id,
                'student_name' => $student->user->name,
                'roll_no' => $student->roll_no,
                'pdf_path' => $existingCopy ? $existingCopy->pdf_path : null,
                'uploaded_at' => $existingCopy ? $existingCopy->updated_at : null,
                'remarks' => $existingCopy ? $existingCopy->remarks : '',
            ];
        }
    }

    private function resetStudentPdfs()
    {
        $this->studentPdfs = [];
        $this->uploadedFiles = [];
    }

    public function searchPerformance()
    {
        try {
            $this->validate([
                'selectedExam' => 'required',
                'selectedStandard' => 'required',
                'selectedSection' => 'required',
                'selectedStudent' => 'required',
            ], [
                'selectedExam.required' => 'Please select an exam',
                'selectedStandard.required' => 'Please select a standard',
                'selectedSection.required' => 'Please select a section',
                'selectedStudent.required' => 'Please select a student',
            ]);

            $results = ModelsExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'studentDetail.user'
            ])
                ->where('exam_id', $this->selectedExam)
                ->where('standard_id', $this->selectedStandard)
                ->where('section_id', $this->selectedSection)
                ->where('student_detail_id', $this->selectedStudent)
                ->get();

            if ($results->isEmpty()) {
                $this->studentPerformance = [];
                $this->notification()->warning('No Results', 'No exam copy records found for the selected criteria.');
                return;
            }

            $this->studentPerformance = $results->toArray();
            $this->notification()->success('Success', count($this->studentPerformance) . ' exam copy record(s) loaded successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->studentPerformance = [];
            $this->notification()->error('Error', 'An error occurred while searching. Please try again.');
        }
    }

    public function uploadPdfs()
    {
        $this->validate([
            'uploadExam' => 'required',
            'uploadStandard' => 'required',
            'uploadSection' => 'required',
            'uploadSubject' => 'required',
        ]);

        $rules = [];
        foreach ($this->uploadedFiles as $studentId => $file) {
            if ($file) {
                $rules["uploadedFiles.$studentId"] = 'file|mimes:pdf|max:5120';
            }
        }

        if (!empty($rules)) {
            $this->validate($rules);
        }

        try {
            $savedCount = 0;

            foreach ($this->uploadedFiles as $studentId => $file) {
                if ($file) {
                    $existingCopy = ModelsExamCopy::where('exam_id', $this->uploadExam)
                        ->where('standard_id', $this->uploadStandard)
                        ->where('section_id', $this->uploadSection)
                        ->where('subject_id', $this->uploadSubject)
                        ->where('student_detail_id', $studentId)
                        ->first();

                    if ($existingCopy && $existingCopy->pdf_path) {
                        Storage::disk('s3')->delete($existingCopy->pdf_path);
                    }

                    $student = $this->students->firstWhere('id', $studentId);
                    $exam = $this->exams->firstWhere('id', $this->uploadExam);
                    $subject = $this->subjects->firstWhere('id', $this->uploadSubject);

                    $fileName = 'exam_copies/' .
                        $exam->id . '_' .
                        $subject->id . '_' .
                        $student->id . '_' .
                        time() . '.pdf';

                    $path = $file->storeAs('admin/exam-copies', basename($fileName), 's3');
                    Storage::disk('s3')->setVisibility($path, 'public');

                    $remarks = $this->studentPdfs[$studentId]['remarks'] ?? '';

                    ModelsExamCopy::updateOrCreate(
                        [
                            'exam_id' => $this->uploadExam,
                            'standard_id' => $this->uploadStandard,
                            'section_id' => $this->uploadSection,
                            'subject_id' => $this->uploadSubject,
                            'student_detail_id' => $studentId,
                        ],
                        [
                            'organization_id' => Auth::user()->organization_id,
                            'pdf_path' => $path,
                            'remarks' => $remarks,
                            'uploaded_by' => Auth::id(),
                        ]
                    );

                    $savedCount++;
                }
            }

            if ($savedCount > 0) {
                $this->notification()->success('PDFs Uploaded Successfully', "PDFs for $savedCount students have been uploaded.");
                $this->uploadedFiles = [];
                $this->loadStudentPdfs();
                $this->loadStatistics();
            } else {
                $this->notification()->warning('No PDFs Uploaded', 'Please select at least one PDF file to upload.');
            }
        } catch (\Exception $e) {
            $this->notification()->error('Error uploading PDFs', $e->getMessage());
        }
    }

    public function deletePdf($studentId)
    {
        try {
            $examCopy = ModelsExamCopy::where('exam_id', $this->uploadExam)
                ->where('standard_id', $this->uploadStandard)
                ->where('section_id', $this->uploadSection)
                ->where('subject_id', $this->uploadSubject)
                ->where('student_detail_id', $studentId)
                ->first();

            if ($examCopy && $examCopy->pdf_path) {
                Storage::disk('s3')->delete($examCopy->pdf_path);
                $examCopy->pdf_path = null;
                $examCopy->save();

                $this->notification()->success('PDF deleted successfully!');
                $this->loadStudentPdfs();
                $this->loadStatistics();
            }
        } catch (\Exception $e) {
            $this->notification()->error('Error deleting PDF', $e->getMessage());
        }
    }

    public function onView($id)
    {
        try {
            $examCopy = ModelsExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'studentDetail.user'
            ])->find($id);

            if (!$examCopy) {
                $this->notification()->error('Exam copy record not found!');
                return;
            }

            $this->sliderTitle = 'Exam Copy Details';
            $this->sliderData = [
                'exam_copy' => $examCopy
            ];

            $this->showSlider = true;
        } catch (\Exception $e) {
            $this->notification()->error('Error loading details', $e->getMessage());
        }
    }

    public function closeSlider()
    {
        $this->showSlider = false;
        $this->sliderData = [];
        $this->sliderTitle = '';
    }

    public function onDownloadPdf($examCopyId)
    {
        try {
            $examCopy = ModelsExamCopy::find($examCopyId);

            if (!$examCopy || !$examCopy->pdf_path) {
                $this->notification()->error('PDF not found!');
                return;
            }

            if (!Storage::disk('s3')->exists($examCopy->pdf_path)) {
                $this->notification()->error('PDF file does not exist!');
                return;
            }

            return redirect(Storage::disk('s3')->url($examCopy->pdf_path));
        } catch (\Exception $e) {
            $this->notification()->error('Error downloading PDF', $e->getMessage());
        }
    }

    public function onDelete($id)
    {
        $this->dialog()->confirm([
            'title' => 'Are you Sure?',
            'icon' => 'exclamation-circle',
            'iconColor' => 'text-red-500',
            'description' => 'Are you sure you want to delete this exam copy record? The PDF file will also be deleted.',
            'accept' => [
                'label' => 'Yes, delete it',
                'method' => 'doDelete',
                'params' => $id,
                'color' => 'negative',
            ],
            'reject' => [
                'label' => 'No',
            ],
        ]);
    }

    public function doDelete($id)
    {
        try {
            $examCopy = ModelsExamCopy::find($id);

            if ($examCopy) {
                if ($examCopy->pdf_path) {
                    Storage::disk('s3')->delete($examCopy->pdf_path);
                }

                $examCopy->delete();
                $this->notification()->success('Exam copy record deleted successfully!');
                $this->loadStatistics();
            }
        } catch (\Exception $e) {
            $this->notification()->error('Error deleting record', $e->getMessage());
        }
    }

    public function render()
    {
        $examCopies = $this->getExamCopies();
        return view('livewire.admin.exam-copy', compact('examCopies'));
    }

    private function getExamCopies()
    {
        if ($this->activeTab !== 'list') {
            return collect();
        }

        $query = ModelsExamCopy::with([
            'exam',
            'standard',
            'section',
            'subject',
            'studentDetail.user'
        ])
            ->where('organization_id', Auth::user()->organization_id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('studentDetail.user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('exam', function ($examQuery) {
                        $examQuery->where('exam_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('subject', function ($subjectQuery) {
                        $subjectQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->filterExam) {
            $query->where('exam_id', $this->filterExam);
        }

        if ($this->filterStandard) {
            $query->where('standard_id', $this->filterStandard);
        }

        if ($this->filterSection) {
            $query->where('section_id', $this->filterSection);
        }

        if ($this->filterSubject) {
            $query->where('subject_id', $this->filterSubject);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }
}
