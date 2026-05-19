<?php

namespace App\Livewire\Admin;

use App\Models\Admin\ContactAdminStudent;
use App\Models\Admin\ContactAdminTeacher;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Enqueries extends Component
{
    use WithPagination, WireUiActions;

    // ─── Tabs ────────────────────────────────────────────────────────────────
    public string $activeTab = 'teacher'; // 'teacher' | 'student'

    // ─── State ──────────────────────────────────────────────────────────────
    public $selectedEnquiry  = null;
    public $showDetailModal  = false;
    public $showReplyModal   = false;

    public $adminReply       = '';

    // ─── Filters ────────────────────────────────────────────────────────────
    public $filterDays    = null;
    public $search        = '';
    public $statusFilter  = '';

    // ─── Custom delete overlay ──────────────────────────────────────────────
    public bool $showDeleteConfirm = false;
    public $deleteTargetId         = null;

    // ─── Stats ──────────────────────────────────────────────────────────────
    public int $totalTeacher  = 0;
    public int $totalStudent  = 0;
    public int $pendingCount  = 0;
    public int $repliedCount  = 0;

    protected $queryString = [
        'activeTab'    => ['except' => 'teacher'],
        'filterDays'   => ['except' => ''],
        'search'       => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    /**
     * Read the query body off an enquiry record regardless of model
     * (teacher_query for ContactAdminTeacher, student_query for ContactAdminStudent).
     */
    public function getQueryText($enquiry): string
    {
        if (!$enquiry) return '';
        return (string) ($enquiry->teacher_query ?? $enquiry->student_query ?? '');
    }

    public function mount(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        if (!Auth::check() || !Auth::user()->organization_id) {
            return;
        }

        $orgId = Auth::user()->organization_id;

        $this->totalTeacher = ContactAdminTeacher::where('organization_id', $orgId)->count();
        $this->totalStudent = ContactAdminStudent::where('organization_id', $orgId)->count();

        if ($this->activeTab === 'teacher') {
            $this->pendingCount = ContactAdminTeacher::where('organization_id', $orgId)->whereNull('admin_reply')->count();
            $this->repliedCount = ContactAdminTeacher::where('organization_id', $orgId)->whereNotNull('admin_reply')->count();
        } else {
            $this->pendingCount = ContactAdminStudent::where('organization_id', $orgId)->whereNull('admin_reply')->count();
            $this->repliedCount = ContactAdminStudent::where('organization_id', $orgId)->whereNotNull('admin_reply')->count();
        }
    }

    public function render()
    {
        $enquiries = $this->getEnquiries();
        return view('livewire.admin.enqueries', compact('enquiries'));
    }

    public function showTab($tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->loadStats();
    }

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); $this->loadStats(); }
    public function updatedFilterDays(): void   { $this->resetPage(); }

    // ─── Queries ────────────────────────────────────────────────────────────

    private function getEnquiries()
    {
        return $this->activeTab === 'student'
            ? $this->getStudentEnquiries()
            : $this->getTeacherEnquiries();
    }

    private function getTeacherEnquiries()
    {
        // Select only columns the blade reads — avoid eager-loading heavy
        // relations (teacherDetail has nested assignedSubjects/sections/
        // classes which would N+1 expand needlessly).
        return ContactAdminTeacher::where('organization_id', Auth::user()->organization_id)
            ->with([
                'user:id,name,email',
                'organization:id,name',
            ])
            ->when($this->filterDays, fn($q) => $q->where('created_at', '>=', Carbon::now()->subDays((int) $this->filterDays)))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('topic', 'like', '%' . $this->search . '%')
                        ->orWhere('teacher_query', 'like', '%' . $this->search . '%')
                        ->orWhere('admin_reply', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'replied') {
                    $query->whereNotNull('admin_reply');
                } elseif ($this->statusFilter === 'pending') {
                    $query->whereNull('admin_reply');
                }
            })
            ->latest()
            ->paginate(10);
    }

    private function getStudentEnquiries()
    {
        return ContactAdminStudent::where('organization_id', Auth::user()->organization_id)
            ->with([
                'user:id,name,email',
                'organization:id,name',
            ])
            ->when($this->filterDays, fn($q) => $q->where('created_at', '>=', Carbon::now()->subDays((int) $this->filterDays)))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('topic', 'like', '%' . $this->search . '%')
                        ->orWhere('student_query', 'like', '%' . $this->search . '%')
                        ->orWhere('admin_reply', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'replied') {
                    $query->whereNotNull('admin_reply');
                } elseif ($this->statusFilter === 'pending') {
                    $query->whereNull('admin_reply');
                }
            })
            ->latest()
            ->paginate(10);
    }

    // ─── View / Reply ───────────────────────────────────────────────────────

    public function viewEnquiry($id): void
    {
        $this->selectedEnquiry = $this->activeTab === 'teacher'
            ? ContactAdminTeacher::where('organization_id', Auth::user()->organization_id)
                ->with(['user:id,name,email', 'organization:id,name'])->findOrFail($id)
            : ContactAdminStudent::where('organization_id', Auth::user()->organization_id)
                ->with(['user:id,name,email', 'organization:id,name'])->findOrFail($id);

        $this->showDetailModal = true;
    }

    public function openReplyModal($id): void
    {
        $this->viewEnquiry($id);
        $this->showDetailModal = false;
        $this->adminReply      = $this->selectedEnquiry->admin_reply ?? '';
        $this->showReplyModal  = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedEnquiry = null;
    }

    public function closeReplyModal(): void
    {
        $this->showReplyModal  = false;
        $this->selectedEnquiry = null;
        $this->adminReply      = '';
    }

    public function sendReply(): void
    {
        $this->validate([
            'adminReply' => 'required|string|min:5',
        ]);

        if ($this->selectedEnquiry) {
            $this->selectedEnquiry->update([
                'admin_reply' => $this->adminReply,
                'admin_text'  => 'Replied by Admin',
            ]);

            $this->closeReplyModal();
            $this->loadStats();
            $this->notification()->success('Reply Sent', 'Your reply has been sent successfully.');
        }
    }

    public function applyFilterDays($days): void
    {
        $this->filterDays = $this->filterDays == $days ? null : $days;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['filterDays', 'search', 'statusFilter']);
        $this->resetPage();
    }

    // ─── Delete (custom overlay) ────────────────────────────────────────────

    public function deleteEnquiry($id): void
    {
        $this->deleteTargetId    = $id;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteTargetId    = null;
    }

    public function confirmDelete(): void
    {
        $modelClass = $this->activeTab === 'teacher' ? ContactAdminTeacher::class : ContactAdminStudent::class;
        $enquiry    = $modelClass::find($this->deleteTargetId);

        if ($enquiry) {
            if (!empty($enquiry->image)) {
                Storage::disk('s3')->delete(parse_url($enquiry->image, PHP_URL_PATH));
            }
            $enquiry->delete();

            $this->notification()->success('Enquiry Deleted', 'The enquiry has been deleted successfully.');
            $this->loadStats();

            if ($this->selectedEnquiry && $this->selectedEnquiry->id == $this->deleteTargetId) {
                $this->closeDetailModal();
            }
        } else {
            $this->notification()->error('Error', 'Enquiry not found.');
        }

        $this->showDeleteConfirm = false;
        $this->deleteTargetId    = null;
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    public function getQueryField(): string
    {
        return $this->activeTab === 'teacher' ? 'teacher_query' : 'student_query';
    }
}
