<?php

namespace App\Livewire\Admin;

use App\Models\Admin\StudentIdCard;
use App\Models\Admin\TeacherIdCard;
use App\Models\Student\StudentDetail;
use App\Models\Teacher\TeacherDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class IdCard extends Component
{
    use WithPagination, WireUiActions;

    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showBulkGenerateModal = false;
    public $showViewModal = false;
    public $search = '';

    // Card type toggle
    public $cardType = 'student'; // 'student' or 'teacher'

    public $cardId;
    public $cardNumber;
    public $expiryDate;
    public $status = 'active';
    public $personSearch = '';
    public $personId;
    public $viewCard = null;

    // Bulk generation
    public $validityMonths = 12;
    public $cardPrefix = 'ID';
    public $perPage = 10;

    protected function rules()
    {
        $rules = [
            'cardNumber' => 'required|string|max:50',
            'expiryDate' => 'required|date',
            'status' => 'required|in:active,inactive',
            'personId' => 'required'
        ];

        // Add unique validation based on card type
        if (!$this->cardId) {
            if ($this->cardType === 'student') {
                $rules['cardNumber'] .= '|unique:student_id_cards,card_number';
                $rules['personId'] .= '|exists:student_details,id';
            } else {
                $rules['cardNumber'] .= '|unique:teacher_id_cards,card_number';
                $rules['personId'] .= '|exists:teacher_details,id';
            }
        } else {
            $table = $this->cardType === 'student' ? 'student_id_cards' : 'teacher_id_cards';
            $rules['cardNumber'] .= "|unique:{$table},card_number,{$this->cardId}";

            if ($this->cardType === 'student') {
                $rules['personId'] .= '|exists:student_details,id';
            } else {
                $rules['personId'] .= '|exists:teacher_details,id';
            }
        }

        return $rules;
    }

    // Switch card type
    public function switchCardType($type)
    {
        $this->cardType = $type;
        $this->resetFilters();
        $this->resetForm();
    }

    // Reset form
    public function resetForm()
    {
        $this->cardId = null;
        $this->cardNumber = null;
        $this->expiryDate = null;
        $this->status = 'active';
        $this->personId = null;
        $this->personSearch = '';
    }

    // Add new card
    public function addCard()
    {
        $this->resetForm();
        $this->showEditModal = true;
    }

    // Edit card
    public function editCard($id)
    {
        if ($this->cardType === 'student') {
            $card = StudentIdCard::with('studentDetail')->find($id);
            if ($card) {
                $this->cardId = $card->id;
                $this->cardNumber = $card->card_number;
                $this->expiryDate = $card->expiry_date->format('Y-m-d');
                $this->status = $card->status;
                $this->personId = $card->student_detail_id;
                $person = $card->studentDetail;
                $this->personSearch = $person ? $person->full_name . ' (' . $person->admission_no . ')' : '';
                $this->showEditModal = true;
            }
        } else {
            $card = TeacherIdCard::with('teacherDetail.user')->find($id);
            if ($card) {
                $this->cardId = $card->id;
                $this->cardNumber = $card->card_number;
                $this->expiryDate = $card->expiry_date->format('Y-m-d');
                $this->status = $card->status;
                $this->personId = $card->teacher_detail_id;
                $person = $card->teacherDetail;
                $this->personSearch = $person ? ($person->user->name ?? 'N/A') . ' (' . $person->employee_id . ')' : '';
                $this->showEditModal = true;
            }
        }
    }

    // View card
    public function showCard($id)
    {
        if ($this->cardType === 'student') {
            $this->viewCard = StudentIdCard::with([
                'studentDetail',
                'studentDetail.standard',
                'studentDetail.section',
                'organization'
            ])->where('id', $id)
                ->where('organization_id', Auth::user()->organization_id)
                ->first();

            if ($this->viewCard) {
                if (!$this->viewCard->qr_code) {
                    $qrCode = $this->generateQrCode($this->viewCard, $this->viewCard->studentDetail, $this->viewCard->organization, 'student');
                    if ($qrCode) {
                        $this->viewCard->update(['qr_code' => $qrCode]);
                    }
                }
                $this->showViewModal = true;
            }
        } else {
            $this->viewCard = TeacherIdCard::with([
                'teacherDetail.user',
                'teacherDetail.assignedSubjects.subject',
                'teacherDetail.assignedClasses.standard',
                'teacherDetail.assignedClasses.section',
                'organization'
            ])->where('id', $id)
                ->where('organization_id', Auth::user()->organization_id)
                ->first();

            if ($this->viewCard) {
                if (!$this->viewCard->qr_code) {
                    $qrCode = $this->generateQrCode($this->viewCard, $this->viewCard->teacherDetail, $this->viewCard->organization, 'teacher');
                    if ($qrCode) {
                        $this->viewCard->update(['qr_code' => $qrCode]);
                    }
                }
                $this->showViewModal = true;
            }
        }

        if (!$this->viewCard) {
            $this->notification()->error(
                $title = 'Error!',
                $description = 'Card not found!'
            );
        }
    }

    // Close view modal
    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewCard = null;
    }

    // Generate QR Code
    private function generateQrCode($card, $person, $organization, $type)
    {
        try {
            $qrData = [
                'card' => [
                    'number' => $card->card_number,
                    'issue_date' => $card->issue_date->format('Y-m-d'),
                    'expiry_date' => $card->expiry_date->format('Y-m-d'),
                    'status' => $card->status,
                    'type' => $type,
                ],
                'organization' => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'address' => $organization->address,
                    'phone' => $organization->phone,
                ],
                'verification' => [
                    'timestamp' => now()->timestamp,
                    'verification_url' => route($type . '.verify', $card->card_number),
                ]
            ];

            if ($type === 'student') {
                $qrData['student'] = [
                    'id' => $person->id,
                    'full_name' => $person->full_name,
                    'admission_no' => $person->admission_no,
                    'dob' => $person->dob ? \Carbon\Carbon::parse($person->dob)->format('Y-m-d') : null,
                    'email' => $person->email,
                    'phone' => $person->phone,
                    'address' => $person->address,
                    'father_name' => $person->father_name,
                    'mother_name' => $person->mother_name,
                ];
                $qrData['academic'] = [
                    'class' => $person->standard->name ?? null,
                    'section' => $person->section->name ?? null,
                    'roll_number' => $person->roll_no ?? null,
                ];
            } else {
                $assignedClasses = $person->assignedClasses->map(function ($class) {
                    return ($class->standard->name ?? '') . ' ' . ($class->section->name ?? '');
                })->filter()->implode(', ');

                $qrData['teacher'] = [
                    'id' => $person->id,
                    'full_name' => $person->user->name ?? 'N/A',
                    'employee_id' => $person->employee_id,
                    'email' => $person->user->email ?? null,
                    'phone' => $person->phone,
                    'address' => $person->address,
                    'qualification' => $person->qualification ?? null,
                    'joining_date' => $person->date_of_joining ? \Carbon\Carbon::parse($person->date_of_joining)->format('Y-m-d') : null,
                    'assigned_classes' => $assignedClasses ?: null,
                ];
            }

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

            $encodedData = urlencode($jsonData);
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={$encodedData}&margin=2&ecc=H";

            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $imageContent = @file_get_contents($qrCodeUrl, false, $context);

            if ($imageContent !== false) {
                return base64_encode($imageContent);
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('QR Code Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    // Save card
    public function saveCard()
    {
        $this->validate();

        try {
            $organization = Auth::user()->organization;

            if (!$organization) {
                throw new \Exception('Organization not found');
            }

            $data = [
                'card_number' => $this->cardNumber,
                'expiry_date' => $this->expiryDate,
                'status' => $this->status,
                'organization_id' => $organization->id,
                'issue_date' => now(),
                'user_id' => Auth::id(),
            ];

            if ($this->cardType === 'student') {
                $person = StudentDetail::with(['standard', 'section'])->find($this->personId);
                $data['student_detail_id'] = $this->personId;
                $model = StudentIdCard::class;
                $existingCardQuery = StudentIdCard::where('student_detail_id', $this->personId);
            } else {
                $person = TeacherDetail::with(['user', 'assignedClasses.standard', 'assignedClasses.section'])->find($this->personId);
                $data['teacher_detail_id'] = $this->personId;
                $model = TeacherIdCard::class;
                $existingCardQuery = TeacherIdCard::where('teacher_detail_id', $this->personId);
            }

            if (!$person) {
                throw new \Exception(ucfirst($this->cardType) . ' not found');
            }

            if ($this->cardId) {
                $card = $model::find($this->cardId);
                $card->update($data);

                $qrCodeBase64 = $this->generateQrCode($card, $person, $organization, $this->cardType);
                if ($qrCodeBase64) {
                    $card->update(['qr_code' => $qrCodeBase64]);
                }

                $this->notification()->success(
                    $title = 'Success!',
                    $description = 'ID Card updated successfully!'
                );
            } else {
                $existingCard = $existingCardQuery->where('status', 'active')->first();

                if ($existingCard) {
                    $this->notification()->warning(
                        $title = 'Warning!',
                        $description = ucfirst($this->cardType) . ' already has an active ID card!'
                    );
                    return;
                }

                $card = $model::create($data);

                $qrCodeBase64 = $this->generateQrCode($card, $person, $organization, $this->cardType);
                if ($qrCodeBase64) {
                    $card->update(['qr_code' => $qrCodeBase64]);
                }

                $this->notification()->success(
                    $title = 'Success!',
                    $description = 'ID Card created successfully!'
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
        $this->cardPrefix = 'ID';
        $this->validityMonths = 12;
        $this->resetValidation();
    }

    // Generate unique card number
    private function generateCardNumber($person)
    {
        $orgId = Auth::user()->organization_id;
        $year = now()->format('y');
        $random = mt_rand(1000, 9999);
        $typePrefix = $this->cardType === 'student' ? 'STU' : 'TCH';

        $baseNumber = "{$this->cardPrefix}{$typePrefix}{$orgId}{$year}{$person->id}{$random}";

        $counter = 1;
        $cardNumber = $baseNumber;

        $model = $this->cardType === 'student' ? StudentIdCard::class : TeacherIdCard::class;

        while ($model::where('card_number', $cardNumber)->exists()) {
            $cardNumber = "{$baseNumber}-{$counter}";
            $counter++;
        }

        return $cardNumber;
    }

    // Bulk generate ID cards
    public function bulkGenerateCards()
    {
        $this->validate([
            'validityMonths' => 'required|integer|min:1|max:60',
            'cardPrefix' => 'required|string|max:10'
        ]);

        try {
            $organization = Auth::user()->organization;

            if ($this->cardType === 'student') {
                $personsWithoutCards = StudentDetail::with(['standard', 'section'])
                    ->where('organization_id', $organization->id)
                    ->whereDoesntHave('idCards', function ($query) {
                        $query->where('status', 'active');
                    })
                    ->get();
                $model = StudentIdCard::class;
                $foreignKey = 'student_detail_id';
            } else {
                $personsWithoutCards = TeacherDetail::with(['user', 'assignedClasses.standard', 'assignedClasses.section'])
                    ->where('organization_id', $organization->id)
                    ->whereDoesntHave('idCards', function ($query) {
                        $query->where('status', 'active');
                    })
                    ->get();
                $model = TeacherIdCard::class;
                $foreignKey = 'teacher_detail_id';
            }

            if ($personsWithoutCards->isEmpty()) {
                $this->notification()->info(
                    $title = 'No Cards Needed',
                    $description = 'All ' . $this->cardType . 's already have active ID cards!'
                );
                return;
            }

            $generatedCount = 0;
            $errors = [];

            foreach ($personsWithoutCards as $person) {
                try {
                    $cardNumber = $this->generateCardNumber($person);

                    $card = $model::create([
                        'card_number' => $cardNumber,
                        $foreignKey => $person->id,
                        'organization_id' => $organization->id,
                        'user_id' => Auth::id(),
                        'issue_date' => now(),
                        'expiry_date' => now()->addMonths($this->validityMonths),
                        'status' => 'active',
                    ]);

                    $qrCode = $this->generateQrCode($card, $person, $organization, $this->cardType);
                    if ($qrCode) {
                        $card->update(['qr_code' => $qrCode]);
                    }

                    $generatedCount++;
                } catch (\Exception $e) {
                    $personName = $this->cardType === 'student'
                        ? $person->full_name
                        : ($person->user->name ?? 'Unknown');
                    $errors[] = ucfirst($this->cardType) . " {$personName}: " . $e->getMessage();
                }
            }

            $this->closeBulkModal();

            if ($generatedCount > 0) {
                $this->notification()->success(
                    $title = 'Success!',
                    $description = "Successfully generated {$generatedCount} ID cards!"
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
                $description = 'Failed to generate cards: ' . $e->getMessage()
            );
        }
    }

    // Search persons
    public function updatedPersonSearch()
    {
        // This will automatically update the person list
    }

    // Get available persons (without active cards)
    public function getAvailablePersonsProperty()
    {
        if (strlen($this->personSearch) < 2) {
            return collect();
        }

        if ($this->cardType === 'student') {
            return StudentDetail::where('organization_id', Auth::user()->organization_id)
                ->where(function ($query) {
                    $query->where('full_name', 'like', '%' . $this->personSearch . '%')
                        ->orWhere('admission_no', 'like', '%' . $this->personSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->personSearch . '%');
                })
                ->whereDoesntHave('idCards', function ($query) {
                    if ($this->cardId) {
                        $query->where('status', 'active')->where('id', '!=', $this->cardId);
                    } else {
                        $query->where('status', 'active');
                    }
                })
                ->limit(10)
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'text' => $student->full_name . ' (' . $student->admission_no . ')',
                        'identifier' => $student->admission_no,
                        'info' => 'Class: ' . ($student->standard->name ?? 'N/A') .
                            ($student->section ? ' | Section: ' . $student->section->name : '')
                    ];
                });
        } else {
            return TeacherDetail::with(['user', 'assignedClasses.standard', 'assignedClasses.section'])
                ->where('organization_id', Auth::user()->organization_id)
                ->where(function ($query) {
                    $query->where('employee_id', 'like', '%' . $this->personSearch . '%')
                        ->orWhere('phone', 'like', '%' . $this->personSearch . '%')
                        ->orWhereHas('user', function ($q) {
                            $q->where('name', 'like', '%' . $this->personSearch . '%')
                                ->orWhere('email', 'like', '%' . $this->personSearch . '%');
                        });
                })
                ->whereDoesntHave('idCards', function ($query) {
                    if ($this->cardId) {
                        $query->where('status', 'active')->where('id', '!=', $this->cardId);
                    } else {
                        $query->where('status', 'active');
                    }
                })
                ->limit(10)
                ->get()
                ->map(function ($teacher) {
                    $assignedClasses = $teacher->assignedClasses->map(function ($class) {
                        return ($class->standard->name ?? '') . ' ' . ($class->section->name ?? '');
                    })->filter()->implode(', ');

                    return [
                        'id' => $teacher->id,
                        'text' => ($teacher->user->name ?? 'N/A') . ' (' . $teacher->employee_id . ')',
                        'identifier' => $teacher->employee_id,
                        'info' => ($assignedClasses ?: 'No classes assigned') .
                            ($teacher->qualification ? ' | ' . $teacher->qualification : '')
                    ];
                });
        }
    }

    // Select person
    public function selectPerson($personId)
    {
        $this->personId = $personId;

        if ($this->cardType === 'student') {
            $person = StudentDetail::find($personId);
            $this->personSearch = $person ? $person->full_name . ' (' . $person->admission_no . ')' : '';
        } else {
            $person = TeacherDetail::with('user')->find($personId);
            $this->personSearch = $person ? ($person->user->name ?? 'N/A') . ' (' . $person->employee_id . ')' : '';
        }

        if (!$this->cardId && $person) {
            $this->cardNumber = $this->generateCardNumber($person);
        }
    }

    // Confirm delete
    public function confirmDelete($id)
    {
        $this->cardId = $id;
        $this->showDeleteModal = true;
    }

    // Close delete modal
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->cardId = null;
    }

    // Delete card
    public function deleteCard()
    {
        try {
            $model = $this->cardType === 'student' ? StudentIdCard::class : TeacherIdCard::class;
            $card = $model::find($this->cardId);

            if ($card) {
                $card->delete();
                $this->notification()->success(
                    $title = 'Deleted!',
                    $description = 'ID Card deleted successfully!'
                );
            }
        } catch (\Exception $e) {
            $this->notification()->error(
                $title = 'Error!',
                $description = 'Failed to delete card: ' . $e->getMessage()
            );
        } finally {
            $this->closeDeleteModal();
        }
    }

    // Reset filters
    public function resetFilters()
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function render()
    {
        if ($this->cardType === 'student') {
            $query = StudentIdCard::with(['studentDetail', 'studentDetail.standard', 'studentDetail.section', 'organization'])
                ->where('organization_id', Auth::user()->organization_id);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('card_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('studentDetail', function ($q2) {
                            $q2->where('full_name', 'like', '%' . $this->search . '%')
                                ->orWhere('admission_no', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            }
        } else {
            $query = TeacherIdCard::with(['teacherDetail.user', 'teacherDetail.assignedClasses.standard', 'teacherDetail.assignedClasses.section', 'organization'])
                ->where('organization_id', Auth::user()->organization_id);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('card_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('teacherDetail', function ($q2) {
                            $q2->where('employee_id', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%')
                                ->orWhereHas('user', function ($q3) {
                                    $q3->where('name', 'like', '%' . $this->search . '%')
                                        ->orWhere('email', 'like', '%' . $this->search . '%');
                                });
                        });
                });
            }
        }

        $cards = $query->latest()->paginate($this->perPage);

        return view('livewire.admin.id-card', [
            'cards' => $cards,
        ]);
    }
}
