<?php

namespace App\Livewire\Admin;

use App\Models\Admin\Exam;
use App\Models\Admin\ExamCopy;
use App\Models\Student\Standard;
use App\Models\Student\Section;
use App\Models\Student\StudentDetail;
use App\Models\Student\Subject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use WireUi\Traits\WireUiActions;

class Performance extends Component
{
    use WireUiActions, WithPagination;

    public $activeTab = 'list';
    public $showSlider = false;
    public $sliderTitle = '';
    public $sliderData = [];

    // For list tab filters
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

    // For upload marks tab
    public $uploadExam = '';
    public $uploadStandard = '';
    public $uploadSection = '';
    public $uploadSubject = '';
    public $studentMarks = [];

    // Data for dropdowns
    public $exams = [];
    public $standards = [];
    public $sections = [];
    public $students = [];
    public $subjects = [];

    public function mount()
    {
        $this->loadFilters();
        $this->loadInitialData();
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

            // Load subjects for the selected standard
            $this->loadSubjectsForStandard($this->filterStandard, $this->filterSection);
        }
    }

    // Real-time methods for list tab
    public function updated($property, $value)
    {
        if (in_array($property, ['search', 'filterExam', 'filterStandard', 'filterSection', 'filterSubject'])) {
            $this->resetPage();
        }

        // Handle real-time updates for dropdowns
        if ($property === 'filterStandard' && $value) {
            $this->sections = Section::where('standard_id', $value)
                ->where('is_active', true)
                ->get();
            $this->filterSection = '';

            // Load subjects for this standard
            $this->loadSubjectsForStandard($value);
            $this->filterSubject = '';
        } elseif ($property === 'filterStandard' && !$value) {
            $this->sections = [];
            $this->filterSection = '';

            // Reset subjects to all subjects
            $this->loadFilters();
            $this->filterSubject = '';
        }

        // Update subjects when section changes
        if ($property === 'filterSection' && $value && $this->filterStandard) {
            $this->loadSubjectsForStandard($this->filterStandard, $value);
            $this->filterSubject = '';
        } elseif ($property === 'filterSection' && !$value && $this->filterStandard) {
            // Reset to standard-level subjects when section is cleared
            $this->loadSubjectsForStandard($this->filterStandard);
            $this->filterSubject = '';
        }
    }

    // Real-time methods for view tab
    public function updatedSelectedStandard($value)
    {
        $this->selectedSection = '';
        $this->selectedStudent = '';
        $this->students = [];
        $this->studentPerformance = [];

        if ($value) {
            $this->sections = Section::where('standard_id', $value)
                ->where('is_active', true)
                ->get();

            // Load subjects for this standard
            $this->loadSubjectsForStandard($value);
        } else {
            $this->sections = [];
            // Reset to all subjects
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
            // Load subjects for this section
            $this->loadSubjectsForStandard($this->selectedStandard, $value);
        } else {
            $this->students = [];
            // Reset to standard-level subjects
            if ($this->selectedStandard) {
                $this->loadSubjectsForStandard($this->selectedStandard);
            }
        }
    }

    public function updatedSelectedStudent($value)
    {
        $this->studentPerformance = [];

        // Auto-search when student is selected
        if ($value && $this->selectedExam && $this->selectedStandard && $this->selectedSection) {
            $this->searchPerformance();
        }
    }

    // Real-time methods for upload marks tab
    public function updatedUploadStandard($value)
    {
        $this->uploadSection = '';
        $this->uploadSubject = '';

        if ($value) {
            $this->sections = Section::where('standard_id', $value)
                ->where('is_active', true)
                ->get();

            // Load subjects for this standard
            $this->loadSubjectsForStandard($value);
        } else {
            $this->sections = [];
            // Reset to all subjects
            $organizationId = Auth::user()->organization_id;
            $this->subjects = Subject::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }
        $this->resetStudentMarks();
    }

    public function updatedUploadSection($value)
    {
        if ($value && $this->uploadStandard) {
            $this->loadStudents($this->uploadStandard, $value);
            // Load subjects for this section
            $this->loadSubjectsForStandard($this->uploadStandard, $value);
        } else {
            $this->students = [];
            // Reset to standard-level subjects
            if ($this->uploadStandard) {
                $this->loadSubjectsForStandard($this->uploadStandard);
            }
        }
        $this->resetStudentMarks();
    }

    public function updatedUploadSubject()
    {
        $this->loadStudentMarks();
    }

    public function updatedUploadExam()
    {
        $this->loadStudentMarks();
    }

    public function showTab($tab)
    {
        $this->activeTab = $tab;

        if ($tab === 'view') {
            $this->reset(['selectedExam', 'selectedStandard', 'selectedSection', 'selectedStudent', 'studentPerformance']);
            $this->sections = [];
            $this->students = [];
            $this->loadFilters(); // Reset subjects
        } elseif ($tab === 'upload') {
            $this->reset(['uploadExam', 'uploadStandard', 'uploadSection', 'uploadSubject', 'studentMarks']);
            $this->sections = [];
            $this->students = [];
            $this->loadFilters(); // Reset subjects
        } else {
            $this->resetPage();
            $this->loadFilters(); // Reset subjects
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

    /**
     * Load subjects based on standard and optionally section
     * Uses standard_subjects and section_subjects pivot tables
     */
    private function loadSubjectsForStandard($standardId, $sectionId = null)
    {
        $organizationId = Auth::user()->organization_id;

        if ($sectionId) {
            // Get subjects assigned to this specific section
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
            // Get subjects assigned to this standard
            $this->subjects = Subject::join('standard_subjects', 'subjects.id', '=', 'standard_subjects.subject_id')
                ->where('standard_subjects.standard_id', $standardId)
                ->where('subjects.organization_id', $organizationId)
                ->where('subjects.is_active', true)
                ->select('subjects.*')
                ->distinct()
                ->orderBy('subjects.name')
                ->get();
        }

        // If no subjects found, return empty collection
        if ($this->subjects->isEmpty()) {
            $this->subjects = collect();
        }
    }

    private function loadStudentMarks()
    {
        $this->studentMarks = [];

        if (!$this->uploadExam || !$this->uploadStandard || !$this->uploadSection || !$this->uploadSubject) {
            return;
        }

        foreach ($this->students as $student) {
            $existingMarks = ExamCopy::where('exam_id', $this->uploadExam)
                ->where('standard_id', $this->uploadStandard)
                ->where('section_id', $this->uploadSection)
                ->where('subject_id', $this->uploadSubject)
                ->where('student_detail_id', $student->id)
                ->first();

            $this->studentMarks[$student->id] = [
                'student_id' => $student->id,
                'student_name' => $student->user->name,
                'roll_no' => $student->roll_no,
                'marks_obtained' => $existingMarks ? $existingMarks->marks_obtained : '',
                'max_marks' => $existingMarks ? $existingMarks->max_marks : 100,
                'grade' => $existingMarks ? $existingMarks->grade : '',
                'remarks' => $existingMarks ? $existingMarks->remarks : '',
            ];
        }
    }

    private function resetStudentMarks()
    {
        $this->studentMarks = [];
    }

    // List tab methods
    public function onView($id)
    {
        try {
            $examCopy = ExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'studentDetail.user',
                'examSubjectMarks.subject'
            ])->find($id);

            if (!$examCopy) {
                $this->notification()->error('Exam record not found!');
                return;
            }

            $this->sliderTitle = 'Exam Copy Details';
            $this->sliderData = [
                'exam_copy' => $examCopy,
                'subject_marks' => $examCopy->examSubjectMarks
            ];

            $this->showSlider = true;
        } catch (\Exception $e) {
            $this->notification()->error(
                'Error loading details',
                $e->getMessage()
            );
        }
    }

    public function closeSlider()
    {
        $this->showSlider = false;
        $this->sliderData = [];
        $this->sliderTitle = '';
    }

    public function onDownloadPdf($examCopyId = null, $type = 'single')
    {
        try {
            if ($type === 'single' && $examCopyId) {
                return $this->downloadSinglePdf($examCopyId);
            } elseif ($type === 'multiple' && $this->selectedExam) {
                return $this->downloadMultiplePdf();
            }
        } catch (\Exception $e) {
            $this->notification()->error(
                'Error generating PDF',
                $e->getMessage()
            );
        }
    }

    public function onDelete($id)
    {
        $this->dialog()->confirm([
            'title' => 'Are you Sure?',
            'icon' => 'exclamation-circle',
            'iconColor' => 'text-red-500',
            'description' => 'Are you sure you want to delete this exam record?',
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
            $examCopy = ExamCopy::find($id);

            if ($examCopy) {
                $examCopy->delete();
                $this->notification()->success('Exam record deleted successfully!');
            }
        } catch (\Exception $e) {
            $this->notification()->error('Error deleting record', $e->getMessage());
        }
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

            // Get records with the exact filters
            $results = ExamCopy::with([
                'exam',
                'standard',
                'section',
                'subject',
                'studentDetail.user',
                'examSubjectMarks.subject'
            ])
                ->where('exam_id', $this->selectedExam)
                ->where('standard_id', $this->selectedStandard)
                ->where('section_id', $this->selectedSection)
                ->where('student_detail_id', $this->selectedStudent)
                ->get();

            if ($results->isEmpty()) {
                $this->studentPerformance = [];
                $this->notification()->warning(
                    'No Results',
                    'No performance records found for the selected criteria.'
                );
                return;
            }

            // Convert to array for Blade template
            $this->studentPerformance = $results->toArray();

            $this->notification()->success(
                'Success',
                count($this->studentPerformance) . ' performance record(s) loaded successfully.'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error in searchPerformance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->studentPerformance = [];
            $this->notification()->error(
                'Error',
                'An error occurred while searching. Please try again.'
            );
        }
    }

    // Upload marks methods
    public function uploadMarks()
    {
        $this->validate([
            'uploadExam' => 'required',
            'uploadStandard' => 'required',
            'uploadSection' => 'required',
            'uploadSubject' => 'required',
        ]);

        try {
            $savedCount = 0;

            foreach ($this->studentMarks as $studentId => $marks) {
                if (!empty($marks['marks_obtained']) && is_numeric($marks['marks_obtained'])) {
                    $maxMarks = $marks['max_marks'] ?? 100;
                    $marksObtained = floatval($marks['marks_obtained']);
                    $percentage = ($marksObtained / $maxMarks) * 100;
                    $grade = $this->calculateGrade($percentage);

                    ExamCopy::updateOrCreate(
                        [
                            'exam_id' => $this->uploadExam,
                            'standard_id' => $this->uploadStandard,
                            'section_id' => $this->uploadSection,
                            'subject_id' => $this->uploadSubject,
                            'student_detail_id' => $studentId,
                        ],
                        [
                            'organization_id' => Auth::user()->organization_id,
                            'marks_obtained' => $marksObtained,
                            'max_marks' => $maxMarks,
                            'percentage' => round($percentage, 2),
                            'grade' => $grade,
                            'remarks' => $marks['remarks'] ?? '',
                        ]
                    );

                    $savedCount++;
                }
            }

            if ($savedCount > 0) {
                $this->notification()->success(
                    'Marks Saved Successfully',
                    "Marks for $savedCount students have been saved."
                );
                $this->loadStudentMarks(); // Refresh with updated data
            } else {
                $this->notification()->warning(
                    'No Marks Saved',
                    'Please enter marks for at least one student.'
                );
            }
        } catch (\Exception $e) {
            $this->notification()->error(
                'Error saving marks',
                $e->getMessage()
            );
        }
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 33) return 'D';
        return 'F';
    }

    private function downloadSinglePdf($examCopyId)
    {
        $this->notification()->success(
            'PDF Download',
            'Exam copy PDF download initiated.'
        );
    }

    private function downloadMultiplePdf()
    {
        $this->notification()->success(
            'PDF Download',
            'Multiple exam copies PDF download initiated.'
        );
    }

    public function render()
    {
        $examCopies = $this->getExamCopies();
        return view('livewire.admin.performance', compact('examCopies'));
    }

    private function getExamCopies()
    {
        if ($this->activeTab !== 'list') {
            return collect();
        }

        $query = ExamCopy::with([
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
