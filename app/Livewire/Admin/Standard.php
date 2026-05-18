<?php

namespace App\Livewire\Admin;

use App\Models\Student\Section;
use App\Models\Student\SectionSubject;
use App\Models\Student\Standard as StudentStandard;
use App\Models\Student\StandardSubject;
use App\Models\Student\Subject;
use App\Models\Teacher\TeacherAssignment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class Standard extends Component
{
    use WireUiActions, WithFileUploads;

    public $openStandard = false;
    public $openSection  = false;
    public $openSubject  = false;
    public $editId       = null;
    public $standards, $sections, $subjects;

    // Standard fields
    public $standardName   = '';
    public $standardCode   = '';
    public $standardBoard  = '';
    public $standardOrder  = '';
    public $standardActive = true;

    // View modal
    public $showViewModal  = false;
    public $viewModalTitle = '';
    public $viewData       = [];
    public $activeTab      = 'standard';

    // Search / filter
    public $search               = '';
    public $filterBoard          = '';
    public $filterStandard       = '';
    public $filterStatus         = '';
    public $filterSubjectStandard = '';
    public $perPage              = 25;

    // Section fields
    public $sectionName        = '';
    public $sectionDescription = '';
    public $sectionActive      = true;
    public $selectedStandard   = null;
    public $filterSection      = '';

    // Subject fields
    public $subjectName, $subjectCode, $subjectDescription;
    public $subjectActive                = true;
    public $selectedStandardForSubject   = null;
    public $selectedSectionsForSubject   = [];
    public $isMandatory                  = true;
    public $existingSubjects             = [];
    public $subjectImage;
    public $subjectDetailImage;
    public $subjectImageUrl, $subjectDetailImageUrl;
    public $subjectImagePreview          = null;
    public $subjectDetailImagePreview    = null;

    protected $listeners = [
        'onViewStandardAdmin',
        'onEditStandard',
        'onDeleteStandard',
        'onViewSectionAdmin',
        'onDeleteSection',
        'onEditSection',
        'onEditSubject',
        'onDeleteSubject',
        'onViewSubjectAdmin',
    ];

    public function mount(): void
    {
        $this->loadStandards();
        $this->loadAllSections();
        $this->loadAllSubjects();
        $this->selectedSectionsForSubject = [];
    }

    // Watch file uploads to generate previews
    public function updatedSubjectImage(): void
    {
        $this->validate(['subjectImage' => 'nullable|image|max:2048']);
        $this->subjectImagePreview = $this->subjectImage?->temporaryUrl();
    }

    public function updatedSubjectDetailImage(): void
    {
        $this->validate(['subjectDetailImage' => 'nullable|image|max:2048']);
        $this->subjectDetailImagePreview = $this->subjectDetailImage?->temporaryUrl();
    }

    public function updated($property): void
    {
        if ($property === 'selectedStandardForSubject' && $this->selectedStandardForSubject) {
            $this->loadSectionsForSelectedStandard();
            $this->loadExistingSubjectsForStandard();
        }
        if ($property === 'filterSubjectStandard') {
            $this->filterSection = '';
        }
    }

    public function updating($name, $value): void
    {
        if (in_array($name, ['search', 'filterBoard', 'filterStandard', 'filterStatus', 'filterSection', 'filterSubjectStandard', 'perPage'])) {
            $this->closeModal();
        }
    }

    public function showTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->closeModal();
        $this->resetFilters();
    }

    public function drillIntoClass(int $standardId): void
    {
        $this->activeTab     = 'section';
        $this->filterStandard = $standardId;
        $this->search        = '';
        $this->filterStatus  = '';
    }

    public function drillIntoSection(int $sectionId): void
    {
        $section = Section::find($sectionId);
        if ($section) {
            $this->activeTab             = 'subject';
            $this->filterSubjectStandard = $section->standard_id;
            $this->filterSection         = $sectionId;
            $this->search                = '';
            $this->filterStatus          = '';
        }
    }

    public function loadStandards(): void
    {
        $this->standards = StudentStandard::where('organization_id', Auth::user()->organization_id)
            ->where('is_active', true)->orderBy('order')->get();
    }

    public function loadAllSections(): void
    {
        $this->sections = Section::with('standard')
            ->whereHas('standard', fn($q) => $q->where('organization_id', Auth::user()->organization_id))
            ->where('is_active', true)->orderBy('name')->get();
    }

    public function loadAllSubjects(): void
    {
        $this->subjects = Subject::where('organization_id', Auth::user()->organization_id)
            ->where('is_active', true)->get();
    }

    public function loadSectionsForSelectedStandard(): void
    {
        $this->sections = Section::where('standard_id', $this->selectedStandardForSubject)
            ->where('is_active', true)->orderBy('name')->get();
    }

    public function loadExistingSubjectsForStandard(): void
    {
        if (!$this->selectedStandardForSubject) {
            $this->existingSubjects = [];
            return;
        }
        $this->existingSubjects = StandardSubject::where('standard_id', $this->selectedStandardForSubject)
            ->where('organization_id', Auth::user()->organization_id)
            ->with('subject')
            ->get()
            ->map(fn($ss) => [
                'id'           => $ss->subject->id,
                'name'         => $ss->subject->name,
                'code'         => $ss->subject->code,
                'is_mandatory' => $ss->is_mandatory,
                'sections'     => SectionSubject::where('subject_id', $ss->subject_id)
                    ->where('standard_id', $this->selectedStandardForSubject)
                    ->pluck('section_id')->toArray(),
            ])->toArray();
    }

    // ─── Computed properties ──────────────────────────────────────────────────

    public function getFilteredStandardsProperty()
    {
        $query = StudentStandard::where('organization_id', Auth::user()->organization_id);

        if ($this->search) {
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('board', 'like', "%{$this->search}%")
            );
        }
        if ($this->filterBoard)    $query->where('board', $this->filterBoard);
        if ($this->filterStatus !== '') $query->where('is_active', $this->filterStatus === 'active');

        return $query->orderBy('order')->paginate($this->perPage);
    }

    public function getFilteredSectionsProperty()
    {
        $query = Section::with('standard')
            ->whereHas('standard', fn($q) => $q->where('organization_id', Auth::user()->organization_id));

        if ($this->search) {
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhereHas('standard', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
            );
        }
        if ($this->filterStandard)  $query->where('standard_id', $this->filterStandard);
        if ($this->filterStatus !== '') $query->where('is_active', $this->filterStatus === 'active');

        return $query->orderBy('name')->paginate($this->perPage);
    }

    public function getFilteredSubjectsProperty()
    {
        $query = Subject::with(['standards', 'sections'])
            ->where('organization_id', Auth::user()->organization_id);

        if ($this->search) {
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
            );
        }
        if ($this->filterSubjectStandard) {
            $query->whereHas('standards', fn($q) => $q->where('standard_id', $this->filterSubjectStandard));
        }
        if ($this->filterSection) {
            $query->whereHas('sections', fn($q) => $q->where('section_id', $this->filterSection));
        }
        if ($this->filterStatus !== '') $query->where('is_active', $this->filterStatus === 'active');

        return $query->orderBy('name')->paginate($this->perPage);
    }

    public function getAvailableSectionsProperty()
    {
        $query = Section::with('standard')
            ->whereHas('standard', fn($q) => $q->where('organization_id', Auth::user()->organization_id))
            ->where('is_active', true);

        if ($this->filterSubjectStandard) {
            $query->where('standard_id', $this->filterSubjectStandard);
        }
        return $query->orderBy('name')->get();
    }

    public function getBoardsProperty()
    {
        return StudentStandard::where('organization_id', Auth::user()->organization_id)
            ->distinct()->pluck('board')->filter()->values();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterBoard', 'filterStandard', 'filterStatus',
                      'filterSubjectStandard', 'filterSection']);
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->openStandard = false;
        $this->openSection  = false;
        $this->openSubject  = false;
        $this->reset([
            'editId', 'standardName', 'standardCode', 'standardBoard', 'standardOrder',
            'sectionName', 'sectionDescription', 'selectedStandard',
            'subjectName', 'subjectCode', 'subjectDescription', 'subjectActive',
            'selectedStandardForSubject', 'selectedSectionsForSubject', 'isMandatory',
            'subjectImage', 'subjectDetailImage', 'subjectImageUrl', 'subjectDetailImageUrl',
            'subjectImagePreview', 'subjectDetailImagePreview', 'existingSubjects',
        ]);
        $this->standardActive = true;
        $this->sectionActive = true;
        $this->dispatch('onStandardAddUpdate');
    }

    private function resetStandardFields(): void
    {
        $this->reset(['standardName', 'standardCode', 'standardBoard', 'standardOrder']);
        $this->standardActive = true;
    }

    private function resetSectionFields(): void
    {
        $this->reset(['sectionName', 'sectionDescription', 'selectedStandard']);
        $this->sectionActive = true;
    }

    private function resetSubjectFields(): void
    {
        $this->reset([
            'subjectName', 'subjectCode', 'subjectDescription',
            'selectedStandardForSubject', 'selectedSectionsForSubject', 'isMandatory',
            'subjectImage', 'subjectDetailImage', 'subjectImageUrl', 'subjectDetailImageUrl',
            'subjectImagePreview', 'subjectDetailImagePreview', 'existingSubjects',
        ]);
        $this->subjectActive = true;
    }

    // ─── Open modals ──────────────────────────────────────────────────────────

    public function onStandard(): void
    {
        $this->editId = null;
        $this->resetStandardFields();
        $this->openStandard = true;
    }

    public function onSection(): void
    {
        $this->editId = null;
        $this->resetSectionFields();
        $this->openSection = true;
    }

    public function onSubject(): void
    {
        $this->editId = null;
        $this->resetSubjectFields();
        $this->openSubject = true;
    }

    // ─── Save Standard ────────────────────────────────────────────────────────

    public function saveStandard(): void
    {
        $orgId = Auth::user()->organization_id;

        $this->validate([
            'standardName'  => 'required|string|max:255|unique:standards,name,' . $this->editId . ',id,organization_id,' . $orgId,
            'standardCode'  => 'required|string|max:50|unique:standards,code,' . $this->editId . ',id,organization_id,' . $orgId,
            'standardBoard' => 'required|string',
        ]);

        $data = [
            'name'            => $this->standardName,
            'code'            => $this->standardCode,
            'board'           => $this->standardBoard,
            'order'           => $this->standardOrder ? (int) $this->standardOrder : 0,
            'is_active'       => $this->standardActive,
            'organization_id' => $orgId,
        ];

        if ($this->editId) {
            StudentStandard::find($this->editId)->update($data);
            $this->notification()->success('Class updated successfully!');
        } else {
            StudentStandard::create($data);
            $this->notification()->success('Class created successfully!');
        }

        $this->closeModal();
        $this->mount();
    }

    // ─── Save Section ─────────────────────────────────────────────────────────

    public function saveSection(): void
    {
        $this->validate([
            'sectionName'     => 'required|string|max:255|unique:sections,name,' . $this->editId . ',id,standard_id,' . $this->selectedStandard,
            'selectedStandard' => 'required|exists:standards,id',
        ]);

        $data = [
            'name'            => $this->sectionName,
            'standard_id'     => $this->selectedStandard,
            'is_active'       => $this->sectionActive,
            'organization_id' => Auth::user()->organization_id,
        ];

        if ($this->editId) {
            Section::find($this->editId)->update($data);
            $this->notification()->success('Section updated successfully!');
        } else {
            Section::create($data);
            $this->notification()->success('Section created successfully!');
        }

        $this->closeModal();
        $this->activeTab = 'section';
        $this->mount();
    }

    // ─── Save Subject ─────────────────────────────────────────────────────────

    public function saveSubject(): void
    {
        $this->validate([
            'subjectName'                  => 'required|string|max:255',
            'subjectCode'                  => 'required|string|max:50',
            'selectedStandardForSubject'   => 'required|exists:standards,id',
            'selectedSectionsForSubject'   => 'required|array|min:1',
            'selectedSectionsForSubject.*' => 'exists:sections,id',
            'subjectImage'                 => 'nullable|image|max:2048',
            'subjectDetailImage'           => 'nullable|image|max:2048',
        ], [
            'selectedSectionsForSubject.required' => 'Please select at least one section.',
            'selectedSectionsForSubject.min'      => 'Please select at least one section.',
        ]);

        // Duplicate name check within standard
        $dupName = StandardSubject::where('standard_id', $this->selectedStandardForSubject)
            ->whereHas('subject', fn($q) => $q->where('name', $this->subjectName)
                ->when($this->editId, fn($q) => $q->where('id', '!=', $this->editId)))
            ->exists();

        if ($dupName) {
            $this->addError('subjectName', 'A subject with this name already exists in the selected class.');
            return;
        }

        // Duplicate code check within standard
        $dupCode = StandardSubject::where('standard_id', $this->selectedStandardForSubject)
            ->whereHas('subject', fn($q) => $q->where('code', $this->subjectCode)
                ->when($this->editId, fn($q) => $q->where('id', '!=', $this->editId)))
            ->exists();

        if ($dupCode) {
            $this->addError('subjectCode', 'A subject with this code already exists in the selected class.');
            return;
        }

        $subjectData = [
            'name'            => $this->subjectName,
            'code'            => $this->subjectCode,
            'description'     => $this->subjectDescription,
            'organization_id' => Auth::user()->organization_id,
            'is_active'       => $this->subjectActive,
        ];

        // Image upload
        if ($this->subjectImage) {
            if ($this->editId) {
                $old = Subject::find($this->editId)?->image;
                if ($old) Storage::disk('s3')->delete(parse_url($old, PHP_URL_PATH));
            }
            $path = $this->subjectImage->store('admin/subjects/images', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $subjectData['image'] = Storage::disk('s3')->url($path);
        } elseif ($this->subjectImageUrl) {
            $subjectData['image'] = $this->subjectImageUrl;
        }

        // Detail image upload
        if ($this->subjectDetailImage) {
            if ($this->editId) {
                $old = Subject::find($this->editId)?->detail_image;
                if ($old) Storage::disk('s3')->delete(parse_url($old, PHP_URL_PATH));
            }
            $path = $this->subjectDetailImage->store('admin/subjects/detail-images', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $subjectData['detail_image'] = Storage::disk('s3')->url($path);
        } elseif ($this->subjectDetailImageUrl) {
            $subjectData['detail_image'] = $this->subjectDetailImageUrl;
        }

        try {
            if ($this->editId) {
                $subject = Subject::find($this->editId);
                $subject->update($subjectData);

                StandardSubject::updateOrCreate(
                    ['standard_id' => $this->selectedStandardForSubject, 'subject_id' => $subject->id],
                    ['organization_id' => Auth::user()->organization_id, 'is_mandatory' => $this->isMandatory]
                );

                SectionSubject::where('subject_id', $subject->id)
                    ->where('standard_id', $this->selectedStandardForSubject)->delete();

                foreach ($this->selectedSectionsForSubject as $sectionId) {
                    SectionSubject::create([
                        'section_id'      => $sectionId,
                        'subject_id'      => $subject->id,
                        'standard_id'     => $this->selectedStandardForSubject,
                        'organization_id' => Auth::user()->organization_id,
                    ]);
                }
                $this->notification()->success('Subject updated successfully!');
            } else {
                $subject = Subject::create($subjectData);

                StandardSubject::create([
                    'standard_id'     => $this->selectedStandardForSubject,
                    'subject_id'      => $subject->id,
                    'organization_id' => Auth::user()->organization_id,
                    'is_mandatory'    => $this->isMandatory,
                ]);

                foreach ($this->selectedSectionsForSubject as $sectionId) {
                    SectionSubject::create([
                        'section_id'      => $sectionId,
                        'subject_id'      => $subject->id,
                        'standard_id'     => $this->selectedStandardForSubject,
                        'organization_id' => Auth::user()->organization_id,
                    ]);
                }
                $this->notification()->success('Subject created successfully!');
            }

            $this->closeModal();
            $this->activeTab = 'subject';
            $this->mount();
        } catch (\Exception $e) {
            $this->notification()->error('Error!', 'Failed to save subject: ' . $e->getMessage());
        }
    }

    public function editStandard(int $id): void
    {
        $s = StudentStandard::find($id);
        if ($s) {
            $this->editId = $id;
            $this->standardName   = $s->name;
            $this->standardCode   = $s->code;
            $this->standardBoard  = $s->board;
            $this->standardOrder  = $s->order;
            $this->standardActive = $s->is_active;
            $this->openStandard   = true;
        }
    }

    public function editSection(int $id): void
    {
        $s = Section::find($id);
        if ($s) {
            $this->editId           = $id;
            $this->sectionName        = $s->name;
            $this->sectionDescription = $s->description;
            $this->selectedStandard   = $s->standard_id;
            $this->sectionActive      = $s->is_active;
            $this->openSection        = true;
        }
    }

    public function editSubject(int $id): void
    {
        $subject = Subject::with(['standards', 'sections'])->find($id);
        if (!$subject) return;

        $this->editId               = $id;
        $this->subjectName          = $subject->name;
        $this->subjectCode          = $subject->code;
        $this->subjectDescription   = $subject->description;
        $this->subjectActive        = $subject->is_active;
        $this->subjectImageUrl      = $subject->image;
        $this->subjectDetailImageUrl = $subject->detail_image;
        $this->subjectImagePreview  = $subject->image;
        $this->subjectDetailImagePreview = $subject->detail_image;

        $ss = StandardSubject::where('subject_id', $id)->first();
        if ($ss) {
            $this->selectedStandardForSubject = $ss->standard_id;
            $this->isMandatory                = $ss->is_mandatory;
            $this->loadSectionsForSelectedStandard();
            $this->selectedSectionsForSubject = SectionSubject::where('subject_id', $id)
                ->where('standard_id', $ss->standard_id)->pluck('section_id')->toArray();
            $this->loadExistingSubjectsForStandard();
        }
        $this->openSubject = true;
    }

    public function onEditStandard(int $id): void  { $this->editStandard($id); }
    public function onEditSection(int $id): void   { $this->editSection($id); }
    public function onEditSubject(int $id): void   { $this->editSubject($id); }

    public function onDeleteStandard(int $id): void
    {
        $this->dialog()->confirm([
            'title'       => 'Delete Class?',
            'icon'        => 'exclamation-circle',
            'iconColor'   => 'text-red-500',
            'description' => 'Are you sure? This action cannot be undone.',
            'accept'      => ['label' => 'Yes, delete it', 'method' => 'performDeleteStandard', 'params' => $id, 'color' => 'negative', 'size' => 'md'],
            'reject'      => ['label' => 'No', 'size' => 'md'],
        ]);
    }

    public function onDeleteSection(int $id): void
    {
        $this->dialog()->confirm([
            'title'       => 'Delete Section?',
            'icon'        => 'exclamation-circle',
            'iconColor'   => 'text-red-500',
            'description' => 'Are you sure? This action cannot be undone.',
            'accept'      => ['label' => 'Yes, delete it', 'method' => 'performDeleteSection', 'params' => $id, 'color' => 'negative', 'size' => 'md'],
            'reject'      => ['label' => 'No', 'size' => 'md'],
        ]);
    }

    public function onDeleteSubject(int $id): void
    {
        $this->dialog()->confirm([
            'title'       => 'Delete Subject?',
            'icon'        => 'exclamation-circle',
            'iconColor'   => 'text-red-500',
            'description' => 'Are you sure? This action cannot be undone.',
            'accept'      => ['label' => 'Yes, delete it', 'method' => 'performDeleteSubject', 'params' => $id, 'color' => 'negative', 'size' => 'md'],
            'reject'      => ['label' => 'No', 'size' => 'md'],
        ]);
    }

    public function performDeleteStandard(int $id): void
    {
        $standard = StudentStandard::find($id);
        if (!$standard) return;

        if (Section::where('standard_id', $id)->exists()
            || StandardSubject::where('standard_id', $id)->exists()
            || \App\Models\Admin\TeacherTimeTable::where('standard_id', $id)->exists()) {
            $this->notification()->warning('Cannot Delete!', 'This class has sections, subjects, or timetable entries. Remove them first.');
            return;
        }

        $standard->delete();
        $this->notification()->success('Class deleted successfully!');
        $this->mount();
        $this->dispatch('onStandardAddUpdate');
    }

    public function performDeleteSection(int $id): void
    {
        $section = Section::find($id);
        if (!$section) return;

        if (\App\Models\Student\StudentDetail::where('section_id', $id)->exists()
            || \App\Models\Admin\TeacherTimeTable::where('section_id', $id)->exists()) {
            $this->notification()->warning('Cannot Delete!', 'This section has students or timetable entries. Remove them first.');
            return;
        }

        // Delete subject assignments for this section only
        SectionSubject::where('section_id', $id)->delete();

        $section->delete();
        $this->notification()->success('Section deleted successfully!');
        $this->mount();
        $this->dispatch('onStandardAddUpdate');
    }

    public function performDeleteSubject(int $id): void
    {
        try {
            if (\App\Models\Admin\TeacherTimeTable::where('subject_id', $id)->exists()
                || TeacherAssignment::where('subject_id', $id)->exists()) {
                $this->notification()->warning('Cannot Delete!', 'This subject is used in timetable or assignments.');
                return;
            }

            StandardSubject::where('subject_id', $id)->delete();
            SectionSubject::where('subject_id', $id)->delete();

            $subject = Subject::find($id);
            if ($subject) {
                if ($subject->image)        Storage::disk('s3')->delete(str_replace(Storage::disk('s3')->url(''), '', $subject->image));
                if ($subject->detail_image) Storage::disk('s3')->delete(str_replace(Storage::disk('s3')->url(''), '', $subject->detail_image));
                $subject->delete();
            }

            $this->notification()->success('Subject deleted successfully!');
            $this->mount();
        } catch (\Exception $e) {
            $this->notification()->error('Failed to delete subject: ' . $e->getMessage());
        }
    }

    public function onViewStandardAdmin(int $id): void
    {
        $s = StudentStandard::withCount(['sections', 'subjects'])->find($id);
        if (!$s) { $this->notification()->error('Class not found!'); return; }

        $this->viewModalTitle = 'Class Details';
        $this->viewData = [
            'name'           => $s->name,
            'code'           => $s->code,
            'board'          => $s->board,
            'order'          => $s->order,
            'is_active'      => $s->is_active ? 'Active' : 'Inactive',
            'sections_count' => $s->sections_count,
            'subjects_count' => $s->subjects_count,
            'created_at'     => $s->created_at->format('d M Y, h:i A'),
        ];
        $this->showViewModal = true;
    }

    public function onViewSectionAdmin(int $id): void
    {
        $s = Section::with(['standard', 'subjects'])->find($id);
        if (!$s) { $this->notification()->error('Section not found!'); return; }

        $this->viewModalTitle = 'Section Details';
        $this->viewData = [
            'name'           => $s->name,
            'description'    => $s->description,
            'class'          => $s->standard->name,
            'is_active'      => $s->is_active ? 'Active' : 'Inactive',
            'subjects_count' => $s->subjects->count(),
            'created_at'     => $s->created_at->format('d M Y, h:i A'),
        ];
        $this->showViewModal = true;
    }

    public function onViewSubjectAdmin(int $id): void
    {
        $s = Subject::with(['standards', 'sections'])->find($id);
        if (!$s) { $this->notification()->error('Subject not found!'); return; }

        $standard = $s->standards->first();
        $this->viewModalTitle = 'Subject Details';
        $this->viewData = [
            'name'         => $s->name,
            'code'         => $s->code,
            'description'  => $s->description,
            'is_active'    => $s->is_active ? 'Active' : 'Inactive',
            'image'        => $s->image,
            'detail_image' => $s->detail_image,
            'class'        => $standard?->name ?? 'Not assigned',
            'is_mandatory' => $standard ? ($standard->pivot?->is_mandatory ? 'Yes' : 'No') : 'N/A',
            'sections'     => $s->sections->pluck('name')->implode(', '),
            'created_at'   => $s->created_at->format('d M Y, h:i A'),
        ];
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal  = false;
        $this->viewData       = [];
        $this->viewModalTitle = '';
    }

    public function render()
    {
        return view('livewire.admin.standard', [
            'filteredStandards' => $this->filteredStandards,
            'filteredSections'  => $this->filteredSections,
            'filteredSubjects'  => $this->filteredSubjects,
            'boards'            => $this->boards,
            'allStandards'      => $this->standards,
            'availableSections' => $this->availableSections,
        ]);
    }
}