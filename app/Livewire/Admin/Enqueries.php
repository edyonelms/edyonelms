<?php

namespace App\Livewire\Admin;

use App\Models\Admin\AdminEnquiry;
use App\Models\Admin\ContactAdminStudent;
use App\Models\Admin\ContactAdminTeacher;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Organization;
use Carbon\Carbon;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Enqueries extends Component
{
    use WithPagination, WireUiActions;

    public $activeTab = 'teacher';
    public $selectedEnquiry = null;
    public $showDetailModal = false;
    public $showReplyModal = false;
    public $filterDays = null;
    public $filterMonths = null;
    public $search = '';
    public $statusFilter = '';
    public $adminReply = '';

    protected $queryString = [
        'filterDays' => ['except' => ''],
        'filterMonths' => ['except' => ''],
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function render()
    {
        // Get enquiries based on active tab
        $enquiries = $this->getEnquiries();

        return view('livewire.admin.enqueries', compact('enquiries'));
    }

    public function showTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    private function getEnquiries()
    {
        switch ($this->activeTab) {
            case 'teacher':
                return $this->getTeacherEnquiries();
            case 'student':
                return $this->getStudentEnquiries();
            case 'website':
                return $this->getWebsiteEnquiries();
            default:
                return collect();
        }
    }

    private function getTeacherEnquiries()
    {
        return ContactAdminTeacher::where('organization_id', Auth::user()->organization_id)
            ->with(['user', 'organization', 'teacherDetail'])
            ->when($this->filterDays, function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays($this->filterDays));
            })
            ->when($this->filterMonths, function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonths($this->filterMonths));
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('topic', 'like', '%' . $this->search . '%')
                        ->orWhere('teacher_query', 'like', '%' . $this->search . '%')
                        ->orWhere('admin_text', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('organization', function ($orgQuery) {
                            $orgQuery->where('name', 'like', '%' . $this->search . '%');
                        });
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
            ->with(['user', 'organization', 'studentDetail'])
            ->when($this->filterDays, function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays($this->filterDays));
            })
            ->when($this->filterMonths, function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonths($this->filterMonths));
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('topic', 'like', '%' . $this->search . '%')
                        ->orWhere('student_query', 'like', '%' . $this->search . '%')
                        ->orWhere('admin_text', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('organization', function ($orgQuery) {
                            $orgQuery->where('name', 'like', '%' . $this->search . '%');
                        });
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

    private function getWebsiteEnquiries()
    {
        return AdminEnquiry::where('organization_id', Auth::user()->organization_id)
            ->with(['organization'])
            ->when($this->filterDays, function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays($this->filterDays));
            })
            ->when($this->filterMonths, function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonths($this->filterMonths));
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('mobile_number', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('type', 'like', '%' . $this->search . '%')
                        ->orWhereHas('organization', function ($orgQuery) {
                            $orgQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                // Website enquiries don't have reply status, so we'll handle differently
                if ($this->statusFilter === 'replied') {
                    $query->whereNotNull('admin_reply'); // If you add this field later
                } elseif ($this->statusFilter === 'pending') {
                    $query->whereNull('admin_reply'); // If you add this field later
                }
            })
            ->latest()
            ->paginate(10);
    }

    public function viewEnquiry($id)
    {
        switch ($this->activeTab) {
            case 'teacher':
                $this->selectedEnquiry = ContactAdminTeacher::where('organization_id', Auth::user()->organization_id)->with(['user', 'organization', 'teacherDetail'])->findOrFail($id);
                break;
            case 'student':
                $this->selectedEnquiry = ContactAdminStudent::where('organization_id', Auth::user()->organization_id)->with(['user', 'organization', 'studentDetail'])->findOrFail($id);
                break;
            case 'website':
                $this->selectedEnquiry = AdminEnquiry::where('organization_id', Auth::user()->organization_id)->with(['organization'])->findOrFail($id);
                break;
        }
        $this->showDetailModal = true;
    }

    public function openReplyModal($id)
    {
        switch ($this->activeTab) {
            case 'teacher':
                $this->selectedEnquiry = ContactAdminTeacher::where('organization_id', Auth::user()->organization_id)->with(['user', 'organization', 'teacherDetail'])->findOrFail($id);
                $this->adminReply = $this->selectedEnquiry->admin_reply ?? '';
                break;
            case 'student':
                $this->selectedEnquiry = ContactAdminStudent::where('organization_id', Auth::user()->organization_id)->with(['user', 'organization', 'studentDetail'])->findOrFail($id);
                $this->adminReply = $this->selectedEnquiry->admin_reply ?? '';
                break;
            case 'website':
                // Website enquiries typically don't have replies in the same way
                $this->selectedEnquiry = AdminEnquiry::where('organization_id', Auth::user()->organization_id)->with(['organization'])->findOrFail($id);
                $this->adminReply = $this->selectedEnquiry->admin_reply ?? '';
                break;
        }
        $this->showReplyModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedEnquiry = null;
    }

    public function closeReplyModal()
    {
        $this->showReplyModal = false;
        $this->selectedEnquiry = null;
        $this->adminReply = '';
    }

    public function sendReply()
    {
        $this->validate([
            'adminReply' => 'required|string|min:10',
        ]);

        if ($this->selectedEnquiry) {
            switch ($this->activeTab) {
                case 'teacher':
                    $this->selectedEnquiry->update([
                        'admin_reply' => $this->adminReply,
                        'admin_text' => 'Replied by Admin',
                    ]);
                    break;
                case 'student':
                    $this->selectedEnquiry->update([
                        'admin_reply' => $this->adminReply,
                        'admin_text' => 'Replied by Admin',
                    ]);
                    break;
                case 'website':
                    // If you want to add reply functionality for website enquiries
                    $this->selectedEnquiry->update([
                        'admin_reply' => $this->adminReply,
                    ]);
                    break;
            }

            $this->closeReplyModal();

            $this->notification()->success(
                $title = 'Reply Sent',
                $description = 'Your reply has been sent successfully.'
            );
        }
    }

    public function applyFilter($type, $value)
    {
        if ($type === 'days') {
            $this->filterDays = $value;
            $this->filterMonths = null;
        } elseif ($type === 'months') {
            $this->filterMonths = $value;
            $this->filterDays = null;
        }

        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->filterDays = null;
        $this->filterMonths = null;
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function deleteEnquiry($id)
    {
        $this->dialog()->confirm([
            'title' => 'Delete Enquiry?',
            'icon' => 'exclamation-circle',
            'iconColor' => 'text-red-500',
            'description' => 'Are you sure you want to delete this enquiry? This action cannot be undone.',
            'accept' => [
                'label' => 'Yes, delete it',
                'method' => 'doDelete',
                'params' => $id,
                'color' => 'negative',
                'size' => 'md',
            ],
            'reject' => [
                'label' => 'Cancel',
                'size' => 'md',
            ],
        ]);
    }

    public function doDelete($id)
    {
        switch ($this->activeTab) {
            case 'teacher':
                $enquiry = ContactAdminTeacher::find($id);
                break;
            case 'student':
                $enquiry = ContactAdminStudent::find($id);
                break;
            case 'website':
                $enquiry = AdminEnquiry::find($id);
                break;
            default:
                $enquiry = null;
        }

        if ($enquiry) {
            // Delete associated image if exists
            if (isset($enquiry->image) && $enquiry->image) {
                $imagePath = parse_url($enquiry->image, PHP_URL_PATH);
                Storage::disk('s3')->delete($imagePath);
            }

            $enquiry->delete();

            $this->notification()->success(
                $title = 'Enquiry Deleted',
                $description = 'The enquiry has been deleted successfully.'
            );
        } else {
            $this->notification()->error(
                $title = 'Error',
                $description = 'Enquiry not found.'
            );
        }

        if ($this->selectedEnquiry && $this->selectedEnquiry->id == $id) {
            $this->closeDetailModal();
        }
    }

    // Method to get tab title
    public function getTabTitle($tab)
    {
        return match ($tab) {
            'teacher' => 'Teacher Enquiries',
            'student' => 'Student Enquiries',
            'website' => 'Website Enquiries',
            default => 'Enquiries'
        };
    }

    // Method to check if tab has reply functionality
    public function hasReplyFunctionality($tab)
    {
        return in_array($tab, ['teacher', 'student']);
    }
}
