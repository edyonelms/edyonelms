<?php

namespace App\Livewire\Admin;

use App\Models\Student\AdmitCard as ModelAdmitCard;
use App\Models\Admin\Exam;
use App\Models\Student\Section;
use App\Models\Student\Standard;
use App\Models\Student\StudentDetail;
use App\Models\Student\Subject; // Add this
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdmitCard extends Component
{
    use WithPagination, WireUiActions;

    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showBulkGenerateModal = false;
    public $showViewModal = false;
    public $search = '';

    // Filters
    public $examFilter = '';
    public $standardFilter = '';
    public $statusFilter = '';

    public $admitCardId;
    public $admitCardNumber;
    public $studentId;
    public $studentSearch = '';
    public $examId;
    public $rollNumber;
    public $examRollNumber;
    public $examDate;
    public $examTime;
    public $examDuration;
    public $reportingTime;
    public $examCenter;
    public $examCenterAddress;
    public $instructions = '';
    public $seatNumber;
    public $roomNumber;
    public $allowedItems = [];
    public $prohibitedItems = [];
    public $status = 'active';
    public $viewAdmitCard = null;

    // Subject-wise exam schedule
    public $subjects = [];
    public $newSubjectName = '';
    public $newSubjectDate = '';
    public $newSubjectTime = '';
    public $newSubjectDuration = '';
    public $selectedSubjectId = ''; // For subject selection
    public $subjectSearch = ''; // For searching subjects

    // Bulk generation
    public $selectedExam;
    public $selectedStandard;
    public $selectedSection;
    public $selectedStudents = [];
    public $bulkInstructions = '';
    public $bulkExamCenter = '';
    public $bulkExamCenterAddress = '';
    public $bulkSubjects = []; // For bulk subject selection
    public $perPage = 10;

    // New properties for items
    public $newAllowedItem = '';
    public $newProhibitedItem = '';

    // Common exam items
    public $commonAllowedItems = [
        'Admit Card',
        'School ID Card',
        'Blue/Black Ball Pen',
        'Pencil',
        'Eraser',
        'Sharpener',
        'Geometry Box',
        'Calculator (if allowed)',
        'Water Bottle (transparent)'
    ];

    public $commonProhibitedItems = [
        'Mobile Phone',
        'Smart Watch',
        'Electronic Devices',
        'Books/Notes',
        'Wallet',
        'Food Items',
        'Colored Pens',
        'Corrective Fluid',
        'Any Paper'
    ];

    protected function rules()
    {
        return [
            'studentId' => 'required|exists:student_details,id',
            'examId' => 'required|exists:exams,id',
            'admitCardNumber' => 'required|string|max:50|unique:admit_cards,admit_card_number,' . $this->admitCardId,
            'rollNumber' => 'required|string|max:50',
            'examRollNumber' => 'nullable|string|max:50',
            'reportingTime' => 'required|date_format:H:i',
            'examCenter' => 'required|string|max:255',
            'examCenterAddress' => 'required|string',
            'instructions' => 'nullable|string',
            'seatNumber' => 'nullable|string|max:20',
            'roomNumber' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,used',
            'subjects' => 'required|array|min:1',
            'subjects.*.subject_id' => 'required|exists:subjects,id',
            'subjects.*.subject_name' => 'required|string|max:100',
            'subjects.*.exam_date' => 'required|date',
            'subjects.*.exam_time' => 'required|date_format:H:i',
            'subjects.*.exam_duration' => 'required|string|max:50',
        ];
    }

    public function mount()
    {
        $this->reportingTime = '08:30';
        $this->allowedItems = ['Admit Card', 'Blue/Black Ball Pen', 'Pencil', 'Eraser'];
        $this->prohibitedItems = ['Mobile Phone', 'Smart Watch', 'Books/Notes'];

        // Initialize with one subject
        $this->subjects = [
            [
                'subject_id' => '',
                'subject_name' => '',
                'exam_date' => now()->addDays(7)->format('Y-m-d'),
                'exam_time' => '09:00',
                'exam_duration' => '3 Hours',
            ]
        ];

        // Initialize bulk subjects
        $this->bulkSubjects = [
            [
                'subject_id' => '',
                'subject_name' => '',
                'exam_date' => now()->addDays(7)->format('Y-m-d'),
                'exam_time' => '09:00',
                'exam_duration' => '3 Hours',
            ]
        ];
    }

    // Add subject from database
    public function addSubjectFromList($index)
    {
        $this->validateOnly("subjects.{$index}.subject_id", [
            "subjects.{$index}.subject_id" => 'required|exists:subjects,id'
        ]);

        $subject = Subject::find($this->subjects[$index]['subject_id']);
        if ($subject) {
            $this->subjects[$index]['subject_name'] = $subject->name;
        }
    }

    // Add bulk subject from database
    public function addBulkSubjectFromList($index)
    {
        $this->validateOnly("bulkSubjects.{$index}.subject_id", [
            "bulkSubjects.{$index}.subject_id" => 'required|exists:subjects,id'
        ]);

        $subject = Subject::find($this->bulkSubjects[$index]['subject_id']);
        if ($subject) {
            $this->bulkSubjects[$index]['subject_name'] = $subject->name;
        }
    }

    // CRITICAL FIX: Add this method to refresh students when exam is selected
    public function updatedSelectedExam()
    {
        $this->selectedStudents = [];
    }

    // CRITICAL FIX: Add this method to refresh students when section changes
    public function updatedSelectedSection()
    {
        $this->selectedStudents = [];
    }

    // Add new subject row
    public function addSubject()
    {
        $this->subjects[] = [
            'subject_id' => '',
            'subject_name' => '',
            'exam_date' => now()->addDays(7)->format('Y-m-d'),
            'exam_time' => '09:00',
            'exam_duration' => '3 Hours',
        ];
    }

    // Add new bulk subject row
    public function addBulkSubject()
    {
        $this->bulkSubjects[] = [
            'subject_id' => '',
            'subject_name' => '',
            'exam_date' => now()->addDays(7)->format('Y-m-d'),
            'exam_time' => '09:00',
            'exam_duration' => '3 Hours',
        ];
    }

    // Remove subject
    public function removeSubject($index)
    {
        if (count($this->subjects) > 1) {
            unset($this->subjects[$index]);
            $this->subjects = array_values($this->subjects); // Re-index array
        } else {
            $this->notification()->warning(
                $title = 'Warning!',
                $description = 'At least one subject is required!'
            );
        }
    }

    // Remove bulk subject
    public function removeBulkSubject($index)
    {
        if (count($this->bulkSubjects) > 1) {
            unset($this->bulkSubjects[$index]);
            $this->bulkSubjects = array_values($this->bulkSubjects);
        } else {
            $this->notification()->warning(
                $title = 'Warning!',
                $description = 'At least one subject is required!'
            );
        }
    }

    // Reset form
    public function resetForm()
    {
        $this->admitCardId = null;
        $this->admitCardNumber = null;
        $this->studentId = null;
        $this->studentSearch = '';
        $this->examId = null;
        $this->rollNumber = null;
        $this->examRollNumber = null;
        $this->reportingTime = '08:30';
        $this->examCenter = '';
        $this->examCenterAddress = '';
        $this->instructions = '';
        $this->seatNumber = null;
        $this->roomNumber = null;
        $this->status = 'active';
        $this->allowedItems = ['Admit Card', 'Blue/Black Ball Pen', 'Pencil', 'Eraser'];
        $this->prohibitedItems = ['Mobile Phone', 'Smart Watch', 'Books/Notes'];

        // Reset subjects
        $this->subjects = [
            [
                'subject_id' => '',
                'subject_name' => '',
                'exam_date' => now()->addDays(7)->format('Y-m-d'),
                'exam_time' => '09:00',
                'exam_duration' => '3 Hours',
            ]
        ];
    }

    // Add new admit card
    public function addAdmitCard()
    {
        $this->resetForm();
        $this->admitCardNumber = ModelAdmitCard::generateAdmitCardNumber(
            Auth::user()->organization_id,
            $this->examId
        );
        $this->showEditModal = true;
    }

    // Edit admit card
    public function editAdmitCard($id)
    {
        $admitCard = ModelAdmitCard::with(['studentDetail', 'exam'])->find($id);

        if ($admitCard) {
            $this->admitCardId = $admitCard->id;
            $this->admitCardNumber = $admitCard->admit_card_number;
            $this->studentId = $admitCard->student_detail_id;
            $this->examId = $admitCard->exam_id;
            $this->rollNumber = $admitCard->roll_number;
            $this->examRollNumber = $admitCard->exam_roll_number;
            $this->reportingTime = $admitCard->reporting_time?->format('H:i') ?? '08:30';
            $this->examCenter = $admitCard->exam_center;
            $this->examCenterAddress = $admitCard->exam_center_address;
            $this->instructions = $admitCard->instructions;
            $this->seatNumber = $admitCard->seat_number;
            $this->roomNumber = $admitCard->room_number;
            $this->status = $admitCard->status;
            $this->allowedItems = $admitCard->allowed_items ?? [];
            $this->prohibitedItems = $admitCard->prohibited_items ?? [];

            // Load subjects
            $this->subjects = $admitCard->subjects ?? [
                [
                    'subject_id' => '',
                    'subject_name' => '',
                    'exam_date' => now()->addDays(7)->format('Y-m-d'),
                    'exam_time' => '09:00',
                    'exam_duration' => '3 Hours',
                ]
            ];

            $student = $admitCard->studentDetail;
            $this->studentSearch = $student ? $student->full_name . ' (' . $student->admission_no . ')' : '';

            $this->showEditModal = true;
        }
    }

    // View admit card
    public function showAdmitCard($id)
    {
        $this->viewAdmitCard = ModelAdmitCard::with([
            'studentDetail',
            'studentDetail.standard',
            'studentDetail.section',
            'exam',
            'organization'
        ])->where('id', $id)
            ->where('organization_id', Auth::user()->organization_id)
            ->first();

        if ($this->viewAdmitCard) {
            // Generate QR code if not exists
            if (!$this->viewAdmitCard->qr_code) {
                $qrCode = $this->generateQrCode($this->viewAdmitCard);
                if ($qrCode) {
                    $this->viewAdmitCard->update(['qr_code' => $qrCode]);
                }
            }
            $this->showViewModal = true;
        } else {
            $this->notification()->error(
                $title = 'Error!',
                $description = 'Admit Card not found!'
            );
        }
    }

    // Close view modal
    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewAdmitCard = null;
    }

    // Generate QR Code
    private function generateQrCode($admitCard)
    {
        try {
            $qrData = [
                'admit_card' => [
                    'number' => $admitCard->admit_card_number,
                    'issue_date' => $admitCard->issue_date?->format('Y-m-d'),
                    'status' => $admitCard->status,
                ],
                'student' => [
                    'id' => $admitCard->studentDetail->id,
                    'full_name' => $admitCard->studentDetail->full_name,
                    'admission_no' => $admitCard->studentDetail->admission_no,
                    'roll_number' => $admitCard->roll_number,
                    'exam_roll_number' => $admitCard->exam_roll_number,
                ],
                'exam' => [
                    'name' => $admitCard->exam_name,
                    'subjects' =>  $admitCard->subjects,
                    'duration' => $admitCard->exam_duration,
                    'center' => $admitCard->exam_center,
                ],
                'academic' => [
                    'class' => $admitCard->studentDetail->standard->name ?? null,
                    'section' => $admitCard->studentDetail->section->name ?? null,
                ],
                'organization' => [
                    'name' => $admitCard->organization->name,
                    'address' => $admitCard->organization->address,
                ],
                'verification' => [
                    'timestamp' => now()->timestamp,
                    'verification_url' => route('admit-card.verify', $admitCard->admit_card_number),
                ]
            ];

            $jsonData = json_encode($qrData, JSON_PRETTY_PRINT);

            if (class_exists('SimpleSoftwareIO\QrCode\QrCode')) {
                $qrCode = QrCode::format('png')
                    ->size(250)
                    ->margin(2)
                    ->errorCorrection('H')
                    ->encoding('UTF-8')
                    ->generate($jsonData);

                return base64_encode($qrCode);
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Admit Card QR Code Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    // Save admit card
    public function saveAdmitCard()
    {
        $this->validate();

        try {
            $organization = Auth::user()->organization;
            $student = StudentDetail::with(['standard', 'section'])->find($this->studentId);
            $exam = Exam::find($this->examId);

            if (!$student || !$exam) {
                throw new \Exception('Student or Exam not found');
            }

            $data = [
                'student_detail_id' => $this->studentId,
                'exam_id' => $this->examId,
                'organization_id' => $organization->id,
                'admit_card_number' => $this->admitCardNumber,
                'student_name' => $student->full_name,
                'father_name' => $student->father_name,
                'mother_name' => $student->mother_name,
                'roll_number' => $this->rollNumber,
                'exam_roll_number' => $this->examRollNumber,
                'standard_id' => $student->standard_id,
                'section_id' => $student->section_id,
                'exam_name' => $exam->exam_name,
                'academic_year' => $exam->academic_year,
                'reporting_time' => $this->reportingTime,
                'exam_center' => $this->examCenter,
                'exam_center_address' => $this->examCenterAddress,
                'instructions' => $this->instructions,
                'seat_number' => $this->seatNumber,
                'room_number' => $this->roomNumber,
                'allowed_items' => $this->allowedItems,
                'prohibited_items' => $this->prohibitedItems,
                'subjects' => $this->subjects,
                'status' => $this->status,
                'issue_date' => now(),
                'created_by' => Auth::id(),
            ];

            if ($this->admitCardId) {
                $admitCard = ModelAdmitCard::find($this->admitCardId);
                $admitCard->update($data);
                $admitCard->updated_by = Auth::id();
                $admitCard->save();

                $this->notification()->success(
                    $title = 'Success!',
                    $description = 'Admit Card updated successfully!'
                );
            } else {
                // Check if student already has admit card for this exam
                $existingCard = ModelAdmitCard::where('student_detail_id', $this->studentId)
                    ->where('exam_id', $this->examId)
                    ->where('status', 'active')
                    ->first();

                if ($existingCard) {
                    $this->notification()->warning(
                        $title = 'Warning!',
                        $description = 'Student already has an active admit card for this exam!'
                    );
                    return;
                }

                $admitCard = ModelAdmitCard::create($data);

                // Generate QR code
                $qrCode = $this->generateQrCode($admitCard);
                if ($qrCode) {
                    $admitCard->update(['qr_code' => $qrCode]);
                }

                $this->notification()->success(
                    $title = 'Success!',
                    $description = 'Admit Card created successfully!'
                );
            }

            $this->closeEditModal();
            $this->resetForm();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->notification()->error(
                $title = 'Error!',
                $description = 'Something went wrong: ' . $e->getMessage()
            );
        }
    }

    // Close edit modal
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    // Open bulk generation modal
    public function openBulkGenerate()
    {
        $this->showBulkGenerateModal = true;
    }

    // Close bulk modal
    public function closeBulkModal()
    {
        $this->showBulkGenerateModal = false;
        $this->selectedExam = null;
        $this->selectedStandard = null;
        $this->selectedSection = null;
        $this->selectedStudents = [];
        $this->bulkInstructions = '';
        $this->bulkExamCenter = '';
        $this->bulkExamCenterAddress = '';
        $this->bulkSubjects = [
            [
                'subject_id' => '',
                'subject_name' => '',
                'exam_date' => now()->addDays(7)->format('Y-m-d'),
                'exam_time' => '09:00',
                'exam_duration' => '3 Hours',
            ]
        ];
        $this->resetValidation();
    }

    // Reset section when standard changes
    public function updatedSelectedStandard()
    {
        $this->selectedSection = null;
        $this->selectedStudents = [];
    }

    // CRITICAL FIX: Livewire 3 - Use #[Computed] attribute
    #[\Livewire\Attributes\Computed]
    public function availableStudentsForBulk()
    {
        // If exam or standard not selected, return empty collection
        if (!$this->selectedExam || !$this->selectedStandard) {
            return collect();
        }

        $query = StudentDetail::with(['standard', 'section'])
            ->where('organization_id', Auth::user()->organization_id)
            ->where('standard_id', $this->selectedStandard);

        // Filter by section if selected
        if ($this->selectedSection) {
            $query->where('section_id', $this->selectedSection);
        }

        // CRITICAL FIX: Exclude students who already have admit card for this exam
        $query->whereDoesntHave('admitCards', function ($q) {
            $q->where('exam_id', $this->selectedExam);
        });

        $students = $query->get();

        // Debug log
        \Log::info('Available Students Query', [
            'exam_id' => $this->selectedExam,
            'standard_id' => $this->selectedStandard,
            'section_id' => $this->selectedSection,
            'count' => $students->count(),
            'students' => $students->pluck('full_name')
        ]);

        return $students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'admission_no' => $student->admission_no,
                'roll_no' => $student->roll_no ?? 'N/A',
                'class' => $student->standard->name ?? 'N/A',
                'section' => $student->section->name ?? '',
                'selected' => in_array($student->id, $this->selectedStudents)
            ];
        });
    }

    // Get subjects property - Livewire 3
    #[\Livewire\Attributes\Computed]
    public function availableSubjects()
    {
        return Subject::where('organization_id', Auth::user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // Get sections for selected standard - Livewire 3
    #[\Livewire\Attributes\Computed]
    public function sections()
    {
        if (!$this->selectedStandard) {
            return collect();
        }

        return Section::where('standard_id', $this->selectedStandard)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // Select/Deselect all students - Livewire 3
    public function toggleAllStudents($select)
    {
        if ($select) {
            $this->selectedStudents = $this->availableStudentsForBulk->pluck('id')->toArray();
        } else {
            $this->selectedStudents = [];
        }
    }

    // Bulk generate admit cards
    public function bulkGenerateAdmitCards()
    {
        $this->validate([
            'selectedExam' => 'required|exists:exams,id',
            'selectedStandard' => 'required|exists:standards,id',
            'bulkExamCenter' => 'required|string|max:255',
            'bulkExamCenterAddress' => 'required|string',
            'selectedStudents' => 'required|array|min:1',
            'bulkSubjects' => 'required|array|min:1',
            'bulkSubjects.*.subject_id' => 'required|exists:subjects,id',
            'bulkSubjects.*.subject_name' => 'required|string|max:100',
            'bulkSubjects.*.exam_date' => 'required|date',
            'bulkSubjects.*.exam_time' => 'required|date_format:H:i',
            'bulkSubjects.*.exam_duration' => 'required|string|max:50',
        ]);

        try {
            $organization = Auth::user()->organization;
            $exam = Exam::find($this->selectedExam);
            $students = StudentDetail::with(['standard', 'section'])
                ->whereIn('id', $this->selectedStudents)
                ->get();

            if ($students->isEmpty()) {
                throw new \Exception('No students selected');
            }

            $generatedCount = 0;
            $errors = [];

            foreach ($students as $student) {
                try {
                    // Check if already has admit card for this exam
                    $existingCard = ModelAdmitCard::where('student_detail_id', $student->id)
                        ->where('exam_id', $this->selectedExam)
                        ->first();

                    if ($existingCard) {
                        $errors[] = "Student {$student->full_name} already has an admit card for this exam";
                        continue;
                    }

                    $admitCardNumber = ModelAdmitCard::generateAdmitCardNumber(
                        $organization->id,
                        $this->selectedExam
                    );

                    $admitCard = ModelAdmitCard::create([
                        'student_detail_id' => $student->id,
                        'exam_id' => $this->selectedExam,
                        'organization_id' => $organization->id,
                        'admit_card_number' => $admitCardNumber,
                        'student_name' => $student->full_name,
                        'father_name' => $student->father_name,
                        'mother_name' => $student->mother_name,
                        'roll_number' => $student->roll_no ?? 'N/A',
                        'standard_id' => $student->standard_id,
                        'section_id' => $student->section_id,
                        'exam_name' => $exam->exam_name,
                        'academic_year' => $exam->academic_year,
                        'reporting_time' => '08:30',
                        'exam_center' => $this->bulkExamCenter,
                        'exam_center_address' => $this->bulkExamCenterAddress,
                        'instructions' => $this->bulkInstructions,
                        'allowed_items' => $this->commonAllowedItems,
                        'prohibited_items' => $this->commonProhibitedItems,
                        'subjects' => $this->bulkSubjects,
                        'status' => 'active',
                        'issue_date' => now(),
                        'created_by' => Auth::id(),
                    ]);

                    // Generate QR code
                    $qrCode = $this->generateQrCode($admitCard);
                    if ($qrCode) {
                        $admitCard->update(['qr_code' => $qrCode]);
                    }

                    $generatedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Student {$student->full_name}: " . $e->getMessage();
                    \Log::error('Bulk Admit Card Generation Error', [
                        'student_id' => $student->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->closeBulkModal();

            if ($generatedCount > 0) {
                $this->notification()->success(
                    $title = 'Success!',
                    $description = "Successfully generated {$generatedCount} admit cards!"
                );
            }

            if (!empty($errors)) {
                $this->notification()->warning(
                    $title = 'Some errors occurred',
                    $description = implode('<br>', array_slice($errors, 0, 5))
                );
            }
        } catch (\Exception $e) {
            $this->notification()->error(
                $title = 'Error!',
                $description = 'Failed to generate admit cards: ' . $e->getMessage()
            );
            \Log::error('Bulk Generation Failed', ['error' => $e->getMessage()]);
        }
    }

    // Search students
    public function updatedStudentSearch()
    {
        // This will automatically update the student list
    }

    // Get available students - Livewire 3
    #[\Livewire\Attributes\Computed]
    public function availableStudents()
    {
        if (strlen($this->studentSearch) < 2) {
            return collect();
        }

        return StudentDetail::where('organization_id', Auth::user()->organization_id)
            ->where(function ($query) {
                $query->where('full_name', 'like', '%' . $this->studentSearch . '%')
                    ->orWhere('admission_no', 'like', '%' . $this->studentSearch . '%')
                    ->orWhere('roll_no', 'like', '%' . $this->studentSearch . '%');
            })
            ->limit(10)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'text' => $student->full_name . ' (' . $student->admission_no . ')',
                    'admission_no' => $student->admission_no,
                    'roll_no' => $student->roll_no,
                    'class' => $student->standard->name ?? 'N/A',
                    'section' => $student->section->name ?? '',
                ];
            });
    }

    // Select student
    public function selectStudent($studentId)
    {
        $this->studentId = $studentId;
        $student = StudentDetail::find($studentId);

        if ($student) {
            $this->studentSearch = $student->full_name . ' (' . $student->admission_no . ')';
            $this->rollNumber = $student->roll_no;

            // Auto-generate admit card number if not editing
            if (!$this->admitCardId && $this->examId) {
                $this->admitCardNumber = ModelAdmitCard::generateAdmitCardNumber(
                    Auth::user()->organization_id,
                    $this->examId
                );
            }
        }
    }

    // Confirm delete
    public function confirmDelete($id)
    {
        $this->admitCardId = $id;
        $this->showDeleteModal = true;
    }

    // Close delete modal
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->admitCardId = null;
    }

    // Delete admit card
    public function deleteAdmitCard()
    {
        try {
            $admitCard = ModelAdmitCard::find($this->admitCardId);

            if ($admitCard) {
                $admitCard->delete();
                $this->notification()->success(
                    $title = 'Deleted!',
                    $description = 'Admit Card deleted successfully!'
                );
            }
        } catch (\Exception $e) {
            $this->notification()->error(
                $title = 'Error!',
                $description = 'Failed to delete admit card: ' . $e->getMessage()
            );
        } finally {
            $this->closeDeleteModal();
        }
    }

    // Reset filters
    public function resetFilters()
    {
        $this->reset(['search', 'examFilter', 'standardFilter', 'statusFilter']);
        $this->resetPage();
    }

    // Get exams property - Livewire 3
    #[\Livewire\Attributes\Computed]
    public function exams()
    {
        return Exam::where('organization_id', Auth::user()->organization_id)
            ->where('is_published', true)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    // Get standards property - Livewire 3
    #[\Livewire\Attributes\Computed]
    public function standards()
    {
        return Standard::where('organization_id', Auth::user()->organization_id)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        $query = ModelAdmitCard::with([
            'studentDetail',
            'studentDetail.standard',
            'studentDetail.section',
            'exam',
            'organization'
        ])->where('organization_id', Auth::user()->organization_id);

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('admit_card_number', 'like', '%' . $this->search . '%')
                    ->orWhere('student_name', 'like', '%' . $this->search . '%')
                    ->orWhere('roll_number', 'like', '%' . $this->search . '%')
                    ->orWhere('exam_roll_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('studentDetail', function ($q2) {
                        $q2->where('full_name', 'like', '%' . $this->search . '%')
                            ->orWhere('admission_no', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->examFilter) {
            $query->where('exam_id', $this->examFilter);
        }

        if ($this->standardFilter) {
            $query->where('standard_id', $this->standardFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $admitCards = $query->latest()->paginate($this->perPage);

        return view('livewire.admin.admit-card', [
            'admitCards' => $admitCards,
        ]);
    }
}
